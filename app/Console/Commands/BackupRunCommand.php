<?php

namespace App\Console\Commands;

use App\Enums\BackupType;
use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run
                            {--type=full : Type of backup (database, files, full)}
                            {--user= : User ID who triggered the backup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a backup of the application';

    /**
     * Execute the console command.
     */
    public function handle(BackupService $backupService)
    {
        if (! config('backup.enabled')) {
            $this->error('Backup is disabled in configuration.');

            return Command::FAILURE;
        }

        $typeInput = $this->option('type');
        $userId = $this->option('user');

        // Validate backup type
        $type = match (strtolower($typeInput)) {
            'database', 'db' => BackupType::DATABASE,
            'files', 'media' => BackupType::FILES,
            'full', 'all' => BackupType::FULL,
            default => null,
        };

        if (! $type) {
            $this->error("Invalid backup type: {$typeInput}");
            $this->info('Valid types: database, files, full');

            return Command::FAILURE;
        }

        $this->info("Starting {$type->label()} backup...");

        try {
            $backup = $backupService->run(
                type: $type,
                userId: $userId ? (int) $userId : null,
                isManual: $this->input->isInteractive()
            );

            $this->newLine();
            $this->info('✓ Backup completed successfully!');
            $this->newLine();
            $this->table(
                ['Property', 'Value'],
                [
                    ['Backup ID', $backup->id],
                    ['Type', $backup->type->label()],
                    ['File', $backup->file_name],
                    ['Size', $backup->file_size_formatted],
                    ['Duration', $backup->duration ?? 'N/A'],
                    ['Status', $backup->status->label()],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('✗ Backup failed!');
            $this->error($e->getMessage());
            $this->newLine();

            if ($this->output->isVerbose()) {
                $this->error($e->getTraceAsString());
            }

            return Command::FAILURE;
        }
    }
}
