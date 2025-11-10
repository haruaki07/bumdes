<?php

namespace App\Models;

use App\Enums\BackupStatus;
use App\Enums\BackupType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Backup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'status',
        'file_path',
        'file_size',
        'error_message',
        'started_at',
        'completed_at',
        'created_by',
        'is_manual',
        'metadata',
    ];

    protected $casts = [
        'type' => BackupType::class,
        'status' => BackupStatus::class,
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_manual' => 'boolean',
        'metadata' => 'array',
        'file_size' => 'integer',
    ];

    /**
     * Get the user who created the backup.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the backup file size in human-readable format.
     */
    public function getFileSizeFormattedAttribute(): string
    {
        if (! $this->file_size) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $this->file_size;
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;

        return round($bytes / pow(1024, $power), 2).' '.$units[$power];
    }

    /**
     * Get the duration of the backup process.
     */
    public function getDurationAttribute(): ?string
    {
        if (! $this->started_at || ! $this->completed_at) {
            return null;
        }

        $seconds = $this->completed_at->diffInSeconds($this->started_at);

        if ($seconds < 60) {
            return $seconds.'s';
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        return $minutes.'m '.$remainingSeconds.'s';
    }

    /**
     * Check if the backup file exists.
     */
    public function fileExists(): bool
    {
        if (! $this->file_path) {
            return false;
        }

        return file_exists($this->file_path);
    }

    /**
     * Delete the backup file from storage.
     */
    public function deleteFile(): bool
    {
        if (! $this->file_path || ! $this->fileExists()) {
            return false;
        }

        return unlink($this->file_path);
    }

    /**
     * Get the backup file name.
     */
    public function getFileNameAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        return basename($this->file_path);
    }

    /**
     * Scope to get only completed backups.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', BackupStatus::COMPLETED);
    }

    /**
     * Scope to get only failed backups.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', BackupStatus::FAILED);
    }

    /**
     * Scope to get manual backups.
     */
    public function scopeManual($query)
    {
        return $query->where('is_manual', true);
    }

    /**
     * Scope to get scheduled backups.
     */
    public function scopeScheduled($query)
    {
        return $query->where('is_manual', false);
    }

    /**
     * Scope to get backups by type.
     */
    public function scopeOfType($query, BackupType $type)
    {
        return $query->where('type', $type);
    }
}
