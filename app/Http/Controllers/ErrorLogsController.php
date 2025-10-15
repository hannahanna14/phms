<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class ErrorLogsController extends Controller
{
    /**
     * Display the error logs page
     */
    public function index(Request $request)
    {
        // Only admins can access error logs
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can view error logs.');
        }

        // Create a test activity log entry if none exist
        if (Activity::count() === 0) {
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'action' => 'test',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ])
                ->log('Accessed Error Logs page for the first time');
        }

        $logType = $request->get('type', 'activity');
        $perPage = (int) $request->get('per_page', 50);
        
        $data = [];
        
        switch ($logType) {
            case 'activity':
                $data = $this->getActivityLogs($request, $perPage);
                break;
            case 'laravel':
                $data = $this->getLaravelLogs($request, $perPage);
                break;
            default:
                $data = $this->getActivityLogs($request, $perPage);
        }

        return Inertia::render('ErrorLogs/Index', [
            'logs' => $data,
            'logType' => $logType,
            'filters' => $request->only(['search', 'date_from', 'date_to', 'level'])
        ]);
    }

    /**
     * Get activity logs
     */
    private function getActivityLogs(Request $request, $perPage)
    {
        $query = Activity::with('causer', 'subject')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('event', 'like', "%{$search}%")
                  ->orWhere('subject_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->get('date_to') . ' 23:59:59');
        }

        $result = $query->paginate($perPage);
        
        // Ensure the data structure is correct for the frontend
        return [
            'data' => $result->items(),
            'total' => $result->total(),
            'per_page' => $result->perPage(),
            'current_page' => $result->currentPage(),
            'last_page' => $result->lastPage()
        ];
    }

    /**
     * Get Laravel error logs
     */
    private function getLaravelLogs(Request $request, $perPage)
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logPath)) {
            $content = File::get($logPath);
            $lines = explode("\n", $content);
            
            // Parse log entries (simplified)
            $currentEntry = null;
            foreach (array_reverse($lines) as $line) {
                if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \w+\.(\w+): (.+)/', $line, $matches)) {
                    if ($currentEntry) {
                        $logs[] = $currentEntry;
                    }
                    $currentEntry = [
                        'timestamp' => $matches[1],
                        'level' => strtoupper($matches[2]),
                        'message' => $matches[3],
                        'full_content' => $line
                    ];
                } elseif ($currentEntry && trim($line)) {
                    $currentEntry['full_content'] .= "\n" . $line;
                }
            }
            
            if ($currentEntry) {
                $logs[] = $currentEntry;
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = strtolower($request->get('search'));
            $logs = array_filter($logs, function($log) use ($search) {
                return strpos(strtolower($log['message']), $search) !== false ||
                       strpos(strtolower($log['level']), $search) !== false;
            });
        }

        if ($request->filled('level')) {
            $level = strtoupper($request->get('level'));
            $logs = array_filter($logs, function($log) use ($level) {
                return $log['level'] === $level;
            });
        }

        // Simple pagination
        $total = count($logs);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        $logs = array_slice($logs, $offset, $perPage);

        return [
            'data' => $logs,
            'total' => $total,
            'per_page' => (int) $perPage,
            'current_page' => (int) $page,
            'last_page' => (int) ceil($total / $perPage)
        ];
    }


    /**
     * Clear logs
     */
    public function clearLogs(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can clear logs.');
        }

        $logType = $request->get('type', 'activity');
        
        switch ($logType) {
            case 'activity':
                Activity::truncate();
                break;
            case 'laravel':
                $logPath = storage_path('logs/laravel.log');
                if (File::exists($logPath)) {
                    File::put($logPath, '');
                }
                break;
        }

        return response()->json(['message' => 'Logs cleared successfully']);
    }

    /**
     * Download logs
     */
    public function downloadLogs(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can download logs.');
        }

        $logType = $request->get('type', 'activity');
        $filename = $logType . '-logs-' . date('Y-m-d-H-i-s') . '.txt';

        switch ($logType) {
            case 'activity':
                $logs = Activity::with('causer', 'subject')
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                $content = "Activity Logs Export - " . date('Y-m-d H:i:s') . "\n";
                $content .= str_repeat("=", 50) . "\n\n";
                
                foreach ($logs as $log) {
                    $content .= "[{$log->created_at}] {$log->event}: {$log->description}\n";
                    $content .= "User: " . ($log->causer ? $log->causer->full_name : 'System') . "\n";
                    $content .= "Subject: {$log->subject_type}\n";
                    if ($log->properties) {
                        $content .= "Properties: " . json_encode($log->properties) . "\n";
                    }
                    $content .= str_repeat("-", 30) . "\n";
                }
                break;
                
            case 'laravel':
                $logPath = storage_path('logs/laravel.log');
                $content = File::exists($logPath) ? File::get($logPath) : 'No Laravel logs found.';
                break;
                
            default:
                $content = 'Invalid log type.';
        }

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
