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
                  <div class="datagrid-content">{{ $customer->phone }}</div>
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
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
