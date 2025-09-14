<?php

namespace Modules\EBilling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\EBilling\Enums\SettingsGroup;
use Modules\EBilling\Settings\EBillingBusinessProfileSettings;
use Modules\EBilling\Settings\EBillingWhatsappSettings;
use Modules\EBilling\Settings\EBillingXenditSettings;

class SettingsController
{
    public function index()
    {
        return redirect(route('e-billing.settings.show', ['group' => 'account']));
    }

    public function show(string $group)
    {
        $group = SettingsGroup::tryFrom($group);
        if (! $group) {
            abort(404);
        }

        $settings = match ($group) {
            SettingsGroup::BUSINESS_PROFILE => app(EBillingBusinessProfileSettings::class),
            SettingsGroup::PAYMENT_GATEWAY => app(EBillingXenditSettings::class),
            SettingsGroup::WHATSAPP => app(EBillingWhatsappSettings::class),
            default => null
        };

        return view('e-billing::settings.'.$group->value, [
            'group' => $group,
            'settings' => $settings ?? null,
        ]);
    }

    public function update(Request $request, $group)
    {
        $group = SettingsGroup::tryFrom($group);
        if (! $group) {
            abort(404);
        }

        $data = $request->validate($this->getValidationRules($group));

        if ($group === SettingsGroup::ACCOUNT) {
            $user = $request->user('ebil');
            $user->name = $data['name'];
            $user->email = $data['email'];
            if (! empty($data['new_password'])) {
                $user->password = Hash::make($data['new_password']);
            }
            $user->save();
        } elseif ($group === SettingsGroup::BUSINESS_PROFILE) {
            $settings = app(EBillingBusinessProfileSettings::class);
            $settings->name = $data['name'];
            $settings->description = $data['description'];
            $settings->phone = $data['phone'];
            $settings->email = $data['email'];
            $settings->address = $data['address'];

            if ($request->hasFile('logo')) {
                $path = Storage::url(Storage::disk('public')->putFileAs(
                    'profile_images',
                    $request->file('logo'),
                    time().'_'.strtolower($request->file('logo')->getClientOriginalName())
                ));
                $settings->logo = $path;
            }

            $settings->save();
        } elseif ($group === SettingsGroup::PAYMENT_GATEWAY) {
            $settings = app(EBillingXenditSettings::class);
            $settings->secret = $data['secret'];
            $settings->webhook_token = $data['webhook_token'];
            $settings->save();
        } elseif ($group === SettingsGroup::WHATSAPP) {
            $settings = app(EBillingWhatsappSettings::class);
            $settings->enabled = (bool) ($data['enabled'] ?? false);
            $settings->provider = $data['provider'];
            $settings->phoneNumber = $data['phoneNumber'] ?? '';
            // WAHA
            $settings->waha_base_url = $data['waha_base_url'] ?? null;
            $settings->waha_api_key = $data['waha_api_key'] ?? null;
            $settings->waha_session = $data['waha_session'] ?? 'default';
            $settings->save();
        }

        return redirect()->route('e-billing.settings.show', ['group' => $group->value])
            ->with('success', 'Settings updated successfully.');
    }

    protected function getValidationRules(SettingsGroup $group)
    {
        switch ($group) {
            case SettingsGroup::ACCOUNT:
                return [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'old_password' => 'nullable|current_password:ebil',
                    'new_password' => 'nullable|string|max:255|confirmed',
                ];
            case SettingsGroup::BUSINESS_PROFILE:
                return [
                    'name' => 'required|string|max:100',
                    'logo' => 'file|mimes:jpg,png,svg|max:1024',
                    'description' => 'required|string|max:100',
                    'phone' => 'required|string|max:20',
                    'email' => 'required|email|max:255',
                    'address' => 'required|string|max:255',
                ];
            case SettingsGroup::PAYMENT_GATEWAY:
                return [
                    'secret' => 'required|string',
                    'webhook_token' => 'required|string',
                ];
            case SettingsGroup::WHATSAPP:
                return [
                    'enabled' => 'nullable|boolean',
                    'provider' => 'required|in:waha',
                ];
            default:
                return [];
        }
    }
}
