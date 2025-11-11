# Transaction CRUD - Quick Reference

## 🚀 Quick Start

### Access the Features

1. Login as **admin** user
2. Navigate to **Keuangan** menu (sidebar)
3. Choose from:
    - **Transaksi** - Manage all transactions
    - **Kategori Transaksi** - Manage categories
    - **Laporan Laba Rugi** - View profit/loss report

## 📋 URLs

```
/transaction-categories          - List categories
/transaction-categories/create   - Create category
/transaction-categories/{id}/edit - Edit category

/transactions                    - List transactions
/transactions/create             - Create transaction
/transactions/{id}               - View detail
/transactions/{id}/edit          - Edit transaction
```

## 🎯 Common Tasks

### Create a Category

1. Go to "Kategori Transaksi"
2. Click "Tambah Kategori"
3. Fill: Name, Code (auto-generated), Type (Income/Expense)
4. Optional: Select parent category, add description
5. Click "Simpan"

### Create a Transaction

1. Go to "Transaksi"
2. Click "Tambah Transaksi"
3. Select Type (Income/Expense)
4. Choose Category (filtered by type)
5. Enter Amount (use . or , for thousands)
6. Select Date
7. Optional: Upload document, add notes, link to funding request
8. Click "Simpan"

### Verify a Transaction

1. Open transaction detail
2. Click "Verifikasi"
3. Confirm
4. Transaction is now locked (can't edit/delete)
5. Can unverify later if needed

### Filter Transactions

1. Go to "Transaksi"
2. Use filter panel:
    - Type: Income/Expense
    - Category: Select from dropdown
    - Date Range: Start and End date
    - Status: Verified/Unverified
3. Click "Filter"

## ⚙️ Auto Features

### Auto-Generated Reference Numbers

-   Leave "No. Referensi" blank when creating
-   System generates: `INC-202511-0001` (income) or `EXP-202511-0001` (expense)
-   Format: `{TYPE}-{YYYYMM}-{sequence}`

### Auto-Generated Category Codes

-   Type category name
-   Select type (Income/Expense)
-   Code auto-fills with pattern: `INC-XXX` or `EXP-XXX`
-   Can manually edit if needed

### Currency Formatting

-   Type amount: `5000000` or `5.000.000`
-   System auto-formats to: `5.000.000`
-   Saves as: `5000000.00` in database

## 🔐 Permissions

### Admin Only

-   All transaction and category management
-   Verify/unverify transactions
-   Delete categories and transactions

### Edit/Delete Restrictions

-   **Verified transactions**: Cannot edit or delete (must unverify first)
-   **Categories with transactions**: Cannot delete
-   **Categories with children**: Cannot delete

## 📄 Document Upload

### Supported Formats

-   PDF documents
-   JPG/JPEG images
-   PNG images

### Size Limit

-   Maximum: 2MB per file

### Storage Location

-   `storage/app/public/transaction-documents/`
-   Files named: `TRX_{timestamp}_{original_name}`

## 🎨 Status Badges

### Transaction Types

-   🟢 **Green Badge**: Pendapatan (Income)
-   🔴 **Red Badge**: Beban (Expense)

### Verification Status

-   🟢 **Green Badge**: Terverifikasi (Verified)
-   🟡 **Yellow Badge**: Belum Verifikasi (Unverified)

### Category Status

-   🟢 **Green Badge**: Aktif (Active)
-   ⚪ **Gray Badge**: Tidak Aktif (Inactive)

## 📊 Reports Integration

### Laba Rugi (Profit/Loss) Report

-   Automatically uses transaction data
-   Filter by date range
-   Shows income, expense, and net profit/loss
-   Export to PDF
-   View charts and trends

### Transaction Summary

-   View at bottom of transaction list
-   Shows total: Surplus (income > expense) or Deficit (expense > income)
-   Color coded: Green (surplus) or Red (deficit)

## 🔍 Search & Sort

### Transaction List

-   Click column headers to sort:
    -   Transaction Date
    -   Reference Number
    -   Amount
-   Use filters for advanced search

### Category List

-   Click column headers to sort:
    -   Code
    -   Name
    -   Type
    -   Order

## ⚠️ Important Notes

1. **Always verify transactions** after checking data
2. **Cannot edit verified transactions** - unverify first if needed
3. **Upload documents** for audit trail
4. **Link to funding requests** when applicable
5. **Use correct category** for accurate reports
6. **Check totals** before verifying batch transactions

## 🐛 Troubleshooting

### "Transaksi yang sudah diverifikasi tidak dapat diedit"

-   Transaction is verified
-   Click "Batalkan Verifikasi" first

### "Kategori tidak dapat dihapus karena memiliki transaksi"

-   Category has transactions
-   Cannot delete, only deactivate

### Document upload fails

-   Check file size (max 2MB)
-   Check format (PDF, JPG, PNG only)
-   Check storage permissions

### Reference number already exists

-   Auto-generation creates unique numbers
-   If manual entry, check for duplicates

## 📞 Support

For issues or questions:

1. Check this guide
2. Check TRANSACTION_CRUD_COMPLETE.md for detailed info
3. Check LABA_RUGI_README.md for report features
4. Contact system administrator

---

**Last Updated**: November 11, 2025
**Version**: 1.0.0
