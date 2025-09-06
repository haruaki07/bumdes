@section('title', 'Checkout Pembayaran')

<x-e-billing::layouts.blank>
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
                <p class="mb-2">Anda akan diarahkan ke halaman pembayaran dalam <span id="redirSec">5</span> detik…
                </p>
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

  @push('js')
    @php
      $sessionData = collect($session)->only(['expires_at', 'action', 'payment_method', 'reference_id', 'id']);
      $sessionMeta = collect($session)
          ->only(['amount', 'fee', 'total_amount'])
          ->put(
              'invoice_url',
              $session ? route('e-billing.invoice.customer-show', ['customer_id' => $session['reference_id']]) : null,
          )
          ->put(
              'customer_invoice_url',
              $session
                  ? route('e-billing.invoice.customer-show', ['customer_id' => $session['customer']['customer_id']])
                  : null,
          )
          ->put(
              'status_url',
              $session ? route('e-billing.invoice.pay.status', ['token' => request()->query('token')]) : null,
          )
          ->put(
              'instructions_url',
              $session
                  ? asset('assets/e-billing/payment_instructions') . "/{$sessionData['payment_method']['code']}.json"
                  : null,
          )
          ->put(
              'transfer_receipt_url',
              $session
                  ? route('e-billing.invoice.transfer-receipts.store', ['invoice' => $session['reference_id']])
                  : null,
          );
    @endphp
    <script id="sessionData" type="application/json">{!! json_encode($sessionData) !!}</script>
    <script id="sessionMeta" type="application/json">{!! json_encode($sessionMeta) !!}</script>
    @vite(['app-modules/e-billing/resources/js/pages/pay.js'])
  @endpush
</x-e-billing::layouts.blank>
