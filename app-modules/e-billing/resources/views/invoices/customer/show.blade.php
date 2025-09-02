@extends('tablar::auth.layout')

@section('title', 'Invoice #' . $invoice->invoice_number)

@section('tablar_css')
  <style>
    @media print {
      .no-print {
        display: none !important;
      }

      a[href]:after {
        content: none !important;
      }

      body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      .container {
        padding: 0 !important;
      }

      .page-center {
        justify-content: flex-start !important;
      }
    }

    .invoice-wrapper {
      max-width: 900px;
      margin: 0 auto;
    }

    .table {
      border-color: transparent;
    }

    .table th,
    .table td {
      vertical-align: middle;
    }

    .table tr:not(:last-child) {
      border-bottom: 1px solid var(--tblr-table-border-color);
    }

    .meta-table {
      width: 100%;
      border-collapse: collapse;
    }

    .meta-label {
      color: var(--tblr-secondary);
    }

    .meta-table td {
      padding: .25rem 0 !important;
    }

    .meta-table tr:last-child td {
      padding-bottom: 0 !important;
    }

    .payment {
      height: 2.5rem;
      aspect-ratio: 1.66666;
      display: inline-block;
      background: no-repeat center / calc(100% - 8px) calc(100% - 8px);
      vertical-align: bottom;
      font-style: normal;
      box-shadow: 0 0 1px 1px rgba(0, 0, 0, .1);
      border-radius: 2px;
      text-align: center;
    }

    .payment-xs {
      line-height: 1.25rem;
      height: 1.25rem;
    }

    .accordion-button:not(.collapsed) .payment-method-previews {
      display: none !important;
    }

    .form-imagecheck-figure::before {
      display: none !important;
    }
  </style>
@endsection

