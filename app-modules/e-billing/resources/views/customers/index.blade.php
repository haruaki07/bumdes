<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Data Master
          </div>
          <h2 class="page-title">
            Pelanggan
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importCustomerModal">
              <i class="ti ti-upload icon"></i>
              Import
            </button>
            {{-- @can('create', \App\Models\BusinessRegistration::class) --}}
            <a href="{{ route('e-billing.master-data.customers.create') }}" class="btn btn-primary">
              <i class="icon ti ti-plus"></i>
              Tambah Pelanggan
            </a>
            {{-- @endcan --}}
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      @if (session('import_error'))
        <div class="alert alert-important alert-danger alert-dismissible d-block" role="alert">
          <div class="d-flex gap-2">
            <div class="alert-icon">
              <i class="ti ti-alert-circle"></i>
            </div>
            <div class="alert-description">Import Gagal</div>
          </div>
          <div class="alert-description mt-2">
            Gagal mengimpor data pelanggan karena terdapat kesalahan berikut:
            <ul class="my-2">
              {!! session('import_error') !!}
            </ul>
          </div>
          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
      @endif

      <div class="row row-deck row-cards">
        <div class="col-12">
          <x-datatable tableId="customersTable" title="Daftar Pelanggan" :data="$customers">
            <x-slot:thead>
              <tr>
                <th>#</th>
                <x-sortable-header field="customer_id" label="ID Pelanggan" />
                <th>Tagihan</th>
                <x-sortable-header field="name" label="Nama Pelanggan" />
                <x-sortable-header field="email" label="Email" />
                <x-sortable-header field="phone" label="Telepon" />
                <x-sortable-header field="package.name" label="Paket" />
                <x-sortable-header field="due" label="Jatuh Tempo" />
                <x-sortable-header field="status" label="Status" />
                <th>Aksi</th>
              </tr>
            </x-slot>

            <x-slot:tbody>
              @forelse ($customers as $customer)
                @php
                  $hasInvoice = $customer->invoice_number != null;
                @endphp
                <tr class="{{ $hasInvoice ? 'table-warning' : '' }}">
                  <td>{{ $loop->iteration + $customers->firstItem() - 1 }}</td>
                  <td class="fw-medium">{{ $customer->customer_id }}</td>
                  <td class="text-center">
                    @if ($hasInvoice)
                      <a href="{{ route('e-billing.invoices.show', $customer->invoice_number) }}"
                        class="btn btn-link">#{{ $customer->invoice_number }}</a>
                    @else
                      -
                    @endif
                  </td>
                  <td>{{ $customer->name }}</td>
                  <td>{{ $customer->email }}</td>
                  <td>{{ $customer->phone?->formatNational() }}</td>
                  <td>
                    <x-common.badge color="info" :label="$customer->package->name" />
                  </td>
                  <td>Setiap tanggal {{ $customer->due }}</td>
                  <td>
                    <x-e-billing::modules.customer.status-badge :status="$customer->status" />
                  </td>
                  <td>
                    <a href="{{ route('e-billing.master-data.customers.show', $customer) }}"
                      class="btn btn-icon btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                      title="Lihat detail">
                      <i class="ti ti-eye"></i>
                    </a>
                    {{-- @can('update', $customer) --}}
                    <a href="{{ route('e-billing.master-data.customers.edit', $customer) }}"
                      class="btn btn-icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                      <i class="ti ti-edit"></i>
                    </a>
                    {{-- @endcan --}}
                    <button class="btn btn-icon btn-danger"
                      onclick="deleteConfirm('{{ route('e-billing.master-data.customers.destroy', $customer) }}')"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                      <i class="ti ti-trash"></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center">Tidak ada pelanggan yang terdaftar.</td>
                </tr>
              @endforelse
            </x-slot>
          </x-datatable>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="importCustomerModal" tabindex="-1" aria-labelledby="importCustomerModalLabel"
    aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="customerImportForm" enctype="multipart/form-data" method="POST"
          action="{{ route('e-billing.master-data.customers.import') }}">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="importCustomerModalLabel">Import Data Pelanggan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body position-relative">
            <div id="customerImportAlerts"></div>
            <div class="mb-3">
              <label class="form-label required">Unggah File (.xlsx / .xls / .csv)</label>
              <input type="file" class="form-control form-dropzone" name="file" accept=".xlsx,.xls,.csv" required>
            </div>
            <div class="text-center">
              <a href="{{ asset('templates/customer_import_template.xlsx') }}" class="btn btn-link" download>
                <i class="ti ti-download icon"></i>
                Unduh Template
              </a>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-link" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
