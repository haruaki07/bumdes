<?php

namespace App\Console\Commands;

use App\Enums\BackupStatus;
use App\Models\Backup;
use Illuminate\Console\Command;

class BackupCleanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:clean
                            {--force : Force deletion without confirmation}
                            {--keep= : Number of backups to keep (overrides config)}
                            {--days= : Delete backups older than X days (overrides config)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old backups based on retention policy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! config('backup.retention.enabled')) {
            $this->info('Backup retention is disabled in configuration.');

            return Command::SUCCESS;
        }

        $keepLast = $this->option('keep') ?? config('backup.retention.keep_last', 10);
        $keepDays = $this->option('days') ?? config('backup.retention.keep_days', 30);

        $this->info('Checking for old backups to clean...');
        $this->newLine();

        // Find old backups
        $query = Backup::where('status', BackupStatus::COMPLETED)
            ->where('created_at', '<', now()->subDays($keepDays))
            ->orderBy('created_at', 'desc');

        $totalCount = $query->count();
        $oldBackups = $query->skip($keepLast)->get();

        if ($oldBackups->isEmpty()) {
            $this->info('No old backups to clean.');

            return Command::SUCCESS;
        }

        $this->info("Found {$oldBackups->count()} old backup(s) to delete:");
        $this->newLine();

        $this->table(
            ['ID', 'Name', 'Type', 'Size', 'Created'],
            $oldBackups->map(fn ($backup) => [
                $backup->id,
                $backup->name,
                $backup->type->label(),
                $backup->file_size_formatted,
                $backup->created_at->format('Y-m-d H:i:s'),
            ])
        );

        $this->newLine();

        if (! $this->option('force')) {
            if (! $this->confirm('Do you want to delete these backups?', false)) {
                $this->info('Operation cancelled.');

                return Command::SUCCESS;
            }
        }

        $deleted = 0;
        $failed = 0;

        foreach ($oldBackups as $backup) {
            try {
                $backup->deleteFile();
                $backup->delete();
                $deleted++;
                $this->info("✓ Deleted backup #{$backup->id}");
            } catch (\Exception $e) {
                $failed++;
                $this->error("✗ Failed to delete backup #{$backup->id}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Cleanup completed: {$deleted} deleted, {$failed} failed.");

        return Command::SUCCESS;
    }
}
