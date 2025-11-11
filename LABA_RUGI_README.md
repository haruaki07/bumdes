# Laporan Laba Rugi (Profit & Loss Report)

## Overview

Fitur Laporan Laba Rugi adalah sistem pelaporan keuangan untuk BUMDes yang mencatat dan menampilkan pendapatan, beban, serta laba/rugi bersih dalam periode tertentu.

## Features

-   ✅ Pencatatan transaksi pendapatan dan beban
-   ✅ Kategorisasi transaksi (dengan sub-kategori)
-   ✅ Perhitungan otomatis bunga dari pendanaan
-   ✅ Laporan dengan filter tanggal custom
-   ✅ Visualisasi data dengan charts (ApexCharts)
-   ✅ Export laporan ke PDF
-   ✅ Admin-only access

## Database Structure

### Tables Created

1. **transaction_categories** - Kategori transaksi (pendapatan & beban)
2. **transactions** - Record transaksi keuangan

### Transaction Categories

Kategori default yang sudah di-seed:

**Pendapatan (Income):**

-   Pendapatan Operasional
    -   Pendapatan Jasa
    -   Pendapatan Penjualan
-   Pendapatan Non-Operasional
    -   Bantuan/Hibah
    -   Pendapatan Lain-lain
-   Pendapatan Bunga (auto-generated)

**Beban (Expense):**

-   Beban Operasional
    -   Gaji dan Tunjangan
    -   Biaya Listrik
    -   Biaya Air
    -   Biaya Telepon & Internet
    -   Biaya Transportasi
    -   Biaya Pemeliharaan
-   Beban Administrasi
    -   Biaya ATK
    -   Biaya Cetak & Fotokopi
    -   Biaya Perizinan
-   Beban Lain-lain
    -   Beban Penyusutan
    -   Beban Miscellaneous

## Usage

### Accessing the Report

1. Login sebagai **Admin**
2. Navigate ke menu **"Laporan Laba Rugi"** di sidebar
3. Pilih periode tanggal (default: bulan ini)
4. Klik **"Generate Laporan"**

### Recording Transactions

Saat ini, transaksi dapat dicatat langsung ke database atau melalui seeder. Untuk pengembangan selanjutnya, Anda dapat membuat CRUD interface untuk mengelola transaksi.

### Recording Interest Income

Pendapatan bunga dari funding requests dapat dicatat otomatis menggunakan command:

```bash
# Record interest for current month
php artisan interest:record

# Record interest for specific month
php artisan interest:record --month=2025-10

# Record interest for specific funding request
php artisan interest:record --funding-request=1

# Record interest for specific funding request in specific month
php artisan interest:record --month=2025-10 --funding-request=1
```

#### Automated Interest Recording (Optional)

Untuk mencatat bunga secara otomatis setiap bulan, tambahkan ke `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Record interest income on the last day of each month
    $schedule->command('interest:record')
        ->monthlyOn(28, '23:00')
        ->timezone('Asia/Jakarta');
}
```

### Exporting to PDF

1. Generate laporan terlebih dahulu
2. Klik tombol **"Export PDF"**
3. File PDF akan otomatis ter-download

## Interest Calculation Formula

Interest income dihitung dengan formula:

```
Interest = (Principal × Annual Rate × Days) / 365
```

Dimana:

-   **Principal**: Jumlah dana yang dicairkan (disbursed_amount)
-   **Annual Rate**: Suku bunga per tahun (interest_rate)
-   **Days**: Jumlah hari dalam periode perhitungan

Contoh:

-   Principal: Rp 10,000,000
-   Rate: 12% per tahun
-   Days: 30 hari
-   Interest = (10,000,000 × 0.12 × 30) / 365 = Rp 98,630

## API Endpoints

### Generate Report

```
POST /laba-rugi/generate
Content-Type: application/json

{
  "start_date": "2025-11-01",
  "end_date": "2025-11-30"
}
```

**Response:**

```json
{
  "success": true,
  "data": {
    "period": {...},
    "income": {...},
    "expense": {...},
    "net_profit": {...}
  },
  "charts": {...}
}
```

### Export PDF

```
POST /laba-rugi/export-pdf
Content-Type: application/x-www-form-urlencoded

start_date=2025-11-01
end_date=2025-11-30
```

## Seeding Sample Data

Untuk testing, jalankan seeder:

```bash
# Seed transaction categories
php artisan db:seed --class=TransactionCategorySeeder

# Seed sample transactions
php artisan db:seed --class=SampleTransactionSeeder
```

## Future Enhancements

Fitur yang bisa dikembangkan:

1. **Transaction CRUD** - Interface untuk input transaksi manual
2. **Budget Management** - Perbandingan budget vs actual
3. **Period Comparison** - Perbandingan antar periode
4. **Export to Excel** - Format export tambahan
5. **Email Reports** - Automated email laporan berkala
6. **Transaction Approval** - Workflow approval transaksi
7. **Multi-Business Unit** - Laporan per unit bisnis
8. **Cash Flow Report** - Laporan arus kas
9. **Balance Sheet** - Neraca keuangan

## Permission & Security

-   Hanya **Admin** yang dapat mengakses laporan Laba Rugi
-   Menggunakan Gate: `viewLabaRugi`
-   Semua transaksi harus diverifikasi sebelum masuk laporan
-   Interest income dicatat dengan verified status otomatis

## Dependencies

-   Laravel 11.x
-   barryvdh/laravel-dompdf (PDF generation)
-   Chart.js 4.4.0 (visualization)
-   Bootstrap 5 / Tablar theme

## Files Structure

```
app/
├── Console/Commands/
│   └── RecordInterestIncome.php
├── Http/
│   ├── Controllers/
│   │   └── LabaRugiController.php
│   └── Requests/
│       └── GenerateLabaRugiRequest.php
├── Models/
│   ├── Transaction.php
│   └── TransactionCategory.php
├── Services/
│   └── LabaRugiService.php
└── Providers/
    └── AppServiceProvider.php (Gate definition)

database/
├── migrations/
│   ├── *_create_transaction_categories_table.php
│   └── *_create_transactions_table.php
└── seeders/
    ├── TransactionCategorySeeder.php
    └── SampleTransactionSeeder.php

resources/views/
└── laba-rugi/
    ├── index.blade.php
    └── pdf.blade.php

routes/
└── web.php (laba-rugi routes)

config/
└── tablar.php (menu configuration)
```

## Support

Untuk pertanyaan atau issue, hubungi tim development atau buat issue di repository.

---

**Created:** November 2025
**Version:** 1.0.0