@section('content')
  <div class="container container-tight py-4 invoice-wrapper">
    <div class="card card-md invoice-card">
      <div class="card-body p-4">
        @php
          $invDate = optional($invoice->created_at);
          $dueDay = $customer->due ?? 1;
          $periodStart = $invDate ? $invDate->copy()->locale('id')->setDay($dueDay)->subMonthNoOverflow() : null;
          $periodEnd = $invDate ? $invDate->copy()->locale('id')->setDay($dueDay)->subDay() : null;
          $periodLabel = $periodEnd ? $periodEnd->locale('id')->translatedFormat('F Y') : '-';
          $pkg = (array) ($invoice->package_detail ?? []);

          $isPaid = $invoice->status === \Modules\EBilling\Enums\InvoiceStatus::PAID;
          $isExpired = $invoice->status === \Modules\EBilling\Enums\InvoiceStatus::EXPIRED;
        @endphp

        <div class="d-flex align-items-end justify-content-between">
          <div class="d-flex align-items-end">
            <img src="{{ 'https://placehold.co/400' }}" alt="Logo" style="height: 84px;" class="me-3">
            <div>
              <h2 class="mb-1">EBilling</h2>
              <p class="text-secondary mb-0">High Speed Home Internet</p>
            </div>
          </div>
          <div style="width: 35%">
            <table class="meta-table">
              <tr>
                <td class="meta-label">Invoice</td>
                <td class="text-end fw-bold">{{ $invoice->invoice_number ?? $invoice->id }}</td>
              </tr>
              <tr>
                <td class="meta-label">Tanggal Invoice</td>
                <td class="text-end">{{ $invDate ? $invDate->format('d-m-Y') : '-' }}</td>
              </tr>
              <tr>
                <td class="meta-label">Nomor Pelanggan</td>
                <td class="text-end">{{ $customer->customer_id ?? $customer->id }}</td>
              </tr>
            </table>
          </div>
        </div>

        <div class="d-flex align-items-start justify-content-between my-5">
          <div>
            <div class="fw-semibold mb-1">Kepada,</div>
            <table class="meta-table" style="max-width: 420px;">
              <tr>
                <td>{{ $customer->name }}</td>
              </tr>
              @if (!empty($customer->phone))
                <tr>
                  <td>Telp, {{ $customer->phone }}</td>
                </tr>
              @endif
              @if (!empty($customer->address))
                <tr>
                  <td>{{ $customer->address }}</td>
                </tr>
              @endif
            </table>
          </div>

          <div class="text-center fs-3">
            <div class="text-uppercase text-muted small">Periode</div>
            <div class="fw-semibold">{{ strtoupper($periodLabel) }}</div>
            <div class="fw-bold text-uppercase {{ $isPaid ? 'text-success' : ($isExpired ? 'text-danger' : '') }}">
              {{ $invoice->status->label() }}
            </div>
          </div>
        </div>

        @if ($isPaid)
          <div class="alert alert-success no-print" role="alert">
            <div class="d-flex">
              <div><i class="ti ti-checks me-2"></i></div>
              <div>
                <div class="fw-bold">Tagihan sudah dibayar</div>
                Terima kasih, pembayaran Anda telah kami terima.
              </div>
            </div>
          </div>
        @elseif ($isExpired)
          <div class="alert alert-danger no-print" role="alert">
            <div class="d-flex">
              <div><i class="ti ti-alert-triangle me-2"></i></div>
              <div>
                <div class="fw-bold">Tagihan telah kadaluarsa</div>
                Silakan hubungi admin untuk membuat tagihan baru.
              </div>
            </div>
          </div>
        @endif


        <div class="table-responsive mb-3">
          <table class="table">
            <thead class="table-light">
              <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Deskripsi</th>
                <th style="width: 15%;">Tarif</th>
                <th style="width: 33%;">Pemakaian</th>
                <th style="width: 16%;" class="text-end">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>
                  Paket Internet
                  @if (!empty($pkg))
                    <div class="text-muted small">{{ $pkg['name'] ?? '' }} @if (!empty($pkg['bandwidth']))
                        ({{ $pkg['bandwidth'] }} Mbps)
                      @endif
                    </div>
                  @endif
                </td>
                <td>Rp {{ number_format($invoice->amount ?? 0, 0, ',', '.') }}</td>
                <td>
                  @if ($periodStart && $periodEnd)
                    {{ $periodStart->translatedFormat('d F Y') }} - {{ $periodEnd->translatedFormat('d F Y') }}
                  @else
                    -
                  @endif
                </td>
                <td class="text-end">Rp {{ number_format($invoice->amount ?? 0, 0, ',', '.') }}</td>
              </tr>
              <tr class="fs-3">
                <td colspan="4" class="text-end fw-semibold">Sub Total:</td>
                <td class="text-end fw-semibold">Rp {{ number_format($invoice->amount ?? 0, 0, ',', '.') }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="col-md-8 fs-5 mb-3">
          <div class="fw-semibold mb-2">Catatan:</div>
          <div class="text-secondary">
            Tagihan harus dibayar paling lambat pada tanggal jatuh tempo. Internet akan diputus jika pembayaran tidak
            diterima setelah tanggal tersebut. Mohon abaikan pemberitahuan ini jika Anda
            telah melakukan pembayaran.
          </div>
        </div>

        <div class="btn-list justify-content-end no-print">
          <button type="button" class="btn" onclick="window.print()">
            <i class="icon ti ti-printer"></i>
            Print
          </button>
          @if ($invoice->status === \Modules\EBilling\Enums\InvoiceStatus::UNPAID)
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentMethodModal">
              Bayar
            </button>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade no-print" id="paymentMethodModal" tabindex="-1">
    <div class="modal-dialog" role="document">
      <form id="paymentMethodForm" class="modal-content" novalidate>
        <div class="modal-header">
          <h5 class="modal-title">Pilih Metode Pembayaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Batal"></button>
        </div>
        <div class="modal-body">
          <div id="paymentMethodAlert"></div>
          <input type="hidden" name="customer_id" value="{{ $customer->customer_id }}" />
          @if (!empty($savedPaymentMethod))
            <div id="savedMethodPanel" class="card border mb-3">
              <div class="card-body py-3 d-flex align-items-center">
                <div class="me-3">
                  @if ($savedPaymentMethod->brand_logo)
                    <span class="payment"
                      style="background-image:url('{{ asset($savedPaymentMethod->brand_logo) }}');"></span>
                  @else
                    <span class="badge bg-primary">{{ $savedPaymentMethod->name ?? $savedPaymentMethod->code }}</span>
                  @endif
                </div>
                <div class="flex-fill">
                  <div class="fw-semibold">Metode pembayaran tersimpan</div>
                  <div class="text-secondary small">{{ $savedPaymentMethod->name ?? $savedPaymentMethod->code }}</div>
                </div>
                <div class="btn-list ms-auto">
                  <button type="button" id="changeMethodBtn" class="btn btn-outline-secondary btn-sm">Ganti
                    metode</button>
                  <button type="button" id="removeSavedMethodBtn" class="btn btn-outline-danger btn-sm">Hapus</button>
                </div>
              </div>
              <!-- Hidden input to use saved method by default -->
              <input type="hidden" name="payment_method" value="{{ $savedPaymentMethod->id }}" />
            </div>
          @endif
          <div id="paymentMethodsBox" class="{{ !empty($savedPaymentMethod) ? 'd-none' : '' }}">
            <div class="mb-2 text-secondary small">Pilih salah satu metode di bawah ini</div>
            <div class="accordion" id="paymentMethodsAccordion">
              @foreach ($paymentMethods->groupBy('type') as $type => $methods)
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed py-2 px-3 fs-4" type="button" data-bs-toggle="collapse"
                      data-bs-target="#type-{{ Str::slug($type) }}" aria-expanded="false"
                      aria-controls="type-{{ Str::slug($type) }}">
                      <div class="w-full d-flex align-items-center">
                        {{ \Modules\EBilling\Enums\PaymentMethodType::from($type)->label() }}
                        <div class="d-flex align-items-center ms-auto payment-method-previews">
                          @php
                            $previewMethods = collect($methods)->filter(fn($m) => !empty($m->brand_logo))->take(2);
                          @endphp
                          @foreach ($previewMethods as $method)
                            <span class="payment payment-xs ms-2"
                              style="background-image: url('{{ asset($method->brand_logo) }}');"></span>
                          @endforeach
                          @if ($methods->count() - $previewMethods->count() > 0)
                            <span
                              class="payment payment-xs ms-2">+{{ $methods->count() - $previewMethods->count() }}</span>
                          @endif
                        </div>
                      </div>
                      <div class="accordion-button-toggle">
                        <i class="ti ti-chevron-down"></i>
                      </div>
                    </button>
                  </h2>
                  <div id="type-{{ Str::slug($type) }}" class="accordion-collapse collapse"
                    data-bs-parent="#paymentMethodsAccordion">
                    <div class="accordion-body pt-1">
                      <div class="row g-3">
                        @foreach ($methods as $method)
                          <div class="col-4">
                            <label class="form-imagecheck w-full bg-white">
                              <input name="payment_method" type="radio" value="{{ $method->id }}"
                                class="form-imagecheck-input" />
                              <span class="form-imagecheck-figure p-3">
                                @if ($method->brand_logo)
                                  <img src="{{ asset($method->brand_logo) }}" alt=""
                                    class="form-imagecheck-image mx-auto object-fit-contain"
                                    style="height:3rem;opacity:1" />
                                @else
                                  <p class="text-center fw-semibold m-0" style="line-height:3rem;">{{ $method->name }}
                                  </p>
                                @endif
                              </span>
                            </label>
                          </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <label class="form-check mb-0 mt-3">
            <input class="form-check-input" type="checkbox" name="save"
              {{ !empty($savedPaymentMethod) ? 'checked' : '' }} />
            <span class="form-check-label">Simpan metode pembayaran untuk digunakan kembali di lain waktu</span>
          </label>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success" data-bs-toggle="loading-button"
            data-bs-disabled-on-loading="true" data-bs-spinner-type="dots">Bayar Sekarang</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@section('tablar_js')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const accordion = document.getElementById('paymentMethodsAccordion');
      accordion.addEventListener('show.bs.collapse', event => {
        event.target.parentElement.classList.add("bg-body");
      });
      accordion.addEventListener('hide.bs.collapse', event => {
        event.target.parentElement.classList.remove("bg-body");
      });

      const setPaymentMethodAlert = (message, type = 'info') => {
        const alertContainer = document.getElementById('paymentMethodAlert');
        alertContainer.innerHTML = `
          <div class="alert alert-important alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        `;
      };

      const paymentMethodForm = document.getElementById('paymentMethodForm');
      const savedMethodPanel = document.getElementById('savedMethodPanel');
      const paymentMethodsBox = document.getElementById('paymentMethodsBox');
      const changeMethodBtn = document.getElementById('changeMethodBtn');
      const removeSavedMethodBtn = document.getElementById('removeSavedMethodBtn');

      const enableMethodList = () => {
        if (savedMethodPanel) savedMethodPanel.classList.add('d-none');
        if (paymentMethodsBox) paymentMethodsBox.classList.remove('d-none');
        // Remove hidden saved input so radios are used
        const hiddenSaved = paymentMethodForm.querySelector('input[name="payment_method"][type="hidden"]');
        if (hiddenSaved) hiddenSaved.remove();
      }

      if (changeMethodBtn) {
        changeMethodBtn.addEventListener('click', enableMethodList);
      }

      if (removeSavedMethodBtn) {
        removeSavedMethodBtn.addEventListener('click', async () => {
          const customerId = paymentMethodForm.querySelector('input[name="customer_id"]').value;
          try {
            const res = await axios.delete(`/e-billing/api/invoice/${customerId}/saved-payment-method`);
            setPaymentMethodAlert(res?.data?.message || 'Metode tersimpan dihapus.', 'success');
            enableMethodList();
            const saveCheckbox = paymentMethodForm.querySelector('input[name="save"]');
            if (saveCheckbox) saveCheckbox.checked = false;
          } catch (err) {
            setPaymentMethodAlert('Gagal menghapus metode tersimpan. Coba lagi.', 'danger');
          }
        });
      }

      paymentMethodForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const btnSubmit = paymentMethodForm.querySelector('button[type="submit"]');
        // Validate: either have hidden saved input or a selected radio
        const hasSavedInput = !!paymentMethodForm.querySelector('input[name="payment_method"][type="hidden"]');
        const selectedRadio = paymentMethodForm.querySelector(
          'input[name="payment_method"][type="radio"]:checked');
        if (!hasSavedInput && !selectedRadio) {
          setPaymentMethodAlert('Silakan pilih metode pembayaran!', 'warning');
          return;
        }

        try {
          btnSubmit._loadingButtonInstance.start();
          const alertContainer = document.getElementById('paymentMethodAlert');
          alertContainer.innerHTML = '';

          const form = new FormData(paymentMethodForm);
          const body = {}
          form.forEach((value, key) => {
            body[key] = value;
          });
          const res = await axios.post('/e-billing/api/invoice/' + body.customer_id + '/request-payment', body);
          const data = res.data || {};
          if (data.status === 'success' && data.redirect_url) {
            window.location.href = data.redirect_url;
            return;
          }
          // Show fallback messages
          const msg = (data && data.message) ? data.message : 'Terjadi kesalahan saat memproses permintaan.';
          setPaymentMethodAlert(msg, 'danger');
        } catch (err) {
          console.error(err);
          // If server returned a JSON error, surface it
          const resp = err?.response?.data;
          if (resp?.message) {
            setPaymentMethodAlert(resp.message, 'danger');
          } else {
            setPaymentMethodAlert('Terjadi kesalahan jaringan. Silakan coba lagi.', 'danger');
          }
        } finally {
          btnSubmit._loadingButtonInstance.stop();
        }
      });
    });
  </script>
@endsection
