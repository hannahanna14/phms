<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'type',
        'status',
        'location',
        'created_by',
        'attendees',
        'selected_users',
        'notes'
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'attendees' => 'array',
        'selected_users' => 'array'
    ];

    /**
     * Get the user who created this schedule
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the users who are attendees of this schedule
     */
    public function attendeeUsers()
    {
        if (!$this->selected_users || !is_array($this->selected_users)) {
            return collect();
        }
        
        return User::whereIn('id', $this->selected_users)->get();
    }

    /**
     * Check if a user can view this schedule
     */
    public function canBeViewedBy($user)
    {
        // Admins and nurses can see all schedules
        if (in_array($user->role, ['admin', 'nurse'])) {
            return true;
        }
        
        // Schedule creator can see their own schedules
        if ($this->created_by === $user->id) {
            return true;
        }
        
        // Check if user is in the attendees list
        if ($this->selected_users && is_array($this->selected_users)) {
            return in_array($user->id, $this->selected_users);
        }
        
        return false;
    }

    /**
     * Get formatted start time
     */
    public function getFormattedStartTimeAttribute()
    {
        return $this->start_datetime->format('M d, Y h:i A');
    }

    /**
     * Get formatted end time
     */
    public function getFormattedEndTimeAttribute()
    {
        return $this->end_datetime->format('M d, Y h:i A');
    }

    /**
     * Get duration in minutes
     */
    public function getDurationAttribute()
    {
        return $this->start_datetime->diffInMinutes($this->end_datetime);
    }

    /**
     * Check if schedule is today
     */
    public function getIsTodayAttribute()
    {
        return $this->start_datetime->isToday();
    }

    /**
     * Check if schedule is upcoming
     */
    public function getIsUpcomingAttribute()
    {
        return $this->start_datetime->isFuture();
    }

    /**
     * Scope for upcoming schedules
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_datetime', '>', now());
    }

    /**
     * Scope for today's schedules
     */
    public function scopeToday($query)
    {
        return $query->whereDate('start_datetime', today());
    }

    /**
     * Scope for schedules by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for schedules by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
