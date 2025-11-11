# 🚀 Laba Rugi - Quick Start Guide

## Installation Completed ✅

The Laba Rugi (Profit & Loss) feature has been successfully implemented in your BUMDes application!

---

## 📍 Access the Feature

1. **Login** as Admin user
2. Look for **"Laporan Laba Rugi"** in the sidebar menu (with 📊 icon)
3. Click to open the report page

---

## 🎯 Quick Actions

### View Report

```
1. Select date range (default: current month)
2. Click "Generate Laporan"
3. View charts and detailed breakdown
```

### Export PDF

```
1. Generate report first
2. Click "Export PDF" button
3. PDF downloads automatically
```

### Record Interest Income

```bash
# For current month
php artisan interest:record

# For specific month (e.g., October 2025)
php artisan interest:record --month=2025-10
```

---

## 📊 What You'll See

### Summary Cards

-   💚 **Total Pendapatan** (Total Income)
-   🔴 **Total Beban** (Total Expense)
-   💙 **Laba/Rugi Bersih** (Net Profit/Loss)

### Charts

1. **Income vs Expense** - Bar comparison
2. **Trend Over Time** - Line chart
3. **Income Breakdown** - Pie chart
4. **Expense Breakdown** - Pie chart

### Detailed Tables

-   Income by category with subcategories
-   Interest income from funding requests
-   Expenses by category with subcategories
-   Final net profit/loss calculation

---

## 🗂️ Transaction Categories

### Income (Pendapatan)

-   Pendapatan Operasional
    -   Pendapatan Jasa
    -   Pendapatan Penjualan
-   Pendapatan Non-Operasional
    -   Bantuan/Hibah
    -   Pendapatan Lain-lain
-   Pendapatan Bunga (auto-calculated)

### Expense (Beban)

-   Beban Operasional
    -   Gaji dan Tunjangan
    -   Biaya Listrik, Air, Telepon
    -   Biaya Transportasi & Pemeliharaan
-   Beban Administrasi
    -   Biaya ATK, Cetak, Perizinan
-   Beban Lain-lain
    -   Beban Penyusutan & Miscellaneous

---

## 💡 Sample Data

To see the report in action, run:

```bash
php artisan db:seed --class=SampleTransactionSeeder
```

This creates 12 sample transactions for testing.

---

## 🔧 Common Tasks

### Re-seed Categories (if needed)

```bash
php artisan db:seed --class=TransactionCategorySeeder
```

### Check Interest Recording

```bash
# View help
php artisan interest:record --help

# Dry run (check what would be recorded)
php artisan interest:record --month=2025-11
```

### Clear Cache (if menu doesn't show)

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## ⚙️ Schedule Monthly Interest (Optional)

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Record interest on last day of each month
    $schedule->command('interest:record')
        ->monthlyOn(28, '23:00');
}
```

Then ensure cron is running:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🎨 UI Features

-   ✅ Responsive design (works on mobile)
-   ✅ Loading indicators
-   ✅ Empty state handling
-   ✅ Error messages
-   ✅ Color-coded profit/loss
-   ✅ Interactive charts
-   ✅ Professional PDF output

---

## 🔒 Security

-   Only **Admin** can access
-   CSRF protected
-   SQL injection safe
-   XSS protection enabled

---

## 📚 Documentation

For detailed information, see:

-   **LABA_RUGI_README.md** - Complete user guide
-   **IMPLEMENTATION_SUMMARY.md** - Technical details

---

## ❓ Troubleshooting

### Menu doesn't appear?

```bash
php artisan config:clear
```

### Charts not showing?

-   Check browser console for JavaScript errors
-   Ensure internet connection (Chart.js loads from CDN)

### PDF export fails?

```bash
composer require barryvdh/laravel-dompdf
```

### No transactions showing?

```bash
php artisan db:seed --class=SampleTransactionSeeder
```

---

## 🚀 Next Steps

1. **Test the Report**

    - Generate report for current month
    - Export to PDF
    - Check all charts load correctly

2. **Record Interest Income**

    - Run `php artisan interest:record` if you have active funding
    - Check transactions table for new records

3. **Add Real Transactions**

    - Manually insert into `transactions` table, OR
    - Build CRUD interface for transaction management (future enhancement)

4. **Customize Categories**
    - Add/edit categories in `transaction_categories` table
    - Match your BUMDes needs

---

## 📞 Support

Questions? Check:

1. LABA_RUGI_README.md
2. Code comments in Controllers/Services
3. Sample seeders for data format

---

**Status:** ✅ Ready to use!
**Version:** 1.0.0
**Date:** November 11, 2025

Happy reporting! 🎉
