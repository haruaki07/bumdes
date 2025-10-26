<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Data Master
          </div>
          <h2 class="page-title">
            Detail Site
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            @if (auth('ebil')->user()->can('update-sites'))
              <a href="{{ route('e-billing.master-data.sites.edit', $site) }}" class="btn btn-warning">
                Edit
              </a>
            @endif
            @if (auth('ebil')->user()->can('delete-sites'))
              <button class="btn btn-danger"
                onclick="deleteConfirm('{{ route('e-billing.master-data.sites.destroy', $site) }}')">
                Hapus
              </button>
            @endif
            <a href="{{ route('e-billing.master-data.sites.index') }}" class="btn btn-secondary">
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
              <h3 class="card-title">Informasi Site</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">Kode</div>
                  <div class="datagrid-content">
                    <x-common.badge color="primary" :label="$site->code" light />
                  </div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Nama Site</div>
                  <div class="datagrid-content">{{ $site->name }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Deskripsi</div>
                  <div class="datagrid-content">{{ $site->description ?? '-' }}</div>
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
                  <div class="datagrid-content">{{ $site->created_at->format('d/m/Y') }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Tanggal Perubahan</div>
                  <div class="datagrid-content">{{ $site->updated_at->format('d/m/Y') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</x-e-billing::layouts.panel>
