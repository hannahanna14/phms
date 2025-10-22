<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthTreatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'date',
        'title',
        'chief_complaint',
        'treatment',
        'status',
        'remarks',
        'attended_by',
        'grade_level',
        'school_year',
        'started_at',
        'expires_at',
        'is_expired',
        'timer_status',
    ];

    protected $casts = [
        'date' => 'date',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_expired' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Start the treatment timer (2 hours from now)
     */
    public function startTimer()
    {
        $this->started_at = now();
        $this->expires_at = now()->addHours(2); // Standard 2-hour timer
        $this->is_expired = false;
        $this->timer_status = 'active';
        $this->status = 'in_progress';
        $this->save();
    }

    /**
     * Check if the treatment timer has expired
     */
    public function isExpired()
    {
        if (!$this->expires_at) {
            return false;
        }
        
        $expired = now()->isAfter($this->expires_at);
        
        // Auto-update expired status if needed
        if ($expired && !$this->is_expired) {
            $this->is_expired = true;
            $this->timer_status = 'expired';
            $this->status = 'completed';
            $this->save();
        }
        
        return $expired;
    }

    /**
     * Get remaining time in minutes
     */
    public function getRemainingMinutes()
    {
        if (!$this->expires_at || $this->isExpired()) {
            return 0;
        }
        
        return now()->diffInMinutes($this->expires_at, false);
    }

    /**
     * Get remaining time formatted as string
     */
    public function getRemainingTimeFormatted()
    {
        if (!$this->expires_at) {
            return 'Not started';
        }
        
        if ($this->isExpired()) {
            return 'Expired';
        }
        
        $minutes = $this->getRemainingMinutes();
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0) {
            return sprintf('%dh %dm remaining', $hours, $mins);
        } else {
            return sprintf('%dm remaining', $mins);
        }
    }

    /**
     * Check if treatment can be edited
     */
    public function canEdit()
    {
        // Only nurses can edit treatments (not teachers or admins)
        $user = auth()->user();
        if ($user && $user->role !== 'nurse') {
            return false;
        }
        
        return !$this->isExpired() && $this->status !== 'completed';
    }

    /**
     * Get timer status for display
     */
    public function getTimerStatus()
    {
        // For teachers, show simplified status
        $user = auth()->user();
        if ($user && $user->role === 'teacher') {
            if (!$this->started_at) {
                return [
                    'status' => 'not_started',
                    'display' => 'Not Started',
                    'color' => 'secondary'
                ];
            }
            
            if ($this->isExpired()) {
                return [
                    'status' => 'expired',
                    'display' => 'Completed',
                    'color' => 'success'
                ];
            }
            
            return [
                'status' => 'active',
                'display' => 'In Progress',
                'color' => 'info'
            ];
        }
        
        // For admins, show detailed timer
        if (!$this->started_at) {
            return [
                'status' => 'not_started',
                'display' => 'Not Started',
                'color' => 'secondary'
            ];
        }
        
        if ($this->isExpired()) {
            return [
                'status' => 'expired',
                'display' => 'Expired',
                'color' => 'danger'
            ];
        }
        
        return [
            'status' => 'active',
            'display' => $this->getRemainingTimeFormatted(),
            'color' => 'warning'
        ];
    }

    /**
     * Complete the treatment timer
     */
    public function completeTimer()
    {
        $this->timer_status = 'completed';
        $this->status = 'completed';
        $this->save();
    }

    /**
     * Pause the treatment timer
     */
    public function pauseTimer()
    {
        $this->timer_status = 'paused';
        $this->save();
    }

    /**
     * Resume the treatment timer
     */
    public function resumeTimer()
    {
        $this->timer_status = 'active';
        $this->save();
    }
}
