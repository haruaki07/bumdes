# Funding Request Feature - Implementation Summary

## Overview

This document summarizes the implementation of the Business Funding Request feature (Modul B – Pendanaan Usaha) based on user stories US-B1, US-B2, and US-B3.

## ✅ Completed Work

### 1. Database Schema & Models

#### **FundingRequest Model** (Enhanced)

-   **Location**: `app/Models/FundingRequest.php`
-   **New Fields Added**:
    -   `approved_by`, `approved_at` - Track who and when approved
    -   `rejected_by`, `rejected_at` - Track who and when rejected
    -   `mou_uploaded_by`, `mou_uploaded_at` - Track MOU upload
    -   `mou_signed_at` - Track when warga signed MOU
    -   `interest_rate` - Interest rate percentage (configurable by admin/operator)
    -   `repayment_duration_months` - Flexible repayment duration
-   **New Relationships**:
    -   `approvedBy()` - User who approved
    -   `rejectedBy()` - User who rejected
    -   `mouUploadedBy()` - User who uploaded MOU
    -   `timelines()` - Timeline history
    -   `disbursements()` - Disbursement records
    -   `repayments()` - Repayment records
-   **Helper Methods**:
    -   `getTotalRepaidAttribute()` - Calculate total repaid amount
    -   `getRemainingAmountAttribute()` - Calculate remaining balance
    -   `isFullyRepaid()` - Check if fully paid

#### **FundingDisbursement Model** (New)

-   **Location**: `app/Models/FundingDisbursement.php`
-   **Migration**: `2025_11_09_000001_create_funding_disbursements_table.php`
-   **Purpose**: Track actual fund disbursements (US-B3)
-   **Fields**:
    -   `funding_request_id` - Link to funding request
    -   `disbursed_by` - Operator who disbursed
    -   `disbursement_date` - When funds were released
    -   `amount` - Amount disbursed
    -   `payment_method` - transfer/tunai
    -   `bank_name`, `account_number`, `account_holder_name` - Transfer details
    -   `notes` - Additional notes
    -   `proof_document` - Disbursement proof (optional)

#### **FundingRepayment Model** (New)

-   **Location**: `app/Models/FundingRepayment.php`
-   **Migration**: `2025_11_09_000002_create_funding_repayments_table.php`
-   **Purpose**: Track installment payments (US-B3)
-   **Fields**:
    -   `funding_request_id` - Link to funding request
    -   `payment_date` - Payment date
    -   `amount` - Repayment amount (flexible)
    -   `payment_method` - transfer/tunai/lainnya
    -   `proof_document` - Proof of payment (scan + materai)
    -   `verified_by`, `verified_at` - Operator verification
    -   `notes` - Additional notes
-   **Methods**:
    -   `isVerified()` - Check if payment is verified

#### **FundingRequestTimeline Model** (New)

-   **Location**: `app/Models/FundingRequestTimeline.php`
-   **Migration**: `2025_11_09_000003_create_funding_request_timelines_table.php`
-   **Purpose**: Track all status changes and actions
-   **Fields**:
    -   `funding_request_id` - Link to funding request
    -   `action` - Action type (enum)
    -   `description` - Action description
    -   `performed_by` - User who performed action
    -   `metadata` - Additional data (JSON)

### 2. Enums

#### **FundingRequestStatus** (New)

-   **Location**: `app/Enums/FundingRequestStatus.php`
-   **Statuses**:
    1. `SUBMITTED` - Pengajuan dibuat (US-B1)
    2. `APPROVED` - Disetujui admin (US-B1)
    3. `MOU_SIGNED` - MOU ditandatangani warga (US-B2)
    4. `READY_TO_DISBURSE` - Siap dicairkan (US-B2)
    5. `DISBURSED` - Dana dicairkan (US-B3)
    6. `REPAYING` - Dalam masa cicilan (US-B3)
    7. `COMPLETED` - Lunas
    8. `REJECTED` - Ditolak (US-B1)
-   **Methods**: `label()`, `color()`, `fromString()`

#### **FundingRequestTimelineAction** (New)

-   **Location**: `app/Enums/FundingRequestTimelineAction.php`
-   **Actions**: SUBMITTED, APPROVED, REJECTED, MOU_UPLOADED, MOU_SIGNED, DISBURSED, REPAYMENT_MADE, COMPLETED
-   **Methods**: `label()`, `description()`, `icon()`, `color()`, `fromString()`

### 3. Updated Models

#### **User Model**

-   **New Relationships**:
    -   `fundingRequests()` - All funding requests by user
    -   `approvedFundingRequests()` - Requests approved by this user
    -   `rejectedFundingRequests()` - Requests rejected by this user
    -   `disbursements()` - Disbursements made by this user (operator)
    -   `verifiedRepayments()` - Repayments verified by this user (operator)

