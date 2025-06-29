<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalCase extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cases';

    protected $fillable = [
        'user_id',
        'name',
        'case_number',
        'type',
        'jurisdiction',
        'venue',
        'status',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * Get the user that owns the case.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parties for the case.
     */
    public function parties(): HasMany
    {
        return $this->hasMany(CaseParty::class, 'case_id');
    }

    /**
     * Get the documents for the case.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(CaseDocument::class, 'case_id');
    }

    /**
     * Get the deadlines for the case.
     */
    public function deadlines(): HasMany
    {
        return $this->hasMany(CaseDeadline::class, 'case_id');
    }

    /**
     * Scope a query to only include cases of a given type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include cases with a given status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include active cases.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the case number with a fallback to "No case number".
     */
    public function getCaseNumberDisplayAttribute()
    {
        return $this->case_number ?: 'No case number';
    }

    /**
     * Get the full case display name.
     */
    public function getDisplayNameAttribute()
    {
        return $this->case_number ? "{$this->name} ({$this->case_number})" : $this->name;
    }
}
