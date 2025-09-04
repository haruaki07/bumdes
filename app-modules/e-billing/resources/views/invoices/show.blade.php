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
            href="{{ route('e-billing.invoice.customer-show', $invoice->status === \Modules\EBilling\Enums\InvoiceStatus::UNPAID ? $invoice->customer->customer_id : $invoice->invoice_number) }}"
            target="_blank">
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
              <div class="card-actions">
                @if ($invoice->status->value !== \Modules\EBilling\Enums\InvoiceStatus::PAID->value)
                  <form method="POST" action="{{ route('e-billing.invoices.mark-paid', $invoice) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-success btn-sm" onclick="return confirm('Tandai invoice sebagai lunas?')">
                      <i class="ti ti-cash"></i> Tandai Lunas
                    </button>
                  </form>
                @endif
              </div>
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

      <div class="row row-deck row-cards mt-3">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Bukti Transfer</h3>
            </div>
            <div class="card-body">
              @if (isset($receipts) && count($receipts))
                <div class="table-responsive">
                  <table class="table table-vcenter card-table">
                    <thead>
                      <tr>
                        <th>Waktu</th>
                        <th>File</th>
                        <th>Status</th>
                        <th>Catatan</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($receipts as $r)
                        <tr>
                          <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                          <td>
                            <a href="{{ Storage::url($r->file_path) }}" target="_blank"
                              rel="noopener">{{ $r->original_name ?? basename($r->file_path) }}</a>
                          </td>
                          <td>
                            <x-common.badge :label="ucfirst($r->status)" :color="$r->status === 'approved' ? 'success' : ($r->status === 'rejected' ? 'danger' : 'warning')" />
                          </td>
                          <td>{{ $r->note ?? '-' }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="text-secondary">Belum ada bukti transfer.</div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