#### **Business Model**

-   Already has `fundingRequests()` relationship - verified ✓

### 4. Policy & Authorization

#### **FundingRequestPolicy** (New)

-   **Location**: `app/Policies/FundingRequestPolicy.php`
-   **Permissions**:
    -   `viewAny()` - Authenticated users (warga see own, admin/operator see all)
    -   `view()` - Owner or admin/operator
    -   `create()` - Warga only (with completed profile)
    -   `update()` - Owner only (submitted status only)
    -   `delete()` - Owner only (submitted status only)
    -   `approve()` - Admin/operator (submitted status)
    -   `reject()` - Admin/operator (submitted status)
    -   `uploadMou()` - Admin/operator (approved status) - US-B2
    -   `signMou()` - Owner (approved status, MOU uploaded) - US-B2
    -   `disburse()` - Operator (MOU signed) - US-B3
    -   `recordRepayment()` - Owner (disbursed/repaying status) - US-B3
    -   `verifyRepayment()` - Operator (disbursed/repaying status)

### 5. Notifications (Email)

#### Created Notifications:

1. **FundingRequestSubmitted** - To admin/operator when warga submits
2. **FundingRequestApproved** - To warga when approved
3. **FundingRequestRejected** - To warga when rejected
4. **MouUploaded** - To warga when MOU is uploaded by admin
5. **DisbursementCompleted** - To warga when funds are disbursed

All notifications support:

-   Email delivery via Laravel Mail
-   Database storage for in-app notifications
-   Queue processing (implements ShouldQueue)

### 6. Database Migration

✅ **Successfully Migrated** (All 4 migrations ran without errors):

-   `2025_11_09_000001_create_funding_disbursements_table`
-   `2025_11_09_000002_create_funding_repayments_table`
-   `2025_11_09_000003_create_funding_request_timelines_table`
-   `2025_11_09_000004_update_funding_requests_table`

---

## 🚧 Remaining Work (To Be Completed)

The following components still need to be implemented to complete the feature:

### 1. Controller

-   **FundingRequestController** with methods:
    -   `index()` - List funding requests (filtered by role)
    -   `create()` - Show submission form (US-B1)
    -   `store()` - Process submission (US-B1)
    -   `show()` - View details with timeline
    -   `approve()` - Approve request (US-B1)
    -   `reject()` - Reject request (US-B1)
    -   `uploadMou()` - Upload MOU document (US-B2)
    -   `signMou()` - Warga signs MOU (US-B2)
    -   `disburse()` - Record disbursement (US-B3)
    -   `storeRepayment()` - Record repayment (US-B3)

### 2. Form Request Validators

Need to create validation classes:

-   `StoreFundingRequestRequest` - Validate submission
-   `ApproveFundingRequestRequest` - Validate approval
-   `RejectFundingRequestRequest` - Validate rejection
-   `UploadMouRequest` - Validate MOU upload
-   `SignMouRequest` - Validate MOU signature
-   `DisburseFundingRequestRequest` - Validate disbursement
-   `StoreRepaymentRequest` - Validate repayment

### 3. Routes

