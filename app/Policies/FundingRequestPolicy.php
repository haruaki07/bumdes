<?php

namespace App\Policies;

use App\Enums\FundingRequestStatus;
use App\Models\FundingRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FundingRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        // Authenticated users can view list (warga see own, admin/operator see all)
        return $user !== null;
    }

    public function view(?User $user, FundingRequest $fundingRequest): bool
    {
        if (! $user) {
            return false;
        }

        // Admin/operator can view all, warga can only view own
        return $user->role === 'admin' ||
            $user->role === 'operator' ||
            $fundingRequest->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        // Only warga can create funding requests and must have completed profile
        return $user->role === 'warga' && $user->hasCompletedProfile();
    }

    public function update(User $user, FundingRequest $fundingRequest): bool
    {
        // Warga can only update their own submitted requests
        return $user->id === $fundingRequest->user_id &&
            $fundingRequest->status === FundingRequestStatus::SUBMITTED;
    }

    public function delete(User $user, FundingRequest $fundingRequest): bool
    {
        // Warga can delete their own submitted requests
        return $user->id === $fundingRequest->user_id &&
            $fundingRequest->status === FundingRequestStatus::SUBMITTED;
    }

    public function approve(User $user, FundingRequest $fundingRequest): bool
    {
        // Admin/operator can approve submitted requests
        return ($user->role === 'admin' || $user->role === 'operator') &&
            $fundingRequest->status === FundingRequestStatus::SUBMITTED;
    }

    public function reject(User $user, FundingRequest $fundingRequest): bool
    {
        // Admin/operator can reject submitted requests
        return ($user->role === 'admin' || $user->role === 'operator') &&
            $fundingRequest->status === FundingRequestStatus::SUBMITTED;
    }

    public function uploadMou(User $user, FundingRequest $fundingRequest): bool
    {
        // Admin/operator can upload MOU for approved requests
        return ($user->role === 'admin' || $user->role === 'operator') &&
            $fundingRequest->status === FundingRequestStatus::APPROVED;
    }

    public function signMou(User $user, FundingRequest $fundingRequest): bool
    {
        // Warga (owner) can sign MOU after it's uploaded
        return $user->id === $fundingRequest->user_id &&
            $fundingRequest->status === FundingRequestStatus::APPROVED &&
            ! empty($fundingRequest->mou_document);
    }

    public function disburse(User $user, FundingRequest $fundingRequest): bool
    {
        // Operator can disburse funds after MOU is signed
        return ($user->role === 'operator' || $user->role === 'admin') &&
            ($fundingRequest->status === FundingRequestStatus::MOU_SIGNED ||
                $fundingRequest->status === FundingRequestStatus::READY_TO_DISBURSE);
    }

    public function recordRepayment(User $user, FundingRequest $fundingRequest): bool
    {
        // Warga (owner) can record repayment for disbursed or repaying status
        return $user->id === $fundingRequest->user_id &&
            ($fundingRequest->status === FundingRequestStatus::DISBURSED ||
                $fundingRequest->status === FundingRequestStatus::REPAYING);
    }

    public function verifyRepayment(User $user, FundingRequest $fundingRequest): bool
    {
        // Operator can verify repayments
        return ($user->role === 'operator' || $user->role === 'admin') &&
            ($fundingRequest->status === FundingRequestStatus::DISBURSED ||
                $fundingRequest->status === FundingRequestStatus::REPAYING);
    }
}
