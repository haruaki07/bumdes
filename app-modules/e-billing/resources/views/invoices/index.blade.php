<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">Transaksi</div>
          <h2 class="page-title">Tagihan/Invoice</h2>
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
                <tr>
                  <td>{{ $loop->iteration + $invoices->firstItem() - 1 }}</td>
                  <td class="fw-medium">{{ $invoice->invoice_number }}</td>
                  <td>{{ $invoice->customer->name ?? '-' }}</td>
                  <td>{{ $invoice->package->name ?? '-' }}</td>
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