Update `routes/web.php` with:

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('funding-requests', FundingRequestController::class);
    Route::post('/funding-requests/{fundingRequest}/approve', [FundingRequestController::class, 'approve'])
        ->name('funding-requests.approve');
    Route::post('/funding-requests/{fundingRequest}/reject', [FundingRequestController::class, 'reject'])
        ->name('funding-requests.reject');
    Route::post('/funding-requests/{fundingRequest}/upload-mou', [FundingRequestController::class, 'uploadMou'])
        ->name('funding-requests.upload-mou');
    Route::post('/funding-requests/{fundingRequest}/sign-mou', [FundingRequestController::class, 'signMou'])
        ->name('funding-requests.sign-mou');
    Route::post('/funding-requests/{fundingRequest}/disburse', [FundingRequestController::class, 'disburse'])
        ->name('funding-requests.disburse');
    Route::post('/funding-requests/{fundingRequest}/repayments', [FundingRequestController::class, 'storeRepayment'])
        ->name('funding-requests.store-repayment');
});
```

### 4. Blade Views

Create views in `resources/views/funding-requests/`:

-   `index.blade.php` - List with filters (role-based)
-   `create.blade.php` - Submission form (US-B1)
-   `show.blade.php` - Detail view with timeline and actions
-   `_approve_modal.blade.php` - Approval modal
-   `_reject_modal.blade.php` - Rejection modal
-   `_upload_mou_modal.blade.php` - MOU upload modal (US-B2)
-   `_sign_mou_modal.blade.php` - MOU signature modal (US-B2)
-   `_disburse_modal.blade.php` - Disbursement modal (US-B3)
-   `_repayment_modal.blade.php` - Repayment modal (US-B3)

### 5. Blade Components

Create components in `resources/views/components/modules/funding-request/`:

-   `status-badge.blade.php` - Status badge with colors
-   `timeline-item.blade.php` - Timeline entry display
-   `repayment-history.blade.php` - Repayment list with status

### 6. Navigation Menu

Add menu items for:

-   **Warga**: "Pengajuan Pendanaan Saya" / "My Funding Requests"
-   **Admin/Operator**: "Semua Pengajuan Pendanaan" / "All Funding Requests"

---

## 💡 Key Features Implemented

### US-B1: Pengajuan Pendanaan (Funding Request Submission)

✅ Database schema ready
✅ Status flow: submitted → approved/rejected
✅ Validation rules in policy (warga only, requires completed profile)
✅ Notifications ready (submitted, approved, rejected)
⏳ UI pending (form, approval/rejection modals)

### US-B2: MOU Digital (Digital MOU Agreement)

✅ Database fields for MOU tracking
✅ Status flow: approved → mou_uploaded → mou_signed → ready_to_disburse
✅ Policy permissions (admin uploads, warga signs)
✅ Support for scanned document upload with signature + materai
✅ Notifications ready (MOU uploaded)
⏳ UI pending (upload modal, sign modal with file upload)

### US-B3: Pencairan Dana (Fund Disbursement & Repayment)

✅ Complete disbursement tracking model
✅ Flexible repayment system (warga can pay any amount anytime)
✅ Admin/operator can configure interest rate and duration
✅ Verification system for repayments
✅ Automatic calculation of remaining balance
✅ Timeline tracking for transparency
✅ Notifications ready (disbursement completed)
⏳ UI pending (disbursement form, repayment upload, history display)

---

## 🔐 Security & Authorization

-   **Role-based access**: Warga, Operator, Admin
-   **Policy-driven authorization** on all actions
-   **Profile completion check** before submission
-   **Business ownership validation**
-   **Status-based action restrictions**
-   **Soft deletes** for data recovery
-   **Audit trail** via timeline

---

## 📧 Notification Flow

1. Warga submits → Email to Admin/Operator
2. Admin approves → Email to Warga
3. Admin uploads MOU → Email to Warga
4. Warga signs MOU → Status updated
5. Operator disburses → Email to Warga
6. Warga uploads repayment → Operator verifies

---

## 🎨 UI Design Guidelines

When implementing the UI:

1. Follow existing patterns from Business/BusinessRegistration modules
2. Use Tabler UI components (already in project)
3. Implement responsive tables with filters
4. Use modal dialogs for actions
5. Show timeline vertically with icons and colors
6. Display status badges with appropriate colors
7. Include file upload with preview
8. Add confirmation dialogs for destructive actions

---

## 📝 Next Steps

1. **Priority High**: Create FundingRequestController
2. **Priority High**: Create form request validators
3. **Priority High**: Create main views (index, create, show)
4. **Priority Medium**: Create action modals
5. **Priority Medium**: Create Blade components
6. **Priority Low**: Add navigation menu items
7. **Testing**: Test all workflows end-to-end

---

## 🛠 Development Notes

-   All models use `Datatable` trait for easy listing
-   Enum casts are used for type safety
-   Foreign keys have proper constraints (cascade/restrict)
-   Timestamps track when actions occur
-   JSON metadata in timeline allows flexible data storage
-   Interest rate and repayment duration are configurable (flexible as requested)
-   Repayment system supports partial payments
-   All monetary values use `decimal(15, 2)` for precision

---

## 📚 Related Files

### Models

-   `app/Models/FundingRequest.php`
-   `app/Models/FundingDisbursement.php`
-   `app/Models/FundingRepayment.php`
-   `app/Models/FundingRequestTimeline.php`
-   `app/Models/User.php` (updated)
-   `app/Models/Business.php` (verified)

### Enums

-   `app/Enums/FundingRequestStatus.php`
-   `app/Enums/FundingRequestTimelineAction.php`

### Policies

-   `app/Policies/FundingRequestPolicy.php`

### Notifications

-   `app/Notifications/FundingRequestSubmitted.php`
-   `app/Notifications/FundingRequestApproved.php`
-   `app/Notifications/FundingRequestRejected.php`
-   `app/Notifications/MouUploaded.php`
-   `app/Notifications/DisbursementCompleted.php`

### Migrations

-   `database/migrations/2025_11_09_000001_create_funding_disbursements_table.php`
-   `database/migrations/2025_11_09_000002_create_funding_repayments_table.php`
-   `database/migrations/2025_11_09_000003_create_funding_request_timelines_table.php`
-   `database/migrations/2025_11_09_000004_update_funding_requests_table.php`

---

**Status**: Backend foundation complete ✅ | Frontend implementation pending ⏳
**Last Updated**: November 9, 2025
