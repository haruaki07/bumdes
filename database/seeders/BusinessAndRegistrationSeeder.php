<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessRegistration;
use Illuminate\Database\Seeder;

class BusinessAndRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        // Create 10 business registrations
        $registrations = BusinessRegistration::factory()->count(10)->create();

        // For each registration, create a timeline entry for creation
        $registrations->each(function ($registration) {
            $registration->timeline()->create([
                'action' => \App\Enums\BusinessRegistrationTimelineAction::SUBMITTED,
                'performed_by' => $registration->applicant_id,
            ]);
        });

        // For each approved registration, create a business and timeline entry for approval
        $registrations->where('status', 'approved')->each(function ($registration) {
            $business = Business::factory()->create([
                'name' => $registration->name,
                'business_type_id' => $registration->business_type_id,
                'owner_id' => $registration->applicant_id,
                'description' => $registration->description,
                'location' => $registration->location,
                'contact_phone' => $registration->contact_phone,
                'contact_email' => $registration->contact_email,
                'status' => 'active',
            ]);
            $registration->timeline()->create([
                'action' => \App\Enums\BusinessRegistrationTimelineAction::APPROVED,
                'performed_by' => $registration->applicant_id,
                'metadata' => ['business_id' => $business->id],
            ]);
        });

        // For each rejected registration, create a timeline entry for rejection
        $registrations->where('status', 'rejected')->each(function ($registration) {
            $registration->timeline()->create([
                'action' => \App\Enums\BusinessRegistrationTimelineAction::REJECTED,
                'performed_by' => $registration->applicant_id,
                'metadata' => ['rejection_reason' => $registration->rejection_reason],
            ]);
        });
    }
}
