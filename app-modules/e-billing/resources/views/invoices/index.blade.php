<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">Transaksi</div>
          <h2 class="page-title">Tagihan/Invoice</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#manualInvoiceModal">
            <i class="ti ti-plus icon"></i>
            Buat Invoice Manual
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')
      <div class="row row-deck row-cards">
        <div class="col-12">
          <x-datatable tableId="invoicesTable" title="Daftar Invoice" :data="$invoices">
            <x-slot:thead>
              <tr>
                <th>#</th>
                <x-sortable-header field="invoice_number" label="No. Invoice" />
                <x-sortable-header field="customer.name" label="Pelanggan" />
                <x-sortable-header field="package.name" label="Paket" />
                <x-sortable-header field="amount" label="Jumlah" />
                <x-sortable-header field="status" label="Status" />
                <x-sortable-header field="paid_at" label="Dibayar Pada" />
                <th>Aksi</th>
              </tr>
            </x-slot>

            <x-slot:tbody>
              @forelse ($invoices as $invoice)
                @php
                  $customer = $invoice->customer ?? $invoice->customer_detail;
                  $package = $invoice->package ?? $invoice->package_detail;
                @endphp
                <tr>
                  <td>{{ $loop->iteration + $invoices->firstItem() - 1 }}</td>
                  <td class="fw-medium">{{ $invoice->invoice_number }}</td>
                  <td>{{ $customer->name ?? '-' }}</td>
                  <td>{{ $package->name ?? '-' }}</td>
                  <td>Rp{{ number_format($invoice->amount, 0, ',', '.') }}</td>
                  <td>
                    <x-common.badge :label="$invoice->status->label()" :color="$invoice->status->color()" />
                  </td>
                  <td>{{ $invoice->paid_at?->format('d/m/Y H:i') ?? '-' }}</td>
                  <td>
                    <a href="{{ route('e-billing.invoices.show', $invoice) }}" class="btn btn-icon btn-primary"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat detail">
                      <i class="ti ti-eye"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center">Tidak ada invoice.</td>
                </tr>
              @endforelse
            </x-slot:tbody>
          </x-datatable>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>

<!-- Manual Invoice Modal -->
<div class="modal fade" id="manualInvoiceModal" tabindex="-1" aria-labelledby="manualInvoiceModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="manualInvoiceModalLabel">Buat Invoice Manual</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('e-billing.invoices.create-manual') }}">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Pilih Pelanggan Aktif</label>
            <select name="customer_id" class="form-select" required>
              <option value="" disabled selected>-- pilih --</option>
              @php($activeCustomers = \Modules\EBilling\Models\Customer::where('status', \Modules\EBilling\Enums\CustomerStatus::ACTIVE)->orderBy('name')->get())
              @foreach ($activeCustomers as $cust)
                <option value="{{ $cust->id }}">{{ $cust->customer_id }} - {{ $cust->name }}
                  ({{ $cust->package?->name ?? 'Paket ?' }})
                </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Buat Invoice</button>
        </div>
      </form>
    </div>
  </div>
</div>
