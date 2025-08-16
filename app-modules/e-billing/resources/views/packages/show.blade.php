<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Data Master
          </div>
          <h2 class="page-title">
            Detail Paket
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <a href="{{ route('e-billing.master-data.packages.edit', $package) }}" class="btn btn-warning">
              Edit
            </a>
            <button class="btn btn-danger"
              onclick="deleteConfirm('{{ route('e-billing.master-data.packages.destroy', $package) }}')">
              Hapus
            </button>
            <a href="{{ route('e-billing.master-data.packages.index') }}" class="btn btn-secondary">
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
              <h3 class="card-title">Informasi Paket</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">Nama Paket</div>
                  <div class="datagrid-content">{{ $package->name }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Deskripsi</div>
                  <div class="datagrid-content">{{ $package->description ?? '-' }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Bandwidth</div>
                  <div class="datagrid-content">{{ $package->bandwidth }} Mbps</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Harga</div>
                  <div class="datagrid-content">Rp{{ number_format($package->price, 0, ',', '.') }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Jatuh Tempo</div>
                  <div class="datagrid-content">{{ $package->due }} Hari</div>
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
                  <div class="datagrid-content">{{ $package->created_at->format('d/m/Y') }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Tanggal Perubahan</div>
                  <div class="datagrid-content">{{ $package->updated_at->format('d/m/Y') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</x-e-billing::layouts.panel>
