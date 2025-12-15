<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Models\Student;
use App\Models\HealthExamination;
use App\Models\SchoolSettings;

class GenerateHealthReportPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $filters;
    public $userId;
    public $exportId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $filters, $userId, $exportId)
    {
        $this->filters = $filters;
        $this->userId = $userId;
        $this->exportId = $exportId;
        $this->onQueue('exports');
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $exportDir = storage_path('app/exports/' . $this->exportId);
        @mkdir($exportDir, 0755, true);

        try {
            // Rebuild students query based on filters
            $gradeLevel = $this->filters['grade_level'] ?? null;
            $schoolYear = $this->filters['school_year'] ?? null;
            $selectedFields = $this->filters['fields'] ?? ['name', 'lrn', 'grade_level', 'section', 'gender', 'age'];
            $healthExamFields = $this->filters['health_exam_fields'] ?? [];

            $user = \App\Models\User::find($this->userId);

            if (!empty($this->filters['selected_students']) && count($this->filters['selected_students']) > 0) {
                $studentIds = collect($this->filters['selected_students'])->pluck('id')->filter()->toArray();
                if (empty($studentIds)) {
                    $studentIds = $this->filters['selected_students'];
                }

                if ($user && $user->role === 'teacher') {
                    $assigned = $user->assignedStudents()->pluck('student_id');
                    $studentsQuery = Student::whereIn('id', $studentIds)->whereIn('id', $assigned);
                } else {
                    $studentsQuery = Student::whereIn('id', $studentIds);
                }
            } else {
                if ($user && $user->role === 'teacher') {
                    $assigned = $user->assignedStudents()->pluck('student_id');
                    $studentsQuery = Student::whereIn('id', $assigned);
                } else {
                    $studentsQuery = Student::query();
                }

                if ($gradeLevel && $gradeLevel !== 'All') {
                    $studentsQuery->where(function($q) use ($gradeLevel) {
                        $q->where('grade_level', $gradeLevel)
                          ->orWhere('grade_level', 'Grade ' . $gradeLevel);
                    });
                }
                if (!empty($this->filters['section']) && $this->filters['section'] !== 'All') {
                    $studentsQuery->where('section', $this->filters['section']);
                }

                if (!empty($this->filters['gender_filter']) && $this->filters['gender_filter'] !== 'All') {
                    $studentsQuery->where('sex', $this->filters['gender_filter']);
                }

                if (!empty($this->filters['min_age'])) {
                    $studentsQuery->where('age', '>=', $this->filters['min_age']);
                }
                if (!empty($this->filters['max_age'])) {
                    $studentsQuery->where('age', '<=', $this->filters['max_age']);
                }
            }

            // Use chunking so memory footprint stays low
            $chunkSize = 200;
            $chunkIndex = 0;
            $pdfFiles = [];

            $schoolSettings = SchoolSettings::getInstance();

            $studentsQuery->orderBy('id');

            $studentsQuery->chunkById($chunkSize, function($students) use (&$chunkIndex, &$pdfFiles, $selectedFields, $healthExamFields, $gradeLevel, $schoolYear, $schoolSettings, $exportDir) {
                $chunkIndex++;
                $reportData = [];

                foreach ($students as $student) {
                    $studentData = [];
                    if (in_array('name', $selectedFields)) {
                        $studentData['name'] = $this->formatNameForReport($student->full_name);
                    }
                    if (in_array('lrn', $selectedFields)) {
                        $studentData['lrn'] = $student->lrn;
                    }
                    if (in_array('grade_level', $selectedFields)) {
                        $studentData['grade_level'] = $student->grade_level;
                    }
                    if (in_array('section', $selectedFields)) {
                        $studentData['section'] = $student->section;
                    }
                    if (in_array('gender', $selectedFields)) {
                        $studentData['gender'] = $student->sex;
                    }
                    if (in_array('age', $selectedFields)) {
                        $studentData['age'] = $student->age;
                    }

                    if (!empty($healthExamFields)) {
                        $healthExam = HealthExamination::where('student_id', $student->id)->latest()->first();
                        $healthData = [];
                        foreach ($healthExamFields as $field) {
                            if ($healthExam) {
                                $healthData[$field] = $healthExam->$field ?? 'N/A';
                            } else {
                                $healthData[$field] = 'N/A';
                            }
                        }
                        $studentData['health_exam'] = $healthData;
                    }

                    $reportData[] = $studentData;
                }

                // Render PDF for this chunk
                $pdf = PDF::loadView('health-report-pdf', [
                    'reportData' => $reportData,
                    'grade_level' => $gradeLevel,
                    'school_year' => $schoolYear,
                    'section' => $this->filters['section'] ?? null,
                    'fields' => $selectedFields,
                    'health_exam_fields' => $healthExamFields,
                    'selected_students' => [],
                    'user_name' => 'System',
                    'schoolSettings' => $schoolSettings
                ]);

                $filePath = $exportDir . '/chunk-' . $chunkIndex . '.pdf';
                file_put_contents($filePath, $pdf->output());
                $pdfFiles[] = $filePath;
            });

            // Create an archive of generated chunk PDFs. Prefer ZipArchive, fall back to PharData/TAR.
            $zipPath = storage_path('app/exports/' . $this->exportId . '.zip');
            $tarPath = storage_path('app/exports/' . $this->exportId . '.tar');
            $archiveCreated = false;

            if (class_exists('\ZipArchive')) {
                try {
                    $zip = new \ZipArchive();
                    if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
                        foreach (glob($exportDir . '/*.pdf') as $file) {
                            $zip->addFile($file, basename($file));
                        }
                        $zip->close();
                        $archiveCreated = file_exists($zipPath);
                    }
                } catch (\Throwable $e) {
                    Log::warning('ZipArchive failed: ' . $e->getMessage());
                }
            }

            if (!$archiveCreated && class_exists('\PharData')) {
                try {
                    // Create a tar archive as a fallback
                    if (file_exists($tarPath)) @unlink($tarPath);
                    $phar = new \PharData($tarPath);
                    foreach (glob($exportDir . '/*.pdf') as $file) {
                        $phar->addFile($file, basename($file));
                    }
                    // Optionally compress to tar.gz if zlib available
                    if (function_exists('gzopen') && !file_exists($tarPath . '.gz')) {
                        $gzPath = $tarPath . '.gz';
                        $phar->compress(\Phar::GZ, basename($gzPath));
                        // Phar::compress writes to {tar}.gz in same dir; ensure the gz exists
                        if (file_exists($gzPath)) {
                            // use gz path as final archive
                            $tarPath = $gzPath;
                        }
                    }
                    $archiveCreated = file_exists($tarPath) || (isset($gzPath) && file_exists($gzPath));
                } catch (\Throwable $e) {
                    Log::warning('PharData TAR creation failed: ' . $e->getMessage());
                }
            }

            // Cleanup chunk files
            foreach (glob($exportDir . '/*.pdf') as $file) {
                @unlink($file);
            }
            @rmdir($exportDir);

            if ($archiveCreated) {
                Log::info('Export archive created', ['exportId' => $this->exportId]);
            } else {
                throw new \RuntimeException('Failed to create export archive: no supported archiver available');
            }
        } catch (\Throwable $e) {
            Log::error('Queued export failed: ' . $e->getMessage());
            file_put_contents(storage_path('app/exports/' . $this->exportId . '.failed'), $e->getMessage());
        }
    }

    private function formatNameForReport($fullName)
    {
        return $fullName;
    }
}
