<?php

namespace Modules\EBilling\Imports;

use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Jobs\AfterImportJob;
use Modules\EBilling\Models\Customer;
use Modules\EBilling\Models\Device;
use Modules\EBilling\Models\Package;
use Modules\EBilling\Models\Site;
use Modules\EBilling\Notifications\CustomerImportFinished;

class CustomersImport implements ToModel, WithChunkReading, WithEvents, WithHeadingRow, WithValidation
{
    protected array $sites;

    protected array $packages;

    protected array $devices;

    use Importable;

    public function __construct()
    {
        $this->sites = Site::pluck('id', 'code')->toArray();
        $this->packages = Package::pluck('id', 'code')->toArray();
        $this->devices = Device::pluck('id', 'code')->toArray();
    }

    /**
     * @return Customer|null
     */
    public function model(array $row)
    {
        return new Customer([
            ...$row,
            'site_id' => $this->sites[$row['site_code']] ?? null,
            'package_id' => $this->packages[$row['package_code']] ?? null,
            'device_id' => $this->devices[$row['device_code']] ?? null,
            'registration_date' => now(),
        ]);
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|max:50|unique:ebil_customers,customer_id',
            'name' => 'required|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|phone:mobile,ID',
            'address' => 'nullable|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'site_code' => 'required|exists:ebil_sites,code',
            'package_code' => 'required|exists:ebil_packages,code',
            'device_code' => 'required|exists:ebil_devices,code',
            'serial_number' => 'nullable|max:100',
            'mac_address' => 'nullable|max:100',
            'due' => 'required|integer|min:1|max:28',
            'due_reminder_days' => 'required|integer|min:1|max:30',
            'grace_period' => 'required|integer|min:1|max:30',
        ];
    }

    /**
     * @return array
     */
    public function customValidationAttributes()
    {
        return [
            'customer_id' => 'ID pelanggan',
            'name' => 'Nama pelanggan',
            'email' => 'Email',
            'phone' => 'Nomor HP',
            'address' => 'Alamat',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'site_code' => 'Kode site',
            'package_code' => 'Kode paket',
            'device_code' => 'Kode perangkat',
            'serial_number' => 'Serial number',
            'mac_address' => 'MAC address',
            'due' => 'Tanggal jatuh tempo',
            'due_reminder_days' => 'Tanggal pengingat',
            'grace_period' => 'Batas waktu pembayaran',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function registerEvents(): array
    {
        return [
            AfterImportJob::class => function (AfterImportJob $event) {
                logger()->info(json_encode($event));
                // Notification::send(, new CustomerImportFinished());
            },
        ];
    }

    public function onError(\Throwable $e)
    {
        logger()->error('Error during customer import: '.$e->getMessage());
    }
}
