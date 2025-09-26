<?php

namespace Modules\EBilling\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\EBilling\Http\Controllers\CustomerController;
use Modules\EBilling\Models\Customer;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportCustomersJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes

    public function __construct(protected string $path, protected string $token) {}

    public function handle(): void
    {
        $cacheKey = CustomerController::importCacheKey($this->token);
        $state = Cache::get($cacheKey);
        if (! $state) {
            // nothing to do
            return;
        }
        $this->updateState(fn ($s) => array_merge($s, ['status' => 'processing']));

        $fullPath = Storage::path($this->path);

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $rows = [];
        try {
            if (in_array($ext, ['xlsx', 'xls'])) {
                $spreadsheet = IOFactory::load($fullPath);
                $sheet = $spreadsheet->getActiveSheet();
                foreach ($sheet->toArray(null, true, true, true) as $index => $row) {
                    $rows[] = $row; // maintain raw
                }
            } else { // csv or txt
                $handle = fopen($fullPath, 'r');
                while (($data = fgetcsv($handle)) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            }

            if (count($rows) <= 1) {
                throw new \RuntimeException('File kosong atau tidak memiliki data.');
            }
            // Assume first row header mapping
            $headers = array_map(fn ($h) => strtolower(trim(is_array($h) ? reset($h) : $h)), $rows[0]);
            // Expected columns minimal
            // customer_id,name,email,phone,address,due,site_id,package_id,device_id
            $expected = ['customer_id', 'name', 'email', 'phone', 'address', 'due', 'site_id', 'package_id', 'device_id'];
            foreach ($expected as $col) {
                if (! in_array($col, $headers)) {
                    throw new \RuntimeException('Kolom wajib diisi: '.$col);
                }
            }

            $dataRows = array_slice($rows, 1);
            $total = count($dataRows);
            $this->updateState(fn ($s) => array_merge($s, ['total' => $total]));

            $chunkSize = 500;
            $processed = 0;
            foreach (array_chunk($dataRows, $chunkSize) as $chunk) {
                DB::transaction(function () use ($chunk, $headers, &$processed) {
                    $insert = [];
                    foreach ($chunk as $rawRow) {
                        // unify row into associative
                        if (is_array($rawRow) && array_is_list($rawRow)) {
                            // numeric index row from CSV
                            $rowAssoc = [];
                            foreach ($headers as $i => $header) {
                                $rowAssoc[$header] = $rawRow[$i] ?? null;
                            }
                        } else {
                            // PhpSpreadsheet style (alphabet keys)
                            $rowAssoc = [];
                            foreach ($headers as $i => $header) {
                                // letters start at A; convert index to letter
                                $cellKey = is_array($rawRow) ? array_keys($rawRow)[$i] ?? null : null;
                                $rowAssoc[$header] = $cellKey ? ($rawRow[$cellKey] ?? null) : null;
                            }
                        }
                        if (! trim((string) ($rowAssoc['customer_id'] ?? ''))) {
                            continue; // skip empty
                        }
                        $due = (int) ($rowAssoc['due'] ?? 1);
                        $insert[] = [
                            'customer_id' => trim($rowAssoc['customer_id']),
                            'name' => $rowAssoc['name'] ?? '-',
                            'email' => $rowAssoc['email'] ?? '',
                            'phone' => $rowAssoc['phone'] ?? '',
                            'address' => $rowAssoc['address'] ?? '',
                            'due' => $due ?: 1,
                            'site_id' => (int) ($rowAssoc['site_id'] ?? 0),
                            'package_id' => (int) ($rowAssoc['package_id'] ?? 0),
                            'device_id' => (int) ($rowAssoc['device_id'] ?? 0),
                            'registration_date' => now(),
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    if ($insert) {
                        // upsert to avoid duplicates by customer_id
                        Customer::upsert($insert, ['customer_id'], ['name', 'email', 'phone', 'address', 'due', 'site_id', 'package_id', 'device_id', 'updated_at']);
                    }
                    $processed += count($chunk);
                });
                $this->updateState(function ($s) use ($processed, $total) {
                    $s['processed'] = $processed;
                    $s['progress'] = $total > 0 ? round($processed / $total * 100, 2) : 100;

                    return $s;
                });
            }
            $this->updateState(fn ($s) => array_merge($s, ['status' => 'finished', 'finished_at' => now()->toISOString()]));
        } catch (\Throwable $e) {
            Log::error('Import customers failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->updateState(fn ($s) => array_merge($s, [
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'finished_at' => now()->toISOString(),
            ]));
        } finally {
            // Optionally delete file
            Storage::delete($this->path);
        }
    }

    protected function updateState(callable $callback): void
    {
        $cacheKey = CustomerController::importCacheKey($this->token);
        $state = Cache::get($cacheKey);
        if ($state === null) {
            return;
        }
        $newState = $callback($state);
        Cache::put($cacheKey, $newState, now()->addHour());
    }
}
