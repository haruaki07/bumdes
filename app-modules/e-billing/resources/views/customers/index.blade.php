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
            <button class="btn btn-outline-secondary d-none d-sm-inline-block" data-bs-toggle="modal"
              data-bs-target="#importCustomerModal">
              <i class="ti ti-upload icon"></i>
              Import
            </button>
            {{-- @can('create', \App\Models\BusinessRegistration::class) --}}
            <a href="{{ route('e-billing.master-data.customers.create') }}"
              class="btn btn-primary d-none d-sm-inline-block">
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
        <form id="customerImportForm" enctype="multipart/form-data" onsubmit="return false;">
          <div class="modal-header">
            <h5 class="modal-title" id="importCustomerModalLabel">Import Data Pelanggan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body position-relative">
            <div id="customerImportAlerts"></div>
            <div class="mb-3">
              <label class="form-label required">Unggah File (.xlsx / .xls / .csv)</label>
              <input type="file" class="form-control form-dropzone" id="customerImportFile" accept=".xlsx,.xls,.csv"
                required>
            </div>
            <div class="text-center">
              <a href="{{ asset('templates/customer_import_template.xlsx') }}" class="btn btn-link" download>
                <i class="ti ti-download icon"></i>
                Unduh Template
              </a>
            </div>
            <div id="customerImportProgress" class="d-none">
              <div class="progress mb-2">
                <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                  aria-valuemax="100">0%</div>
              </div>
              <div class="small text-muted">
                <span data-role="processed">0</span>/<span data-role="total">0</span>
                diproses
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-link" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" id="btnStartImport" class="btn btn-primary">Mulai Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @push('js')
    <script type="module">
      const form = document.getElementById('customerImportForm');
      const fileInput = document.getElementById('customerImportFile');
      const alerts = document.getElementById('customerImportAlerts');
      const progressWrapper = document.getElementById('customerImportProgress');
      const progressBar = progressWrapper.querySelector('.progress-bar');
      const btnStart = document.getElementById('btnStartImport');
      const spanProcessed = progressWrapper.querySelector('[data-role="processed"]');
      const spanTotal = progressWrapper.querySelector('[data-role="total"]');

      let evtSource = null;

      function toast(msg, type) {
        const t = new Toast({
          body: msg,
          className: type ? `border-0 bg-${type} text-white` : '',
          btnCloseWhite: !!type,
          placement: 'top-right'
        });
        t.show();
        return t;
      }

      function showAlert(message, type = 'info') {
        alerts.insertAdjacentHTML('afterbegin', `
          <div class="alert alert-${type} alert-important alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        `);
      }

      function toggleDismissButton(disable = false) {
        const buttons = document.getElementById('importCustomerModal').querySelectorAll('[data-bs-dismiss="modal"]');
        if (buttons) {
          buttons.forEach(b => b.disabled = disable);
        }
      }

      function closeEventSource() {
        if (evtSource) {
          evtSource.close();
          evtSource = null;
        }
      }

      function listenStream(url) {
        closeEventSource();
        evtSource = new EventSource(url);
        evtSource.addEventListener('progress', (e) => {
          try {
            const data = JSON.parse(e.data);
            if (data.total !== null && data.total !== undefined) {
              spanTotal.textContent = data.total;
            }
            spanProcessed.textContent = data.processed ?? 0;
            progressWrapper.classList.remove('d-none');
            const pct = data.progress ?? 0;
            progressBar.style.width = pct + '%';
            progressBar.setAttribute('aria-valuenow', pct);
            progressBar.textContent = pct + '%';

            if (data.status === 'finished') {
              toast('Import selesai. Memuat ulang data...', 'success');
              closeEventSource();
              setTimeout(() => window.location.reload(), 1200);
            } else if (data.status === 'failed') {
              showAlert('Import gagal: ' + (data.error_message || 'Unknown error'), 'danger');
              btnStart.disabled = false;
              closeEventSource();
            }
          } catch (err) {
            console.error('SSE parse error', err);
          } finally {
            toggleDismissButton(false);
          }
        });
        evtSource.onerror = () => {
          // we will not auto-reconnect because server side ends when finished
        };
      }

      form.addEventListener('submit', async () => {
        if (!fileInput.files.length) {
          showAlert('Silakan pilih file terlebih dahulu.', 'danger');
          return;
        }
        btnStart.disabled = true;
        const t = toast('Mengunggah file dan memulai import...');
        const fd = new FormData();
        fd.append('file', fileInput.files[0]);
        try {
          toggleDismissButton(true);
          const res = await fetch("{{ route('e-billing.master-data.customers.import.store') }}", {
            method: 'POST',
            headers: {
              accept: 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: fd
          });
          if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message || 'Upload gagal');
          }
          const data = await res.json();
          t.hide();
          toast('File berhasil diunggah. Memproses data...');
          listenStream(data.stream_url);
        } catch (e) {
          showAlert('Import gagal: ' + e.message, 'danger');
          btnStart.disabled = false;
          toggleDismissButton(false);
        }
      });

      const dropzone = new Dropzone(".form-dropzone", {
        maxFileSize: 5 * 1024 * 1024,
      });

      document.getElementById('importCustomerModal').addEventListener('hidden.bs.modal', () => {
        // reset form
        form.reset();
        alerts.innerHTML = '';
        progressWrapper.classList.add('d-none');
        progressBar.style.width = '0%';
        progressBar.setAttribute('aria-valuenow', 0);
        progressBar.textContent = '0%';
        spanProcessed.textContent = '0';
        spanTotal.textContent = '0';
        btnStart.disabled = false;
        closeEventSource();
      });
    </script>
  @endpush
</x-e-billing::layouts.panel>
