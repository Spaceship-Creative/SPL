<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CaseDeadline extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'title',
        'description',
        'due_date',
        'reminder_date',
        'status',
        'priority',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'reminder_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'pending',
        'priority' => 'medium',
    ];

    /**
     * Get the case that owns the deadline.
     */
    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    /**
     * Scope a query to only include deadlines with a given status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include pending deadlines.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include completed deadlines.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include overdue deadlines.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                    ->orWhere(function ($q) {
                        $q->where('status', 'pending')
                          ->where('due_date', '<', now());
                    });
    }

    /**
     * Scope a query to only include deadlines with a given priority.
     */
    public function scopeWithPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to only include high priority deadlines.
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'critical']);
    }

    /**
     * Scope a query to only include deadlines due within a given number of days.
     */
    public function scopeDueWithin($query, $days)
    {
        return $query->where('due_date', '<=', now()->addDays($days))
                    ->where('due_date', '>=', now());
    }

    /**
     * Scope a query to only include deadlines needing reminders.
     */
    public function scopeNeedingReminders($query)
    {
        return $query->where('status', 'pending')
                    ->whereNotNull('reminder_date')
                    ->where('reminder_date', '<=', now());
    }

    /**
     * Scope a query to order deadlines by due date.
     */
    public function scopeOrderByDueDate($query, $direction = 'asc')
    {
        return $query->orderBy('due_date', $direction);
    }

    /**
     * Scope a query to order deadlines by priority.
     */
    public function scopeOrderByPriority($query)
    {
        return $query->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low')");
    }

    /**
     * Check if the deadline is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date->isPast();
    }

    /**
     * Check if the deadline is due today.
     */
    public function isDueToday(): bool
    {
        return $this->due_date->isToday();
    }

    /**
     * Check if the deadline is due within the next week.
     */
    public function isDueThisWeek(): bool
    {
        return $this->due_date->isBetween(now(), now()->addWeek());
    }

    /**
     * Get the number of days until the deadline.
     */
    public function getDaysUntilDueAttribute()
    {
        if ($this->due_date->isPast()) {
            return 0;
        }
        
        return now()->diffInDays($this->due_date);
    }

    /**
     * Get the priority display name with proper capitalization.
     */
    public function getPriorityDisplayAttribute()
    {
        return ucfirst($this->priority);
    }

    /**
     * Get the status display name with proper capitalization.
     */
    public function getStatusDisplayAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Get the due date in a human readable format.
     */
    public function getDueDateHumanAttribute()
    {
        return $this->due_date->format('M j, Y \a\t g:i A');
    }

    /**
     * Get the priority color for UI display.
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'critical' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }

    /**
     * Mark the deadline as completed.
     */
    public function markAsCompleted(): bool
    {
        return $this->update(['status' => 'completed']);
    }

    /**
     * Mark the deadline as overdue.
     */
    public function markAsOverdue(): bool
    {
        return $this->update(['status' => 'overdue']);
    }
}
