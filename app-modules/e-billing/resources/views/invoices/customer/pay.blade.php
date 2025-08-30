@extends('tablar::auth.layout')

@section('title', 'Checkout Pembayaran')

@section('tablar_css')
  <style>
    .qr-box {
      width: 260px;
      height: 260px;
      padding: 12px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
    }

    .mono {
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
  </style>
@endsection

@section('content')
  <div class="container container-tight py-4">
    <div class="card card-md">
      <div class="card-body p-4">
        @if (!empty($error))
          <div class="alert alert-danger" role="alert">{{ $error }}</div>
        @else
          @php
            $s = $session;
            $action = $s['action'] ?? [];
            $actionType = $action['type'] ?? null;
            $descriptor = $action['descriptor'] ?? null;
            $value = $action['value'] ?? null;
            $expiresAt = isset($s['expires_at']) ? \Carbon\CarbonImmutable::parse($s['expires_at']) : null;

            $referenceId = $s['reference_id'] ?? '-';
            $amount = $s['amount'] ?? 0;
            $amountFormatted = number_format($amount, 0, ',', '.');
            $customerId = $s['customer']['customer_id'] ?? '';
          @endphp

          <div class="d-flex align-items-center mb-3">
            <div class="me-auto">
              <div class="text-secondary">Invoice</div>
              <h3 class="mb-0">{{ $referenceId }}</h3>
            </div>
            <div class="text-end">
              <div class="text-secondary">Jumlah</div>
              <h3 class="mb-0">Rp {{ $amountFormatted }}</h3>
            </div>
          </div>

          <div class="alert alert-info d-flex gap-2" role="alert" id="countdownAlert">
            <i class="ti ti-info-circle alert-icon"></i>
            <div>
              Selesaikan pembayaran sebelum
              <strong>{{ $expiresAt ? $expiresAt->locale('id')->translatedFormat('d F Y H:i') : '-' }}</strong>
              <span id="countdown" class="ms-1"></span>
            </div>
          </div>

          {{-- Handle action types --}}
          @if ($actionType === 'PRESENT_TO_CUSTOMER')
            @if ($descriptor === 'QR_STRING')
              <div class="text-center">
                <p class="mb-3">Scan QR berikut di aplikasi pembayaran Anda:</p>
                <div id="qr" class="qr-box d-inline-flex align-items-center justify-content-center"></div>
                <p class="small text-secondary mt-2">
                  Jika QR tidak muncul,
                  <button id="btnCopyQR" class="btn btn-link p-0 align-baseline">salin string</button>
                  lalu tempel di aplikasi pembayaran.
                </p>
              </div>
            @elseif (in_array($descriptor, ['VIRTUAL_ACCOUNT_NUMBER', 'PAYMENT_CODE']))
              <div class="text-center">
                <p class="mb-2">Gunakan nomor berikut untuk membayar:</p>
                <div class="h2 mono" id="codeBox">{{ $value }}</div>
                <button class="btn btn-outline-primary mt-2" id="btnCopyCode">
                  <i class="icon ti ti-copy"></i> Salin Nomor
                </button>
              </div>
            @else
              <div class="alert alert-warning">Aksi tidak didukung: {{ $descriptor }}</div>
            @endif
          @elseif ($actionType === 'REDIRECT_CUSTOMER')
            @if ($descriptor === 'WEB_URL' && filter_var($value, FILTER_VALIDATE_URL))
              <div class="text-center">
                <p class="mb-2">Anda akan diarahkan ke halaman pembayaran dalam <span id="redirSec">5</span> detik…</p>
                <a class="btn btn-primary" id="btnGoNow" href="{{ $value }}" rel="noopener" target="_blank">
                  Buka Sekarang
                </a>
              </div>
            @else
              <div class="alert alert-warning">Tautan tidak valid untuk pengalihan.</div>
            @endif
          @else
            <div class="alert alert-warning">Respon pembayaran tidak dapat ditangani. Silakan coba lagi.</div>
          @endif

          <div class="mt-4">
            <a href="{{ route('e-billing.invoice.customer-show', ['customer_id' => $customerId]) }}" class="btn"
              id="btnBack">
              Kembali ke Invoice
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection

@section('tablar_js')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const session = @json(collect($session)->only(['expires_at', 'action']) ?? null);
      const action = session?.action || {};
      const {
        type,
        descriptor,
        value
      } = action;

      const expiresAt = session?.expires_at ? new Date(session.expires_at) : null;
      const countdownEl = document.getElementById('countdown');
      let timerId;

      // Countdown
      function updateCountdown() {
        if (!expiresAt || !countdownEl) return;
        const now = new Date();
        const diff = Math.max(0, Math.floor((expiresAt - now) / 1000));
        const m = Math.floor(diff / 60);
        const s = diff % 60;
        countdownEl.textContent = `(sisa ${m}m ${s}s)`;

        if (diff <= 0) {
          clearInterval(timerId);
          countdownEl.textContent = '(kedaluwarsa)';
          disableButton('btnGoNow');
        }
      }
      updateCountdown();
      timerId = setInterval(updateCountdown, 1000);

      // Polling status
      const statusUrl = @json($session ? route('e-billing.invoice.pay.status', ['token' => request()->query('token')]) : null);
      async function pollStatus() {
        if (!statusUrl) return;
        try {
          const res = await fetch(statusUrl, {
            headers: {
              'Accept': 'application/json'
            }
          });
          if (!res.ok) return;
          const data = await res.json();

          if (['SUCCEEDED', 'AUTHORIZED'].includes(data.payment_status)) {
            showAlert('success', 'Pembayaran berhasil. Anda dapat menutup halaman ini.');
            document.getElementById('btnBack')?.href =
              `{{ route('e-billing.invoice.customer-show', ['customer_id' => $session['reference_id']]) }}`;
            clearInterval(timerId);
            return;
          }
          if (data.payment_status === 'EXPIRED' || data.expired) {
            showAlert('warning', 'Sesi pembayaran telah kedaluwarsa. Silakan buat permintaan baru.');
            return;
          }
          setTimeout(pollStatus, 5000);
        } catch {
          setTimeout(pollStatus, 7000);
        }
      }
      setTimeout(pollStatus, 5000);

      // QR Rendering
      if (type === 'PRESENT_TO_CUSTOMER' && descriptor === 'QR_STRING' && value) {
        const qrEl = document.getElementById('qr');
        if (qrEl && window.QRCode) {
          QRCode.toCanvas(value, {
            width: 236,
            margin: 0
          }, (err, canvas) => {
            if (!err) {
              qrEl.innerHTML = '';
              qrEl.appendChild(canvas);
            }
          });
        }
        bindCopy('btnCopyQR', value);
      }

      // Copy Code
      bindCopy('btnCopyCode', document.getElementById('codeBox')?.textContent?.trim());

      // Redirect
      if (type === 'REDIRECT_CUSTOMER' && descriptor === 'WEB_URL' && value) {
        const redirEl = document.getElementById('redirSec');
        const btnGo = document.getElementById('btnGoNow');
        let left = 5;
        const tick = () => {
          if (redirEl) redirEl.textContent = left;
          if (left <= 0) {
            btnGo?.click();
            return;
          }
          left--;
          setTimeout(tick, 1000);
        };
        tick();
      }

      // Helpers
      function bindCopy(btnId, text) {
        const btn = document.getElementById(btnId);
        if (btn && text && navigator.clipboard) {
          btn.addEventListener('click', async () => {
            try {
              await navigator.clipboard.writeText(text);
            } catch (_) {
              alert('Gagal menyalin.');
            }
          });
        }
      }

      function disableButton(id) {
        const el = document.getElementById(id);
        if (el) el.setAttribute('disabled', 'disabled');
      }

      function showAlert(type, message) {
        const wrapper = document.createElement('div');
        wrapper.className = `alert alert-${type} mt-3`;
        wrapper.textContent = message;
        document.querySelector('.card-body')?.appendChild(wrapper);
      }
    });
  </script>
@endsection
