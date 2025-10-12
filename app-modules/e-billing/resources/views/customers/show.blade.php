<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Data Master
          </div>
          <h2 class="page-title">
            Detail Pelanggan
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <a href="{{ route('e-billing.master-data.customers.edit', $customer) }}" class="btn btn-warning">
              Edit
            </a>
            <button class="btn btn-danger"
              onclick="deleteConfirm('{{ route('e-billing.master-data.customers.destroy', $customer) }}')">
              Hapus
            </button>
            <a href="{{ route('e-billing.master-data.customers.index') }}" class="btn btn-secondary">
              Kembali
            </a>
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
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Informasi Pelanggan</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">ID Pelanggan</div>
                  <div class="datagrid-content">{{ $customer->customer_id }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Nama Pelanggan</div>
                  <div class="datagrid-content">{{ $customer->name }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Email</div>
                  <div class="datagrid-content">{{ $customer->email }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Nomor HP</div>
                  <div class="datagrid-content">{{ $customer->phone?->formatNational() }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Site/Wilayah</div>
                  <div class="datagrid-content">{{ $customer->site->name }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Alamat</div>
                  <div class="datagrid-content">{{ $customer->address }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Latitude</div>
                  <div class="datagrid-content">{{ $customer->latitude ?? '-' }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Longitude</div>
                  <div class="datagrid-content">{{ $customer->longitude ?? '-' }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Map URL</div>
                  <div class="datagrid-content">
                    @if ($customer->map_url)
                      <a href="{{ $customer->map_url }}" target="_blank"
                        rel="noopener noreferrer">{{ $customer->map_url }}</a>
                    @else
                      -
                    @endif
                  </div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Jatuh Tempo</div>
                  <div class="datagrid-content">Setiap tanggal {{ $customer->due }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Pengingat Sebelum Jatuh Tempo</div>
                  <div class="datagrid-content">{{ $customer->due_reminder_days ?? '-' }} Hari</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Batas Waktu Pembayaran</div>
                  <div class="datagrid-content">{{ $customer->grace_period ?? '-' }} Hari</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Tanggal Registrasi</div>
                  <div class="datagrid-content">{{ $customer->registration_date->format('d/m/Y') }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Status</div>
                  <div class="datagrid-content">
                    <x-e-billing::modules.customer.status-badge :status="$customer->status" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Informasi Paket/Langganan</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">Nama Paket</div>
                  <div class="datagrid-content">{{ $customer->package->name }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Bandwidth</div>
                  <div class="datagrid-content">
                    <x-common.badge color="info" label="{{ $customer->package->bandwidth }} Mbps" />
                  </div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Harga</div>
                  <div class="datagrid-content">
                    <x-common.badge color="info"
                      label="Rp{{ number_format($customer->package->price, 0, ',', '.') }}" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Siklus Tagihan</h3>
            </div>
            <div class="card-body">
              <p class="text-muted mb-2">
                Simulasi 6 bulan ke depan berdasarkan tanggal jatuh tempo, batas waktu
                pembayaran, dan paket. "Tanggal Isolir" adalah estimasi layanan dinonaktifkan jika belum bayar.
              </p>
              <div class="table-responsive">
                <table class="table table-bordered" id="billingPreviewTable">
                  <thead>
                    <tr>
                      <th>Bulan</th>
                      <th>Masa Aktif</th>
                      <th>Tanggal Jatuh Tempo</th>
                      <th>Tanggal Isolir</th>
                      <th>Periode (Range)</th>
                      <th>Nominal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colspan="6" class="text-muted text-center">Memuat...</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Informasi Perangkat</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">Merek Perangkat</div>
                  <div class="datagrid-content">{{ $customer->device?->brand ?? '-' }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Model</div>
                  <div class="datagrid-content">{{ $customer->device?->model ?? '-' }}</div>
                </div>


                <div class="datagrid-item">
                  <div class="datagrid-title">Nomor Seri</div>
                  <div class="datagrid-content">
                    <x-common.badge color="info" label="{{ $customer->serial_number ?? '-' }}" />
                  </div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">MAC Address</div>
                  <div class="datagrid-content">
                    <x-common.badge color="info" label="{{ $customer->mac_address ?? '-' }}" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Meta</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">Tanggal Pembuatan</div>
                  <div class="datagrid-content">{{ $customer->created_at->format('d/m/Y') }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Tanggal Perubahan</div>
                  <div class="datagrid-content">{{ $customer->updated_at->format('d/m/Y') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Tiket Pelanggan</h3>
              <div class="card-actions">
                <a href="{{ route('e-billing.tickets.create', ['customer_id' => $customer->id]) }}"
                  class="btn btn-sm btn-primary">Buat Tiket</a>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Kode</th>
                      <th>Subjek</th>
                      <th>Prioritas</th>
                      <th>Status</th>
                      <th>Aktivitas Terakhir</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($customer->tickets()->latest('last_activity_at')->limit(10)->get() as $t)
                      <tr>
                        <td class="fw-medium">{{ $t->code }}</td>
                        <td>{{ $t->subject }}</td>
                        <td><x-common.badge :color="$t->priority->color()" :label="$t->priority->label()" /></td>
                        <td><x-common.badge :color="$t->status->color()" :label="$t->status->label()" /></td>
                        <td>{{ $t->last_activity_at?->diffForHumans() }}</td>
                        <td class="text-end"><a href="{{ route('e-billing.tickets.show', $t) }}"
                            class="btn btn-sm btn-primary">Detail</a></td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada tiket.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>


      </div>
    </div>
  </div>

  <script type="module">
    const tableBody = document.querySelector('#billingPreviewTable tbody');
    if (!tableBody) {
      throw new Error('Table body not found');
    }

    function formatDate(d) {
      return d.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
      });
    }

    const cycles = getBillingCycles({
      due: {{ $customer->due }},
      grace: {{ $customer->grace_period }}
    });

    tableBody.innerHTML = cycles.map(cycle => {
      const monthLabel = new Date(cycle.due_date).toLocaleDateString("id-ID", {
        month: "long",
        year: "numeric",
      });

      return `<tr>
        <td>${monthLabel}</td>
        <td>${cycle.duration} Bulan</td>
        <td>${formatDate(cycle.due_date)}</td>
        <td>${formatDate(cycle.isolation_date)}</td>
        <td>${formatDate(cycle.period.start)} - ${formatDate(cycle.period.end)}</td>
        <td>${formatRupiah({{ $customer->package->price }})}</td>
      </tr>`;
    }).join('');
  </script>
</x-e-billing::layouts.panel>
