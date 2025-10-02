<?php

namespace Modules\EBilling\Http\Controllers;

use App\Http\Integrations\WhatsApp\Requests\Wablas\DeviceInfoRequest;
use App\Http\Integrations\WhatsApp\WablasConnector;
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

            if ($data['provider'] === 'wablas') {
                $settings->wablas_server = $data['wablas_server'] ?? '';
                $settings->wablas_api_key = $data['wablas_api_key'] ?? '';
                $settings->wablas_secret_key = $data['wablas_secret_key'] ?? '';
            }

            if (isset($data['invoice_reminder_template'])) {
                $settings->invoice_reminder_template = $data['invoice_reminder_template'];
            }
            $settings->save();
        }

        return redirect()->route('e-billing.settings.show', ['group' => $group->value])
            ->with('success', 'Pengaturan berhasil diperbarui.');
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
                    'provider' => 'required|in:waha,wablas',
                    'wablas_server' => 'nullable|required_if:provider,wablas|string',
                    'wablas_api_key' => 'nullable|required_if:provider,wablas|string',
                    'wablas_secret_key' => 'nullable|required_if:provider,wablas|string',
                    'invoice_reminder_template' => 'required|string|max:500',
                ];
            default:
                return [];
        }
    }

    /**
     * Fetch Wablas device info for testing connection
     */
    public function whatsappDeviceInfo(Request $request)
    {
        try {
            $connector = new WablasConnector(
                server: $request->input('server'),
                apiKey: $request->input('api_key'),
                secretKey: $request->input('secret_key')
            );
            $response = $connector->send(new DeviceInfoRequest);
            $deviceInfo = $response->dto();

            return response()->json($deviceInfo);
        } catch (\Exception $e) {
            logger()->error('Failed to fetch Wablas device info: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mendapatkan informasi device',
            ], 400);
        }
    }
}
