<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Pengaturan
          </div>
          <h2 class="page-title">
            Detail User
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <a href="{{ route('e-billing.settings.users.edit', $user) }}" class="btn btn-warning">Edit</a>
            <button class="btn btn-danger"
              onclick="deleteConfirm('{{ route('e-billing.settings.users.destroy', $user) }}')">Hapus</button>
            <a href="{{ route('e-billing.settings.users.index') }}" class="btn btn-secondary">Kembali</a>
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
              <h3 class="card-title">Informasi User</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">Nama</div>
                  <div class="datagrid-content">{{ $user->name }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Email</div>
                  <div class="datagrid-content">{{ $user->email }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Role</div>
                  <div class="datagrid-content"><x-common.badge :label="$user->getRoleNames()->first()" randomize light />
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
                  <div class="datagrid-content">{{ $user->created_at?->format('d/m/Y H:i') }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Tanggal Perubahan</div>
                  <div class="datagrid-content">{{ $user->updated_at?->format('d/m/Y H:i') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</x-e-billing::layouts.panel>
