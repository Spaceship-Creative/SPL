<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class CaseDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'case_id',
        'name',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'category',
        'tags',
        'version',
        'processing_status',
        'ai_processed_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'file_size' => 'integer',
        'version' => 'integer',
        'ai_processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'version' => 1,
        'processing_status' => 'pending',
    ];

    /**
     * Get the case that owns the document.
     */
    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    /**
     * Scope a query to only include documents with a given processing status.
     */
    public function scopeWithProcessingStatus($query, $status)
    {
        return $query->where('processing_status', $status);
    }

    /**
     * Scope a query to only include pending documents.
     */
    public function scopePending($query)
    {
        return $query->where('processing_status', 'pending');
    }

    /**
     * Scope a query to only include completed documents.
     */
    public function scopeCompleted($query)
    {
        return $query->where('processing_status', 'completed');
    }

    /**
     * Scope a query to only include documents of a given category.
     */
    public function scopeOfCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include documents with a specific tag.
     */
    public function scopeWithTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFileSizeHumanAttribute()
    {
        if (!$this->file_size) {
            return 'Unknown size';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the processing status with proper capitalization.
     */
    public function getProcessingStatusDisplayAttribute()
    {
        return ucfirst($this->processing_status);
    }

    /**
     * Check if the document has been processed by AI.
     */
    public function isProcessed(): bool
    {
        return $this->processing_status === 'completed' && $this->ai_processed_at !== null;
    }

    /**
     * Check if the document is currently being processed.
     */
    public function isProcessing(): bool
    {
        return $this->processing_status === 'processing';
    }

    /**
     * Mark the document as processed.
     */
    public function markAsProcessed(): bool
    {
        return $this->update([
            'processing_status' => 'completed',
            'ai_processed_at' => now(),
        ]);
    }

    /**
     * Mark the document as failed processing.
     */
    public function markAsProcessingFailed(): bool
    {
        return $this->update([
            'processing_status' => 'failed',
        ]);
    }

    /**
     * Get the file URL if it exists.
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path && Storage::exists($this->file_path)) {
            return Storage::url($this->file_path);
        }
        
        return null;
    }

    /**
     * Get the file extension from the file name.
     */
    public function getFileExtensionAttribute()
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }
}
