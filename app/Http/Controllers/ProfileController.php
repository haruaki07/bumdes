<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteProfileRequest;
use App\Models\WargaProfile;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the form for completing the profile.
     */
    public function complete()
    {
        $user = Auth::user();

        // Redirect if profile is already completed
        if ($user->hasCompletedProfile()) {
            return redirect()->route('dashboard')->with('info', 'Profil Anda sudah lengkap.');
        }

        $profile = $user->wargaProfile;

        return view('profile.complete', compact('profile'));
    }

    /**
     * Store the completed profile information.
     */
    public function store(CompleteProfileRequest $request)
    {
        $user = Auth::user();

        // Create or update the warga profile
        $profile = WargaProfile::updateOrCreate(
            ['user_id' => $user->id],
            $request->validated()
        );

        return redirect()->route('dashboard')->with('success', 'Profil berhasil dilengkapi!');
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->wargaProfile;

        if (! $profile) {
            return redirect()->route('profile.complete');
        }

        return view('profile.edit', compact('profile'));
    }

    /**
     * Update the profile information.
     */
    public function update(CompleteProfileRequest $request)
    {
        $user = Auth::user();

        $profile = $user->wargaProfile;

        if (! $profile) {
            return redirect()->route('profile.complete');
        }

        $profile->update($request->validated());

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }
}
