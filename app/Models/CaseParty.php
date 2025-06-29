<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseParty extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'name',
        'role',
        'party_type',
        'contact_info',
        'address',
        'phone',
        'email',
    ];

    protected $casts = [
        'contact_info' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'party_type' => 'individual',
    ];

    /**
     * Get the case that owns the party.
     */
    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    /**
     * Scope a query to only include parties with a given role.
     */
    public function scopeWithRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope a query to only include plaintiffs.
     */
    public function scopePlaintiffs($query)
    {
        return $query->where('role', 'plaintiff');
    }

    /**
     * Scope a query to only include defendants.
     */
    public function scopeDefendants($query)
    {
        return $query->where('role', 'defendant');
    }

    /**
     * Scope a query to only include attorneys.
     */
    public function scopeAttorneys($query)
    {
        return $query->where('role', 'attorney');
    }

    /**
     * Scope a query to only include individuals.
     */
    public function scopeIndividuals($query)
    {
        return $query->where('party_type', 'individual');
    }

    /**
     * Scope a query to only include organizations.
     */
    public function scopeOrganizations($query)
    {
        return $query->where('party_type', 'organization');
    }

    /**
     * Get the formatted contact information.
     */
    public function getFormattedContactAttribute()
    {
        $contact = [];
        
        if ($this->email) {
            $contact[] = $this->email;
        }
        
        if ($this->phone) {
            $contact[] = $this->phone;
        }
        
        return implode(' | ', $contact);
    }

    /**
     * Get the role display name with proper capitalization.
     */
    public function getRoleDisplayAttribute()
    {
        return ucfirst($this->role);
    }

    /**
     * Get the party type display name with proper capitalization.
     */
    public function getPartyTypeDisplayAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->party_type));
    }
}
