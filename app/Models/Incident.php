<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incident extends Model
{
    protected $fillable = [
        'student_id',
        'date',
        'complaint',
        'actions_taken',
        'status',
        'timer_status',
        'grade_level',
        'school_year',
        'started_at',
        'expires_at',
        'is_expired'
    ];

    protected $casts = [
        'date' => 'date',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_expired' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Start the incident timer (2 hours from now)
     */
    public function startTimer()
    {
        $this->started_at = now();
        $this->expires_at = now()->addHours(2); // Standard 2-hour timer
        $this->is_expired = false;
        $this->timer_status = 'active';
        $this->save();
    }

    /**
     * Check if the incident timer has expired
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
     * Check if incident can be edited
     */
    public function canEdit()
    {
        // Only nurses can edit incidents (not teachers or admins)
        $user = auth()->user();
        if ($user && $user->role !== 'nurse') {
            return false;
        }
        
        return !$this->isExpired() && $this->status !== 'closed';
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
                    'display' => 'Expired',
                    'color' => 'danger'
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
}