<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Data Master
          </div>
          <h2 class="page-title">
            Detail Perangkat
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            @if (auth('ebil')->user()->can('update-devices'))
              <a href="{{ route('e-billing.master-data.devices.edit', $device) }}" class="btn btn-warning">
                Edit
              </a>
            @endif
            @if (auth('ebil')->user()->can('delete-devices'))
              <button class="btn btn-danger"
                onclick="deleteConfirm('{{ route('e-billing.master-data.devices.destroy', $device) }}')">
                Hapus
              </button>
            @endif
            <a href="{{ route('e-billing.master-data.devices.index') }}" class="btn btn-secondary">
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
              <h3 class="card-title">Informasi Perangkat</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">Kode</div>
                  <div class="datagrid-content">
                    <x-common.badge color="primary" :label="$device->code" light />
                  </div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Merek</div>
                  <div class="datagrid-content">{{ $device->brand }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Model</div>
                  <div class="datagrid-content">{{ $device->model }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Deskripsi</div>
                  <div class="datagrid-content">{{ $device->description ?? '-' }}</div>
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
                  <div class="datagrid-content">{{ $device->created_at->format('d/m/Y') }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Tanggal Perubahan</div>
                  <div class="datagrid-content">{{ $device->updated_at->format('d/m/Y') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</x-e-billing::layouts.panel>
