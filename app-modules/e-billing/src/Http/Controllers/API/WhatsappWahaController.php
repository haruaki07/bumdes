<?php

namespace Modules\EBilling\Http\Controllers\API;

use CCK\LaravelWahaSaloonSdk\Waha\Waha;
use Illuminate\Http\Request;
use Modules\EBilling\Settings\EBillingWhatsappSettings;
use Saloon\Exceptions\Request\RequestException;

class WhatsappWahaController
{
    public function __construct(
        protected EBillingWhatsappSettings $settings
    ) {}

    protected function makeWaha()
    {
        if ($this->settings->provider !== 'waha' || empty($this->settings->waha_session)) {
            throw new \RuntimeException('WhatsApp via Waha is not properly configured.');
        }

        $waha = new Waha(
            config('services.waha.base_url'),
            config('services.waha.api_key'),
        );

        return $waha;
    }

    public function status(Request $request)
    {
        try {
            $waha = $this->makeWaha();
            $res = $waha->sessions()->getSessionInformation($this->settings->waha_session);

            return response()->json($res->json(), $res->status());
        } catch (RequestException $e) {
            return response()->json(['error' => 'Failed to get status', 'message' => $e->getMessage()], 500);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Unexpected error', 'message' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $waha = $this->makeWaha();
            $res = $waha->sessions()->logoutFromTheSession($this->settings->waha_session);

            return response()->json($res->json() ?? ['ok' => $res->successful()], $res->status());
        } catch (RequestException $e) {
            return response()->json(['error' => 'Failed to logout', 'message' => $e->getMessage()], 500);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Unexpected error', 'message' => $e->getMessage()], 500);
        }
    }

    public function qr(Request $request)
    {
        try {
            $waha = $this->makeWaha();

            // Try to get binary PNG first
            $res = $waha->auth()->getQrCodeImage($this->settings->waha_session);
            if ($res->successful() && $res->header('Content-Type') === 'image/png') {
                return response($res->body(), 200)->header('Content-Type', 'image/png');
            }

            // Fallback to base64 JSON and decode
            $res = $waha->auth()->getQrCodeBase64($this->settings->waha_session);
            if ($res->successful()) {
                $data = $res->json();
                $base64 = $data['data'] ?? null;
                if ($base64) {
                    return response(base64_decode($base64), 200)->header('Content-Type', $data['mimetype'] ?? 'image/png');
                }
            }

            // Final fallback: raw value -> generate QR on client is not possible via <img>, return text
            $raw = $waha->auth()->getQrCodeRaw($this->settings->waha_session);

            return response()->json($raw->json(), $raw->status());
        } catch (RequestException $e) {
            return response('Failed to load QR: '.$e->getMessage(), 500);
        } catch (\Throwable $e) {
            return response('Unexpected error: '.$e->getMessage(), 500);
        }
    }

    public function requestCode(Request $request)
    {
        $data = $request->validate([
            'phoneNumber' => 'required|string',
        ]);

        try {
            return response()->json(['code' => 'J9P6-K71C'], 200);
            $waha = $this->makeWaha();
            $res = $waha->auth()->requestAuthenticationCode($this->settings->waha_session, $data['phoneNumber'], null);

            return response()->json($res->json(), $res->status());
        } catch (RequestException $e) {
            return response()->json(['error' => 'Failed to request code', 'message' => $e->getMessage()], 500);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Unexpected error', 'message' => $e->getMessage()], 500);
        }
    }
}
