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
  <div class="container container-narrow py-4 my-auto" style="max-width: 900px;">
    <div class="card">
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

          <div class="alert alert-info d-flex gap-2" role="alert" id="countdownAlert"
            @if ($descriptor === 'BANK_TRANSFER_DETAILS') style="display:none" @endif>
            <i class="ti ti-info-circle alert-icon"></i>
            <div>
              Selesaikan pembayaran sebelum
              <strong>{{ $expiresAt ? $expiresAt->locale('id')->translatedFormat('d F Y H:i') : '-' }}</strong>
              <span id="countdown" class="ms-1"></span>
            </div>
          </div>

          {{-- Handle action types --}}
          @if ($actionType === 'PRESENT_TO_CUSTOMER')
            @if ($descriptor === 'BANK_TRANSFER_DETAILS')
              @php
                $bank = $value['bank'] ?? 'Bank';
                $accNo = $value['account_number'] ?? '';
                $accName = $value['account_name'] ?? config('app.name');
                $note = $value['note'] ?? '';
              @endphp
              <div>
                <div class="alert alert-secondary" role="alert">
                  Silakan transfer sesuai detail berikut, lalu kirim bukti ke admin untuk konfirmasi.
                </div>
                <div class="row g-3">
                  <div class="col-md-4">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="text-secondary">Bank</div>
                        <div class="h4 mb-2">{{ $bank }}</div>
                        <div class="text-secondary">No. Rekening</div>
                        <div class="h4 mono" id="codeBox">{{ $accNo }}</div>
                        <button class="btn btn-outline-primary mt-2" id="btnCopyCode">
                          <i class="icon ti ti-copy"></i> Salin No. Rekening
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="row mb-2">
                          <div class="col-6 text-secondary">Nama Pemilik</div>
                          <div class="col-6 text-end">{{ $accName }}</div>
                        </div>
                        <div class="row mb-2">
                          <div class="col-6 text-secondary">Nominal</div>
                          <div class="col-6 text-end">Rp {{ $amountFormatted }}</div>
                        </div>
                        <div class="row">
                          <div class="col-6 text-secondary">Berita/Referensi</div>
                          <div class="col-6 text-end">{{ $referenceId }}</div>
                        </div>
                        @if ($note)
                          <hr>
                          <div class="small text-secondary">{{ $note }}</div>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card mt-3">
                  <div class="card-body">
                    <h4 class="card-title mb-3">Unggah Bukti Transfer</h4>
                    <form id="receiptForm" class="row g-2">
                      <div class="col-12 col-md-6">
                        <input type="file" name="file" id="receiptFile" class="form-control" accept="image/*,.pdf"
                          required>
                        <div class="form-hint">JPG, PNG, atau PDF. Maks 4MB.</div>
                      </div>
                      <div class="col-12 col-md-6">
                        <input type="text" name="note" class="form-control" placeholder="Catatan (opsional)">
                      </div>
                      <div class="col-12">
                        <button type="submit" class="btn btn-primary" id="btnUploadReceipt">
                          <i class="ti ti-upload"></i> Unggah Bukti
                        </button>
                        <span class="text-secondary small ms-2" id="uploadStatus"></span>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            @elseif ($descriptor === 'QR_STRING')
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

          <div id="instructionsSection" class="mt-4 d-none">
            <h4 class="mb-3">Cara Pembayaran</h4>
            <div id="instructionsTabs"></div>
          </div>

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
  <script id="sessionData" type="application/json">{!! json_encode(collect($session)->only(['expires_at', 'action', 'payment_method', 'reference_id', 'id'])) !!}</script>
  <script id="sessionMeta" type="application/json">{!! json_encode(collect($session)->only(['amount','fee','total_amount'])) !!}</script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const session = JSON.parse(document.getElementById('sessionData').textContent);
      const meta = JSON.parse(document.getElementById('sessionMeta').textContent || '{}');
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
      if (!(type === 'PRESENT_TO_CUSTOMER' && descriptor === 'BANK_TRANSFER_DETAILS')) {
        updateCountdown();
        timerId = setInterval(updateCountdown, 1000);
      }

      // Polling status
      const statusUrl = @json($session ? route('e-billing.invoice.pay.status', ['token' => request()->query('token')]) : null);
      async function pollStatus() {
        if (!statusUrl) return;
        // Do not poll for manual bank transfer; admin will confirm manually
        if (type === 'PRESENT_TO_CUSTOMER' && descriptor === 'BANK_TRANSFER_DETAILS') return;
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
            document.getElementById('btnBack').href =
              `{{ route('e-billing.invoice.customer-show', ['customer_id' => '_CUSTOMER_ID_']) }}`
              .replace('_CUSTOMER_ID_', session?.reference_id ?? '');
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

      // Payment Instructions
      (function loadPaymentInstructions() {
        const methodCode = session?.payment_method?.code;
        if (!methodCode) return;
        const baseUrl = @json(asset('assets/e-billing/payment_instructions'));
        const url = `${baseUrl}/${methodCode}.json`;

        // Determine full payment code placeholder value
        const fullPaymentCode = (descriptor === 'VIRTUAL_ACCOUNT_NUMBER' || descriptor === 'PAYMENT_CODE') ?
          (value || document.getElementById('codeBox')?.textContent?.trim() || '') :
          (document.getElementById('codeBox')?.textContent?.trim() || '');

        const accountNumber = (descriptor === 'BANK_TRANSFER_DETAILS') ? (value?.account_number || document
          .getElementById('codeBox')?.textContent?.trim() || '') : '';
        const accountName = (descriptor === 'BANK_TRANSFER_DETAILS') ? (value?.account_name || '') : '';
        const bankName = (descriptor === 'BANK_TRANSFER_DETAILS') ? (value?.bank || '') : '';

        const vars = {
          fullPaymentCode,
          iBankingSource: getIbankingUrl(methodCode),
          merchantName: "{{ 'E-Billing' }}", // for qris, should be using global config
          accountNumber,
          accountName,
          bankName,
          invoiceNumber: session?.reference_id || ''
        };

        fetch(url, {
            headers: {
              'Accept': 'application/json'
            }
          })
          .then(res => res.ok ? res.json() : null)
          .then(data => {
            if (!data || !data.instructions) return;
            renderInstructions(data.instructions, vars);
          })
          .catch(() => {});
      })();

      function getIbankingUrl(code) {
        // Minimal mapping; extend as needed
        const map = {
          'BCA_VIRTUAL_ACCOUNT': 'https://ibank.klikbca.com',
          'BNI_VIRTUAL_ACCOUNT': 'https://ibank.bni.co.id',
          'BSI_VIRTUAL_ACCOUNT': 'https://bsinet.bankbsi.co.id',
          'PERMATA_VIRTUAL_ACCOUNT': 'https://www.permatanet.com'
        };
        return map[code] || '#';
      }

      function applyVars(str, vars) {
        if (typeof str !== 'string') return '';
        return str.replace(/\{\{\s*(\w+)\s*\}\}/g, (_, k) => (vars[k] ?? ''));
      }

      function sanitizeHtml(input) {
        // allow only a, strong, em, br and text
        const wrapper = document.createElement('div');
        wrapper.innerHTML = input;
        const allowed = new Set(['A', 'STRONG', 'EM', 'BR']);

        (function walk(node) {
          const children = Array.from(node.childNodes);
          for (const child of children) {
            if (child.nodeType === Node.TEXT_NODE) continue;
            if (child.nodeType === Node.ELEMENT_NODE) {
              if (!allowed.has(child.tagName)) {
                // Replace disallowed element with its text content
                const text = document.createTextNode(child.textContent || '');
                node.replaceChild(text, child);
                continue;
              }
              if (child.tagName === 'A') {
                const href = child.getAttribute('href') || '';
                if (!/^https?:\/\//i.test(href) && href !== '#') {
                  child.removeAttribute('href');
                }
                child.setAttribute('rel', 'noopener');
                child.setAttribute('target', '_blank');
              }
              walk(child);
            } else {
              node.removeChild(child);
            }
          }
        })(wrapper);

        return wrapper.innerHTML;
      }

      function renderInstructions(instructions, vars) {
        // Support array-form (no categories) and object-form (with categories)
        if (Array.isArray(instructions)) {
          return renderInstructionList(instructions, vars);
        }
        return renderInstructionsTabs(instructions, vars);
      }

      function renderInstructionList(blocks, vars) {
        const section = document.getElementById('instructionsSection');
        const tabsHost = document.getElementById('instructionsTabs');
        if (!section || !tabsHost) return;

        const container = document.createElement('div');
        const steps = Array.isArray(blocks) ? blocks : [];
        if (!steps.length) return;

        for (const block of steps) {
          const card = document.createElement('div');
          card.className = 'card mb-3';
          const cb = document.createElement('div');
          cb.className = 'card-body';
          const h = document.createElement('h5');
          h.className = 'card-title';
          h.textContent = block.title || 'Langkah';
          cb.appendChild(h);

          const ol = document.createElement('ol');
          ol.className = 'mb-0 ps-3';
          const stepsArr = Array.isArray(block.steps) ? block.steps : [];
          for (const s of stepsArr) {
            const li = document.createElement('li');
            li.className = 'mb-1';
            const applied = applyVars(String(s), vars);
            li.innerHTML = sanitizeHtml(applied);
            ol.appendChild(li);
          }
          cb.appendChild(ol);
          card.appendChild(cb);
          container.appendChild(card);
        }

        tabsHost.innerHTML = '';
        tabsHost.appendChild(container);
        section.classList.remove('d-none');
      }

      function renderInstructionsTabs(instructions, vars) {
        const section = document.getElementById('instructionsSection');
        const tabsHost = document.getElementById('instructionsTabs');
        if (!section || !tabsHost) return;

        const categories = Object.keys(instructions || {});
        if (!categories.length) return;

        const nav = document.createElement('ul');
        nav.className = 'nav nav-tabs';

        const content = document.createElement('div');
        content.className = 'tab-content border border-top-0 p-3 rounded-bottom';

        const makeId = (k) => `ins-${k.replace(/[^a-z0-9]/gi, '')}-${Math.random().toString(36).slice(2, 7)}`;
        let first = true;

        for (const key of categories) {
          const pretty = key.toUpperCase();
          const paneId = makeId(key);

          // Tab button
          const li = document.createElement('li');
          li.className = 'nav-item';
          const a = document.createElement('a');
          a.className = 'nav-link' + (first ? ' active' : '');
          a.dataset.bsToggle = 'tab';
          a.href = `#${paneId}`;
          a.textContent = pretty;
          li.appendChild(a);
          nav.appendChild(li);

          // Pane
          const pane = document.createElement('div');
          pane.className = 'tab-pane fade' + (first ? ' show active' : '');
          pane.id = paneId;

          const steps = Array.isArray(instructions[key]) ? instructions[key] : [];
          if (!steps.length) {
            const empty = document.createElement('div');
            empty.className = 'text-secondary';
            empty.textContent = 'Tidak ada petunjuk untuk kanal ini.';
            pane.appendChild(empty);
          } else {
            for (const block of steps) {
              const card = document.createElement('div');
              card.className = 'card mb-3';
              const cb = document.createElement('div');
              cb.className = 'card-body';
              const h = document.createElement('h5');
              h.className = 'card-title';
              h.textContent = block.title || 'Langkah';
              cb.appendChild(h);

              const ol = document.createElement('ol');
              ol.className = 'mb-0 ps-3';
              const stepsArr = Array.isArray(block.steps) ? block.steps : [];
              for (const s of stepsArr) {
                const li = document.createElement('li');
                li.className = 'mb-1';
                const applied = applyVars(String(s), vars);
                li.innerHTML = sanitizeHtml(applied);
                ol.appendChild(li);
              }
              cb.appendChild(ol);
              card.appendChild(cb);
              pane.appendChild(card);
            }
          }

          content.appendChild(pane);
          first = false;
        }

        tabsHost.innerHTML = '';
        tabsHost.appendChild(nav);
        tabsHost.appendChild(content);
        section.classList.remove('d-none');
      }

      // Helpers
      // Upload transfer receipt
      (function bindUpload() {
        if (!(type === 'PRESENT_TO_CUSTOMER' && descriptor === 'BANK_TRANSFER_DETAILS')) return;
        const form = document.getElementById('receiptForm');
        if (!form) return;
        form.addEventListener('submit', async (e) => {
          e.preventDefault();
          const file = document.getElementById('receiptFile')?.files?.[0];
          if (!file) return;
          const btn = document.getElementById('btnUploadReceipt');
          const statusEl = document.getElementById('uploadStatus');
          const token = new URLSearchParams(window.location.search).get('token');
          const requestUrl = @json(route('e-billing.invoice.transfer-receipts.store', ['invoice' => '__INVOICE_ID__']));
          const url = requestUrl.replace('__INVOICE_ID__', session?.reference_id ?? '');
          const formData = new FormData(form);
          btn.setAttribute('disabled', 'disabled');
          statusEl.textContent = 'Mengunggah…';
          try {
            const res = await fetch(url, {
              method: 'POST',
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: formData
            });
            const data = await res.json().catch(() => null);
            if (!res.ok) throw new Error(data?.message || 'Gagal mengunggah');
            statusEl.textContent = 'Bukti terkirim. Menunggu verifikasi admin.';
          } catch (err) {
            statusEl.textContent = (err?.message || 'Gagal mengunggah.');
          } finally {
            btn.removeAttribute('disabled');
          }
        });
      })();

      function bindCopy(btnId, text) {
        const btn = document.getElementById(btnId);
        if (btn && text && navigator.clipboard) {
          btn.addEventListener('click', async () => {
            try {
              await navigator.clipboard.writeText(text);
              copyFeedback(btn);
            } catch (_) {
              alert('Gagal menyalin.');
            }
          });
        }
      }

      function copyFeedback(btn) {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="icon ti ti-check"></i> Tersalin';
        setTimeout(() => {
          btn.innerHTML = originalHtml;
        }, 1500);
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

      // Inject fee & total summary if exists
      (function renderFeeSummary() {
        const container = document.querySelector('.card-body');
        if (!container) return;
        const fee = meta.fee || 0;
        if (fee <= 0) return;
        const base = meta.amount || 0;
        const total = meta.total_amount || (base + fee);
        const box = document.createElement('div');
        box.className = 'alert alert-info mt-3';
        box.innerHTML = `<div class="d-flex flex-column">
            <div><strong>Rincian Pembayaran</strong></div>
            <div class="small">Tagihan: <span class="fw-semibold">Rp${base.toLocaleString('id-ID')}</span></div>
            <div class="small">Biaya Metode: <span class="fw-semibold">Rp${fee.toLocaleString('id-ID')}</span></div>
            <div class="mt-1">Total Dibayar: <span class="fw-bold">Rp${total.toLocaleString('id-ID')}</span></div>
        </div>`;
        container.insertBefore(box, container.firstChild.nextSibling);
      })();
    });
  </script>
@endsection
