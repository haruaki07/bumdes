<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Data Master
          </div>
          <h2 class="page-title">
            Site
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            {{-- @can('create', \App\Models\BusinessRegistration::class) --}}
            <a href="{{ route('e-billing.master-data.sites.create') }}" class="btn btn-primary">
              <i class="icon ti ti-plus"></i>
              Tambah Site
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
          <x-datatable tableId="sitesTable" title="Daftar Site" :data="$sites">
            <x-slot:thead>
              <tr>
                <th>#</th>
                <x-sortable-header field="code" label="Kode" />
                <x-sortable-header field="name" label="Nama" />
                <x-sortable-header field="description" label="Deskripsi" />
                <th>Aksi</th>
              </tr>
            </x-slot>

            <x-slot:tbody>
              @forelse ($sites as $site)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td><x-common.badge color="primary" :label="$site->code" light /></td>
                  <td class="fw-medium">{{ $site->name }}</td>
                  <td title="{{ $site->description }}">{{ Str::limit($site->description, 30) ?? '-' }}</td>
                  <td>
                    <a href="{{ route('e-billing.master-data.sites.show', $site) }}" class="btn btn-icon btn-primary"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat detail">
                      <i class="ti ti-eye"></i>
                    </a>
                    {{-- @can('update', $site) --}}
                    <a href="{{ route('e-billing.master-data.sites.edit', $site) }}" class="btn btn-icon btn-warning"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                      <i class="ti ti-edit"></i>
                    </a>
                    {{-- @endcan --}}
                    <button class="btn btn-icon btn-danger"
                      onclick="deleteConfirm('{{ route('e-billing.master-data.sites.destroy', $site) }}')"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                      <i class="ti ti-trash"></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center">Tidak ada site yang terdaftar.</td>
                </tr>
              @endforelse
            </x-slot>
          </x-datatable>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
