<?php

namespace App\Jobs;

use App\Models\Backup;
use App\Services\BackupService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ExecuteBackup implements ShouldQueue
{
    use Queueable;

    protected BackupService $backupService;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Backup $backupRecord
    ) {
        $this->backupService = app(BackupService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting backup job for Backup ID: {$this->backupRecord->id}");
        $this->backupService->execute($this->backupRecord);
    }
}
