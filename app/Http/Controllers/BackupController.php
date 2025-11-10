<?php

namespace App\Http\Controllers;

use App\Enums\BackupStatus;
use App\Enums\BackupType;
use App\Jobs\ExecuteBackup;
use App\Jobs\ExecuteRestore;
use App\Models\Backup;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class BackupController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (! Auth::check() || Auth::user()->role !== 'admin') {
                    abort(403);
                }

                return $next($request);
            }),
        ];
    }

    public function __construct(
        protected BackupService $backupService
    ) {}

    /**
     * Display a listing of backups.
     */
    public function index(Request $request)
    {
        $query = Backup::with('createdBy')
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $backups = $query->paginate(15)->withQueryString();

        $statistics = $this->backupService->getStatistics();

        return view('admin.backups.index', compact('backups', 'statistics'));
    }

    /**
     * Show the form for creating a new backup.
     */
    public function create()
    {
        return view('admin.backups.create');
    }

    /**
     * Store a newly created backup.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:database,files,full'],
        ]);

        try {
            $type = match ($request->type) {
                'database' => BackupType::DATABASE,
                'files' => BackupType::FILES,
                'full' => BackupType::FULL,
            };

            // Create backup database record
            $backup = $this->backupService->createBackupRecord(
                type: $type,
                userId: Auth::id(),
                isManual: true
            );

            // Start backup in background
            ExecuteBackup::dispatch($backup);

            return redirect()
                ->route('admin.backups.show', $backup)
                ->with('success', 'Backup berhasil dibuat!');
        } catch (\Exception $e) {
            Log::error('Manual backup failed', [
                'user_id' => Auth::id(),
                'type' => $request->type,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Gagal membuat backup: '.$e->getMessage());
        }
    }

    /**
     * Display the specified backup.
     */
    public function show(Backup $backup)
    {
        $backup->load('createdBy');

        return view('admin.backups.show', compact('backup'));
    }

    /**
     * Download the backup file.
     */
    public function download(Backup $backup)
    {
        if ($backup->status !== BackupStatus::COMPLETED) {
            return redirect()
                ->back()
                ->with('error', 'Tidak dapat mengunduh backup yang belum selesai.');
        }

        if (! $backup->fileExists()) {
            return redirect()
                ->back()
                ->with('error', 'Backup file tidak ditemukan.');
        }

        return Response::download(
            $backup->file_path,
            $backup->file_name,
            [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="'.$backup->file_name.'"',
            ]
        );
    }

    /**
     * Restore from the backup.
     */
    public function restore(Request $request, Backup $backup)
    {
        if ($backup->status !== BackupStatus::COMPLETED) {
            return redirect()
                ->back()
                ->with('error', 'Tidak dapat memulihkan dari backup yang belum selesai.');
        }

        if (! $backup->fileExists()) {
            return redirect()
                ->back()
                ->with('error', 'Backup file tidak ditemukan.');
        }

        // Require confirmation
        $request->validate([
            'confirm' => ['required', 'accepted'],
        ], [
            'confirm.required' => 'Anda harus mengkonfirmasi operasi pemulihan.',
            'confirm.accepted' => 'Anda harus mengkonfirmasi operasi pemulihan.',
        ]);

        try {
            ExecuteRestore::dispatch($backup);

            Log::info('Backup restore initiated', [
                'backup_id' => $backup->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()
                ->route('admin.backups.index')
                ->with('success', 'Backup sedang dipulihkan. Proses ini mungkin memakan waktu.');
        } catch (\Exception $e) {
            Log::error('Backup restore failed', [
                'backup_id' => $backup->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Gagal memulihkan backup: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified backup.
     */
    public function destroy(Backup $backup)
    {
        try {
            $backup->deleteFile();
            $backup->delete();

            Log::info('Backup deleted', [
                'backup_id' => $backup->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()
                ->route('admin.backups.index')
                ->with('success', 'Backup berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Backup deletion failed', [
                'backup_id' => $backup->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus backup: '.$e->getMessage());
        }
    }

    /**
     * Get backup statistics for dashboard widget.
     */
    public function statistics()
    {
        $statistics = $this->backupService->getStatistics();

        return response()->json($statistics);
    }

    /**
     * Clean old backups.
     */
    public function cleanOld()
    {
        try {
            $deleted = 0;
            $keepLast = config('backup.retention.keep_last', 10);
            $keepDays = config('backup.retention.keep_days', 30);

            $oldBackups = Backup::where('status', BackupStatus::COMPLETED)
                ->where('created_at', '<', now()->subDays($keepDays))
                ->orderBy('created_at', 'desc')
                ->get()
                ->skip($keepLast);

            foreach ($oldBackups as $backup) {
                $backup->deleteFile();
                $backup->delete();
                $deleted++;
            }

            Log::info('Old backups cleaned', [
                'deleted_count' => $deleted,
                'user_id' => Auth::id(),
            ]);

            return redirect()
                ->route('admin.backups.index')
                ->with('success', "Berhasil membersihkan {$deleted} backup lama.");
        } catch (\Exception $e) {
            Log::error('Backup cleanup failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Gagal membersihkan backup: '.$e->getMessage());
        }
    }

    /**
     * Clean all backups.
     */
    public function clean()
    {
        try {
            $deleted = 0;
            $backups = Backup::all();

            foreach ($backups as $backup) {
                $backup->deleteFile();
                $backup->delete();
                $deleted++;
            }

            Log::info('All backups deleted', [
                'deleted_count' => $deleted,
                'user_id' => Auth::id(),
            ]);

            return redirect()
                ->route('admin.backups.index')
                ->with('success', 'Berhasil menghapus semua backup!');
        } catch (\Exception $e) {
            Log::error('Backup clean all failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus semua backup: '.$e->getMessage());
        }
    }
}
