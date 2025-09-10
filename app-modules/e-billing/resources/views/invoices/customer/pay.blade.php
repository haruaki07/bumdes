@section('title', 'Checkout Pembayaran')

<x-e-billing::layouts.blank>
  @if (!empty($session) && config('app.env') !== 'production')
    <div id="simulateBar"
      style="position:sticky;top:0;left:0;z-index:999;background:#fff3cd;color:#856404;font-size:14px;cursor:pointer;padding:6px 12px;text-align:center;font-weight:500">
      Simulasikan pembayaran
    </div>
  @endif
  @if (!empty($error))
    <div class="empty">
      <div class="empty-img">
        <img src="{{ asset('assets/images/misc/cat.webp') }}" width="180" alt="Cat" />
      </div>
      <p class="empty-title">{{ $title }}</p>
      <p class="empty-subtitle text-secondary">
        {{ $error }}
      </p>
    </div>
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
      $fee = $s['fee'] ?? 0;
      $totalAmount = $amount + $fee;
      $amountFormatted = number_format($amount, 0, ',', '.');
      $feeFormatted = number_format($fee, 0, ',', '.');
      $totalAmountFormatted = number_format($totalAmount, 0, ',', '.');
      $customerId = $s['customer']['customer_id'] ?? '';
    @endphp

    <div class="row g-0 h-100">
      <div class="col-md-8 border-0 rounded-0 card">

        <div class="container py-5" style="max-width: 720px;">

          <div class="alert alert-info d-flex gap-2" role="alert" id="countdownAlert"
            @if ($descriptor === 'BANK_TRANSFER_DETAILS') style="display:none" @endif>
            <i class="ti ti-info-circle alert-icon"></i>
            <div>
              Selesaikan pembayaran sebelum
              <strong>{{ $expiresAt ? $expiresAt->locale('id')->translatedFormat('d F Y H:i') : '-' }}</strong>
              <span id="countdown" class="ms-1"></span>
            </div>
          </div>

          @if ($actionType === 'PRESENT_TO_CUSTOMER')
            @if ($descriptor === 'BANK_TRANSFER_DETAILS')
              @php
                $bank = $value['bank'] ?? 'Bank';
                $accNo = $value['account_number'] ?? '';
                $accName = $value['account_name'] ?? config('app.name');
                $note = $value['note'] ?? '';
              @endphp
              <div class="alert alert-secondary" role="alert">
                Silakan transfer sesuai detail berikut, lalu kirim bukti ke admin untuk konfirmasi.
              </div>
              <div class="card shadow-none rounded-0 p-5">
                <div class="row gy-5 gy-sm-0">
                  <div class="col-sm-6 order-sm-last">
                    <div class="h-100 d-flex flex-shrink-0 justify-content-center align-items-center">
                      <div class="text-center">
                        @php
                          $imageUrl = $session['payment_method']['image_url'];
                        @endphp
                        <div class="payment border-0 shadow-none"
                          style="height: 120px;background-image:url('{{ asset($imageUrl) }}');">
                        </div>
                        @if ($note)
                          <div class="small text-secondary">
                            Catatan:
                            <p>{{ $note }}</p>
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="datagrid fs-1">
                      <div class="datagrid-item">
                        <div class="datagrid-title">Nomor Rekening</div>
                        <div class="datagrid-content">
                          <span id="codeBox" class="mono">{{ $accNo }}</span>
                          <button class="btn btn-icon" id="btnCopyCode" data-bs-toggle="tooltip" title="Salin">
                            <i class="icon ti ti-copy"></i>
                          </button>
                        </div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Nama Pemilik</div>
                        <div class="datagrid-content">{{ $accName }}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Nominal Bayar</div>
                        <div class="datagrid-content">Rp{{ $totalAmountFormatted }}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card mt-3" style="border-style: dashed;">
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
            @elseif ($descriptor === 'QR_STRING')
              <div class="text-center d-flex flex-column align-items-center gap-2">
                <p class="m-0">Scan QR berikut di aplikasi pembayaran Anda:</p>
                <div id="qr" class="qr-box d-inline-flex align-items-center justify-content-center"></div>
                <button class="btn d-inline-block" id="btnDownloadQR">
                  <i class="ti ti-download icon"></i>
                  Unduh
                </button>
              </div>
            @elseif ($descriptor === 'VIRTUAL_ACCOUNT_NUMBER')
              <div class="card shadow-none rounded-0 p-5">
                <div class="row">
                  <div class="col-6">
                    <div class="datagrid fs-1">
                      <div class="datagrid-item">
                        <div class="datagrid-title">Nomor Virtual Account</div>
                        <div class="datagrid-content">
                          <span id="codeBox" class="mono">{{ $value }}</span>
                          <button class="btn btn-icon" id="btnCopyCode" data-bs-toggle="tooltip" title="Salin">
                            <i class="icon ti ti-copy"></i>
                          </button>
                        </div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Nama Virtual Account</div>
                        <div class="datagrid-content">{{ $session['merchant_name'] }}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Nominal Bayar</div>
                        <div class="datagrid-content">Rp{{ $totalAmountFormatted }}</div>
                      </div>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="h-100 d-flex flex-shrink-0 justify-content-center align-items-center">
                      <div class="text-center">
                        @php
                          $imageUrl = $session['payment_method']['image_url'];
                        @endphp
                        <div class="payment border-0 shadow-none"
                          style="height: 120px;background-image:url('{{ asset($imageUrl) }}');">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @elseif ($descriptor === 'PAYMENT_CODE')
              <div class="card shadow-none rounded-0 p-5">
                <div class="row">
                  <div class="col-6">
                    <div class="datagrid fs-1">
                      <div class="datagrid-item">
                        <div class="datagrid-title">Kode Pembayaran</div>
                        <div class="datagrid-content">
                          <span id="codeBox" class="mono">{{ $value }}</span>
                          <button class="btn btn-icon" id="btnCopyCode" data-bs-toggle="tooltip" title="Salin">
                            <i class="icon ti ti-copy"></i>
                          </button>
                        </div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Bayar ke Merchant</div>
                        <div class="datagrid-content">{{ $session['merchant_name'] }}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Nominal Bayar</div>
                        <div class="datagrid-content">Rp{{ $totalAmountFormatted }}</div>
                      </div>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="h-100 d-flex flex-shrink-0 justify-content-center align-items-center">
                      <div class="text-center">
                        <div class="text-primary text-uppercase small">Tunjukkan ke kasir</div>
                        <svg id="barcode" style="width: 240px; height: 120px"></svg>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @else
              <div class="alert alert-warning">Aksi tidak didukung: {{ $descriptor }}</div>
            @endif
          @elseif ($actionType === 'REDIRECT_CUSTOMER')
            @if ($descriptor === 'WEB_URL' && filter_var($value, FILTER_VALIDATE_URL))
              <div class="text-center">
                <p class="mb-2">Anda akan diarahkan ke halaman pembayaran dalam <span id="redirSec">5</span> detik…
                </p>
                <a class="btn btn-primary" id="btnGoNow" href="{{ $value }}" rel="noopener"
                  target="_blank">
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

          <div class="mt-5">
            <a href="{{ route('e-billing.invoice.customer-show', ['customer_id' => $customerId]) }}"
              class="btn btn-secondary" id="btnBack">
              Kembali
            </a>
          </div>

        </div>
      </div>
      <div class="col-md-4">
        <div class="container container-tight py-5 px-4">
          <h2>Rincian Pembayaran</h2>
          <p>Nomor Tagihan: {{ $referenceId }}</p>
          <hr class="my-4" style="opacity:0.05" />
          <div class="d-flex align-items-start">
            <div class="me-auto">
              <h4 class="mb-0">{{ $invoice->package_detail->name }}</h4>
              <div class="text-secondary">{{ $invoice->period_start_date->locale('id')->format('d M Y') }} s.d.
                {{ $invoice->period_end_date->locale('id')->format('d M Y') }}</div>
            </div>
            <div class="text-end" style="width:120px">
              <h4 class="mb-0">{{ $amountFormatted }}</h4>
            </div>
          </div>
          <hr class="my-4" style="opacity:0.05" />
          <div class="d-flex align-items-center">
            <div class="ms-auto">
              <h4 class="mb-0">Subtotal</h4>
            </div>
            <div class="text-end" style="width:120px">
              <h4 class="mb-0">{{ $amountFormatted }}</h4>
            </div>
          </div>
          @if ($fee > 0)
            <hr class="my-4" style="opacity:0.05" />
            <div class="d-flex align-items-center mb-3">
              <div class="ms-auto">
                <div class="fs-4">Biaya Admin</div>
              </div>
              <div class="text-end" style="width:120px">
                <div class="fs-4">{{ $feeFormatted }}</div>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <div class="ms-auto">
                <h4 class="mb-0">Total Biaya</h4>
              </div>
              <div class="text-end" style="width:120px">
                <h4 class="mb-0">{{ $feeFormatted }}</h4>
              </div>
            </div>
          @endif
          <hr class="my-4" style="opacity:0.05" />
          <div class="d-flex align-items-center">
            <div class="ms-auto">
              <h4 class="mb-0">Total</h4>
            </div>
            <div class="text-end" style="width:120px">
              <div class="fs-3 fw-bold">Rp{{ $totalAmountFormatted }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif

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
      if (!empty($session)) {
          $sessionMeta->put(
              'simulate_url',
              route('e-billing.invoice.pay.simulate', ['token' => request()->query('token')]),
          );
      }
    @endphp
    <script id="sessionData" type="application/json">{!! json_encode($sessionData) !!}</script>
    <script id="sessionMeta" type="application/json">{!! json_encode($sessionMeta) !!}</script>
    @vite(['app-modules/e-billing/resources/js/pages/pay.js'])
  @endpush
</x-e-billing::layouts.blank>
