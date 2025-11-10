<?php

namespace App\Services;

use App\Enums\BackupStatus;
use App\Enums\BackupType;
use App\Models\Backup;
use App\Notifications\BackupFailedNotification;
use App\Notifications\BackupSuccessNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use ZipArchive;

class BackupService
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('backup');
        $this->ensureDirectoriesExist();
    }

    /**
     * Run a backup.
     */
    public function run(BackupType $type, ?int $userId = null, bool $isManual = false): Backup
    {
        $backup = $this->createBackupRecord($type, $userId, $isManual);

        return $this->execute($backup);
    }

    /**
     * Restore from a backup.
     */
    public function restore(Backup $backup): bool
    {
        if ($backup->status !== BackupStatus::COMPLETED) {
            throw new \Exception('Cannot restore from an incomplete backup');
        }

        if (! $backup->fileExists()) {
            throw new \Exception('Backup file does not exist');
        }

        try {
            match ($backup->type) {
                BackupType::DATABASE => $this->restoreDatabase($backup),
                BackupType::FILES => $this->restoreFiles($backup),
                BackupType::FULL => $this->restoreFull($backup),
            };

            Log::info('Backup restored successfully', ['backup_id' => $backup->id]);

            return true;
        } catch (\Exception $e) {
            Log::error('Backup restore failed', [
                'backup_id' => $backup->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function execute(Backup $backup): Backup
    {
        try {
            $backup->update([
                'status' => BackupStatus::IN_PROGRESS,
                'started_at' => now(),
            ]);

            $filePath = match ($backup->type) {
                BackupType::DATABASE => $this->backupDatabase($backup),
                BackupType::FILES => $this->backupFiles($backup),
                BackupType::FULL => $this->backupFull($backup),
            };

            $fileSize = file_exists($filePath) ? filesize($filePath) : 0;

            $backup->update([
                'status' => BackupStatus::COMPLETED,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'completed_at' => now(),
            ]);

            $this->sendNotification($backup, true);
            $this->cleanOldBackups();

            Log::info('Backup completed successfully', ['backup_id' => $backup->id]);

            return $backup->fresh();
        } catch (\Exception $e) {
            $backup->update([
                'status' => BackupStatus::FAILED,
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            $this->sendNotification($backup, false);

            Log::error('Backup failed', [
                'backup_id' => $backup->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Backup database.
     */
    protected function backupDatabase(): string
    {
        $timestamp = now()->format('Y-m-d_His');
        $filename = "database_backup_{$timestamp}.sql";
        $filepath = $this->config['paths']['database'].'/'.$filename;

        $connection = config('database.default');
        $dbConfig = config("database.connections.{$connection}");

        $command = $this->getDumpCommand($dbConfig, $filepath);

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('Database backup failed: '.implode("\n", $output));
        }

        // Compress the SQL file if compression is enabled
        if ($this->config['compression']['enabled']) {
            $compressedFile = $filepath.'.gz';
            $this->compressFile($filepath, $compressedFile);
            unlink($filepath);

            return $compressedFile;
        }

        return $filepath;
    }

    /**
     * Backup files and media.
     */
    protected function backupFiles(): string
    {
        $timestamp = now()->format('Y-m-d_His');
        $filename = "files_backup_{$timestamp}.zip";
        $filepath = $this->config['paths']['files'].'/'.$filename;

        $zip = new ZipArchive;
        if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception('Cannot create zip file');
        }

        $includePaths = $this->config['files']['include'];
        $excludePaths = $this->config['files']['exclude'];

        foreach ($includePaths as $path) {
            if (is_dir($path)) {
                $this->addDirectoryToZip($zip, $path, $path, $excludePaths);
            } elseif (is_file($path)) {
                $zip->addFile($path, basename($path));
            }
        }

        $zip->close();

        return $filepath;
    }

    /**
     * Backup both database and files.
     */
    protected function backupFull(Backup $backup): string
    {
        $timestamp = now()->format('Y-m-d_His');
        $filename = "full_backup_{$timestamp}.zip";
        $filepath = $this->config['paths']['backup'].'/'.$filename;

        // Create temporary backups
        $dbFile = $this->backupDatabase();
        $filesFile = $this->backupFiles();

        // Combine into one zip
        $zip = new ZipArchive;
        if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception('Cannot create full backup zip file');
        }

        $zip->addFile($dbFile, 'database/'.basename($dbFile));
        $zip->addFile($filesFile, 'files/'.basename($filesFile));
        $zip->close();

        // Clean up temporary files
        if (file_exists($dbFile)) {
            unlink($dbFile);
        }
        if (file_exists($filesFile)) {
            unlink($filesFile);
        }

        return $filepath;
    }

    /**
     * Restore database from backup.
     */
    protected function restoreDatabase(Backup $backup, ?string $filepath = null): void
    {
        $filepath = $filepath ?? $backup->file_path;

        // Decompress if needed
        if (str_ends_with($filepath, '.gz')) {
            $decompressed = $this->config['paths']['temp'].'/'.basename($filepath, '.gz');
            $this->decompressFile($filepath, $decompressed);
            $filepath = $decompressed;
        }

        $connection = config('database.default');
        $dbConfig = config("database.connections.{$connection}");

        $command = $this->getRestoreCommand($dbConfig, $filepath);

        exec($command, $output, $returnVar);

        // Clean up temp file
        if (isset($decompressed) && file_exists($decompressed)) {
            unlink($decompressed);
        }

        if ($returnVar !== 0) {
            throw new \Exception('Database restore failed: '.implode("\n", $output));
        }

        // Delete backup record
        DB::table('backups')->where('id', $backup->id)->delete();
    }

    /**
     * Restore files from backup.
     */
    protected function restoreFiles(Backup $backup, ?string $filepath = null): void
    {
        $filepath = $filepath ?? $backup->file_path;

        $zip = new ZipArchive;
        if ($zip->open($filepath) !== true) {
            throw new \Exception('Cannot open backup zip file');
        }

        $extractPath = $this->config['paths']['temp'].'/restore_'.time();
        $zip->extractTo($extractPath);
        $zip->close();

        // Move files to their original locations
        $includePaths = $this->config['files']['include'];
        foreach ($includePaths as $targetPath) {
            $sourcePath = $extractPath.'/'.basename($targetPath);
            if (is_dir($sourcePath)) {
                File::copyDirectory($sourcePath, $targetPath);
            }
        }

        // Clean up temp directory
        File::deleteDirectory($extractPath);
    }

    /**
     * Restore full backup.
     */
    protected function restoreFull(Backup $backup): void
    {
        $filepath = $backup->file_path;

        $zip = new ZipArchive;
        if ($zip->open($filepath) !== true) {
            throw new \Exception('Cannot open full backup zip file');
        }

        $extractPath = $this->config['paths']['temp'].'/restore_full_'.time();
        $zip->extractTo($extractPath);
        $zip->close();

        // find extracted database and files backups
        $dbFile = collect(File::files($extractPath.'/database'))->first();
        $filesFile = collect(File::files($extractPath.'/files'))->first();

        if ($dbFile) {
            $this->restoreDatabase($backup, $dbFile->getPathname());
        }

        if ($filesFile) {
            $this->restoreFiles($backup, $filesFile->getPathname());
        }

        File::deleteDirectory($extractPath);
    }

    /**
     * Get mysqldump command.
     */
    protected function getDumpCommand(array $dbConfig, string $filepath): string
    {
        $host = $dbConfig['host'];
        $port = $dbConfig['port'] ?? 3306;
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];

        $excludeTables = $this->config['database']['exclude_tables'];
        $excludeParams = '';
        foreach ($excludeTables as $table) {
            $excludeParams .= " --ignore-table={$database}.{$table}";
        }

        return sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            $excludeParams,
            escapeshellarg($database),
            escapeshellarg($filepath)
        );
    }

    /**
     * Get mysql restore command.
     */
    protected function getRestoreCommand(array $dbConfig, string $filepath): string
    {
        $host = $dbConfig['host'];
        $port = $dbConfig['port'] ?? 3306;
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];

        return sprintf(
            'mysql --host=%s --port=%s --user=%s --password=%s %s < %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($filepath)
        );
    }

    /**
     * Add directory to zip recursively.
     */
    protected function addDirectoryToZip(ZipArchive $zip, string $path, string $basePath, array $excludePaths = []): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (! $file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($basePath) + 1);

                // Check if file should be excluded
                $shouldExclude = false;
                foreach ($excludePaths as $excludePath) {
                    if (str_starts_with($filePath, $excludePath)) {
                        $shouldExclude = true;
                        break;
                    }
                }

                if (! $shouldExclude) {
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
    }

    /**
     * Compress file using gzip.
     */
    protected function compressFile(string $source, string $destination): void
    {
        $level = $this->config['compression']['level'];
        $command = sprintf(
            'gzip -%d -c %s > %s',
            $level,
            escapeshellarg($source),
            escapeshellarg($destination)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('File compression failed');
        }
    }

    /**
     * Decompress gzip file.
     */
    protected function decompressFile(string $source, string $destination): void
    {
        $command = sprintf(
            'gzip -d -c %s > %s',
            escapeshellarg($source),
            escapeshellarg($destination)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('File decompression failed');
        }
    }

    /**
     * Create backup record.
     */
    public function createBackupRecord(BackupType $type, ?int $userId, bool $isManual): Backup
    {
        $name = sprintf(
            '%s backup - %s',
            $type->label(),
            now()->format('Y-m-d H:i:s')
        );

        return Backup::create([
            'name' => $name,
            'type' => $type,
            'status' => BackupStatus::PENDING,
            'created_by' => $userId,
            'is_manual' => $isManual,
        ]);
    }

    /**
     * Clean old backups based on retention policy.
     */
    protected function cleanOldBackups(): void
    {
        if (! $this->config['retention']['enabled']) {
            return;
        }

        $keepLast = $this->config['retention']['keep_last'];
        $keepDays = $this->config['retention']['keep_days'];

        // Delete old backups
        $oldBackups = Backup::where('status', BackupStatus::COMPLETED)
            ->where('created_at', '<', now()->subDays($keepDays))
            ->orderBy('created_at', 'desc')
            ->get()
            ->slice($keepLast);

        foreach ($oldBackups as $backup) {
            $backup->deleteFile();
            $backup->delete();
        }

        Log::info('Old backups cleaned', ['deleted_count' => $oldBackups->count()]);
    }

    /**
     * Send notification about backup.
     */
    protected function sendNotification(Backup $backup, bool $success): void
    {
        Notification::send($backup->createdBy, $success ? new BackupSuccessNotification($backup) : new BackupFailedNotification($backup));
    }

    /**
     * Ensure backup directories exist.
     */
    protected function ensureDirectoriesExist(): void
    {
        $directories = [
            $this->config['paths']['backup'],
            $this->config['paths']['database'],
            $this->config['paths']['files'],
            $this->config['paths']['temp'],
        ];

        foreach ($directories as $directory) {
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }
    }

    /**
     * Get backup statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total' => Backup::count(),
            'completed' => Backup::where('status', BackupStatus::COMPLETED)->count(),
            'failed' => Backup::where('status', BackupStatus::FAILED)->count(),
            'total_size' => Backup::where('status', BackupStatus::COMPLETED)->sum('file_size'),
            'last_backup' => Backup::where('status', BackupStatus::COMPLETED)
                ->latest('completed_at')
                ->first(),
            'by_type' => [
                'database' => Backup::where('type', BackupType::DATABASE)
                    ->where('status', BackupStatus::COMPLETED)
                    ->count(),
                'files' => Backup::where('type', BackupType::FILES)
                    ->where('status', BackupStatus::COMPLETED)
                    ->count(),
                'full' => Backup::where('type', BackupType::FULL)
                    ->where('status', BackupStatus::COMPLETED)
                    ->count(),
            ],
        ];
    }
}
