<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">Transaksi</div>
          <h2 class="page-title">Detail Invoice</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a class="btn btn-primary"
            href="{{ route('e-billing.invoice.customer-show', $invoice->customer->customer_id) }}" target="_blank">
            <i class="ti ti-file-invoice icon"></i>
            Lihat Invoice
          </a>
          <a class="btn btn-secondary" href="{{ route('e-billing.invoices.index') }}">Kembali</a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')
      <div class="row row-deck row-cards">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Informasi Invoice</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">No. Invoice</div>
                  <div class="datagrid-content">{{ $invoice->invoice_number }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Status</div>
                  <div class="datagrid-content">
                    <x-common.badge :label="$invoice->status->label()" :color="$invoice->status->color()" />
                  </div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Jumlah</div>
                  <div class="datagrid-content">Rp{{ number_format($invoice->amount, 0, ',', '.') }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Dibuat Pada</div>
                  <div class="datagrid-content">{{ $invoice->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Dibayar Pada</div>
                  <div class="datagrid-content">{{ $invoice->paid_at?->format('d/m/Y H:i') ?? '-' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Pelanggan</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">ID Pelanggan</div>
                  <div class="datagrid-content">{{ $invoice->customer->customer_id ?? '-' }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Nama</div>
                  <div class="datagrid-content">{{ $invoice->customer->name ?? '-' }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Email</div>
                  <div class="datagrid-content">{{ $invoice->customer->email ?? '-' }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Telepon</div>
                  <div class="datagrid-content">{{ $invoice->customer->phone ?? '-' }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Paket</div>
                  <div class="datagrid-content">{{ $invoice->package->name ?? '-' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Ringkasan Pembayaran</h3>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-vcenter card-table">
                  <tbody>
                    <tr>
                      <td>Harga Paket</td>
                      <td class="text-end">
                        Rp{{ number_format($invoice->package->price ?? $invoice->amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                      <td>Total</td>
                      <td class="text-end fw-bold">Rp{{ number_format($invoice->amount, 0, ',', '.') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
