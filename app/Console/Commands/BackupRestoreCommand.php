<?php

namespace App\Console\Commands;

use App\Enums\BackupStatus;
use App\Models\Backup;
use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupRestoreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:restore
                            {backup? : Backup ID to restore}
                            {--latest : Restore from the latest backup}
                            {--type= : Filter by backup type when using --latest}
                            {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore data from a backup';

    /**
     * Execute the console command.
     */
    public function handle(BackupService $backupService)
    {
        $backupId = $this->argument('backup');
        $latest = $this->option('latest');
        $type = $this->option('type');

        // Get the backup
        if ($backupId) {
            $backup = Backup::find($backupId);

            if (! $backup) {
                $this->error("Backup with ID {$backupId} not found.");

                return Command::FAILURE;
            }
        } elseif ($latest) {
            $query = Backup::where('status', BackupStatus::COMPLETED)
                ->orderBy('completed_at', 'desc');

            if ($type) {
                $query->where('type', $type);
            }

            $backup = $query->first();

            if (! $backup) {
                $this->error('No completed backup found.');

                return Command::FAILURE;
            }
        } else {
            // Show list of available backups
            $backups = Backup::where('status', BackupStatus::COMPLETED)
                ->orderBy('completed_at', 'desc')
                ->limit(10)
                ->get();

            if ($backups->isEmpty()) {
                $this->error('No completed backups available.');

                return Command::FAILURE;
            }

            $this->info('Available backups:');
            $this->newLine();

            $this->table(
                ['ID', 'Name', 'Type', 'Size', 'Created'],
                $backups->map(fn ($b) => [
                    $b->id,
                    $b->name,
                    $b->type->label(),
                    $b->file_size_formatted,
                    $b->completed_at->format('Y-m-d H:i:s'),
                ])
            );

            $this->newLine();
            $this->info('Use: php artisan backup:restore {id}');
            $this->info('Or: php artisan backup:restore --latest');

            return Command::SUCCESS;
        }

        // Display backup information
        $this->info('Backup Details:');
        $this->newLine();
        $this->table(
            ['Property', 'Value'],
            [
                ['ID', $backup->id],
                ['Name', $backup->name],
                ['Type', $backup->type->label()],
                ['Size', $backup->file_size_formatted],
                ['Created', $backup->completed_at->format('Y-m-d H:i:s')],
                ['File', $backup->file_name],
            ]
        );
        $this->newLine();

        // Check if file exists
        if (! $backup->fileExists()) {
            $this->error('Backup file does not exist!');

            return Command::FAILURE;
        }

        // Confirm restore
        if (! $this->option('force')) {
            $this->warn('⚠ WARNING: This will overwrite current data!');
            $this->newLine();

            if (! $this->confirm('Are you sure you want to restore from this backup?', false)) {
                $this->info('Restore cancelled.');

                return Command::SUCCESS;
            }

            if (! $this->confirm('Are you ABSOLUTELY sure? This action cannot be undone!', false)) {
                $this->info('Restore cancelled.');

                return Command::SUCCESS;
            }
        }

        // Perform restore
        $this->newLine();
        $this->info('Starting restore process...');

        try {
            $backupService->restore($backup);

            $this->newLine();
            $this->info('✓ Restore completed successfully!');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('✗ Restore failed!');
            $this->error($e->getMessage());
            $this->newLine();

            if ($this->output->isVerbose()) {
                $this->error($e->getTraceAsString());
            }

            return Command::FAILURE;
        }
    }
}
