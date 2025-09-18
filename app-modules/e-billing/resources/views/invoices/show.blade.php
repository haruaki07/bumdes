@php
  use Modules\EBilling\Enums\InvoiceStatus;
@endphp

<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">Invoice</div>
          <h2 class="page-title d-flex align-items-center gap-2">
            <span>#{{ $invoice->invoice_number }}</span>
            <x-common.badge :label="$invoice->status->label()" :color="$invoice->status->color()" />
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a class="btn btn-secondary" href="{{ route('e-billing.invoices.index') }}">
            Kembali
          </a>
          <a class="btn btn-outline-primary" href="{{ $invoice->public_url }}" target="_blank">
            <i class="ti ti-external-link icon"></i> Halaman Publik
          </a>
          @if ($invoice->status !== InvoiceStatus::PAID)
            <form method="POST" action="{{ route('e-billing.invoices.send-notification', $invoice) }}"
              class="d-inline">
              @csrf
              <button class="btn btn-outline-success">
                <i class="ti ti-send icon"></i> Kirim Email
              </button>
            </form>
            <form method="POST" action="{{ route('e-billing.invoices.mark-paid', $invoice) }}" class="d-inline">
              @csrf
              <button class="btn btn-success">
                <i class="ti ti-check icon"></i> Tandai Lunas
              </button>
            </form>
          @endif
          @if ($invoice->status === InvoiceStatus::PAID)
            <form method="POST" action="{{ route('e-billing.invoices.mark-unpaid', $invoice) }}" class="d-inline">
              @csrf
              <button class="btn btn-outline-danger">
                <i class="ti ti-x icon"></i> Tandai Belum Lunas
              </button>
            </form>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <div class="row row-cards mb-3">
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="avatar bg-primary text-white text-capitalize">Rp</span>
                </div>
                <div class="col">
                  <div class="text-secondary">Jumlah</div>
                  <div class="fw-bold">Rp{{ number_format($invoice->amount, 0, ',', '.') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="avatar bg-{{ $invoice->status->color() }} text-white"><i class="ti ti-flag"></i></span>
                </div>
                <div class="col">
                  <div class="text-secondary">Status</div>
                  <div class="fw-bold">{{ $invoice->status->label() }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="avatar bg-muted text-white"><i class="ti ti-calendar-plus"></i></span>
                </div>
                <div class="col">
                  <div class="text-secondary">Dibuat</div>
                  <div class="fw-bold">{{ $invoice->created_at->format('d/m/Y H:i') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="avatar bg-info text-white"><i class="ti ti-calendar-check"></i></span>
                </div>
                <div class="col">
                  <div class="text-secondary">Dibayar</div>
                  <div class="fw-bold">{{ $invoice->paid_at?->format('d/m/Y H:i') ?? '-' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row row-deck row-cards">
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Rincian Invoice</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">No. Invoice</div>
                  <div class="datagrid-content">{{ $invoice->invoice_number }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Paket</div>
                  <div class="datagrid-content">{{ $invoice->package_detail->name ?? '-' }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Harga Paket</div>
                  <div class="datagrid-content">
                    Rp{{ number_format($invoice->package_detail->price ?? $invoice->amount, 0, ',', '.') }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Terakhir Diperbarui</div>
                  <div class="datagrid-content">{{ $invoice->updated_at->format('d/m/Y H:i') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Pelanggan</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">Nama</div>
                  <div class="datagrid-content">{{ $invoice->customer_detail->name ?? '-' }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">ID Pelanggan</div>
                  <div class="datagrid-content">{{ $invoice->customer_detail->customer_id ?? '-' }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Email</div>
                  <div class="datagrid-content">{{ $invoice->customer_detail->email ?? '-' }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Telepon</div>
                  <div class="datagrid-content">{{ $invoice->customer_detail->phone ?? '-' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Ringkasan Pembayaran</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">Harga Paket</div>
                  <div class="datagrid-content">Rp{{ number_format($invoice->package_detail->price, 0, ',', '.') }}
                  </div>
                </div>
                @if ($invoice->paymentMethod && $invoice->paymentMethod->calculateFee($invoice->amount) > 0)
                  <div class="datagrid-item">
                    <div class="datagrid-title">Biaya Admin</div>
                    <div class="datagrid-content">
                      Rp{{ number_format($invoice->paymentMethod->calculateFee($invoice->amount), 0, ',', '.') }}</div>
                  </div>
                @endif
                <div class="datagrid-item">
                  <div class="datagrid-title">Total</div>
                  <div class="datagrid-content fw-bold fs-3">Rp{{ number_format($invoice->amount, 0, ',', '.') }}
                  </div>
                </div>
                @if ($invoice->payment_method_code)
                  <div class="datagrid-item">
                    <div class="datagrid-title">Metode Pembayaran</div>
                    <div class="datagrid-content text-capitalize">
                      @if ($invoice->paymentMethod->brand_logo)
                        <span class="payment payment-xs me-1"
                          style="background-image:url('{{ asset($invoice->paymentMethod->brand_logo) }}');"></span>
                      @endif
                      {{ $invoice->paymentMethod->name ?? '-' }} -
                      {{ $invoice->paymentMethod->type->label() }}
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      @if (isset($receipts) && count($receipts))
        <div class="row row-deck row-cards pt-3">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Bukti Transfer</h3>
              </div>
              <div class="table-responsive">
                <table class="table card-table table-vcenter">
                  <thead>
                    <tr>
                      <th>Waktu</th>
                      <th>Status</th>
                      <th>File</th>
                      <th>Catatan</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($receipts as $r)
                      <tr>
                        <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                          <x-common.badge :label="$r->status->label()" :color="$r->status->color()" />
                        </td>
                        <td>
                          <a href="{{ Storage::url($r->file_path) }}" target="_blank"
                            rel="noopener">{{ $r->original_name ?? basename($r->file_path) }}</a>
                        </td>
                        <td class="text-secondary"
                          style="max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                          {{ $r->note ?? '-' }}
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
</x-e-billing::layouts.panel>
