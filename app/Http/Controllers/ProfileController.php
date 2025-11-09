<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteProfileRequest;
use App\Models\WargaProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        // For warga role, require completed profile
        if ($user->role === 'warga' && ! $profile) {
            return redirect()->route('profile.complete');
        }

        return view('profile.edit', compact('user', 'profile'));
    }

    /**
     * Update the user's basic account information.
     */
    public function update(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Informasi akun berhasil diperbarui!');
    }

    /**
     * Update the warga profile information.
     */
    public function updateWargaProfile(CompleteProfileRequest $request)
    {
        $user = Auth::user();

        // Only warga can update warga profile
        if ($user->role !== 'warga') {
            abort(403, 'Anda tidak memiliki akses ke fitur ini.');
        }

        $profile = $user->wargaProfile;

        if (! $profile) {
            // Create if not exists
            $profile = WargaProfile::create(array_merge(
                ['user_id' => $user->id],
                $request->validated()
            ));
        } else {
            $profile->update($request->validated());
        }

        return redirect()->back()->with('success', 'Profil warga berhasil diperbarui!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('success', 'Password berhasil diperbarui!');
    }
}
