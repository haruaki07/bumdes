<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Pengaturan
          </div>
          <h2 class="page-title">
            Manajemen Role
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <a href="{{ route('e-billing.settings.roles.create') }}" class="btn btn-primary">
              <i class="icon ti ti-plus"></i>
              Tambah Role
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
          <x-datatable tableId="rolesTable" title="Daftar Role" :data="$roles">
            <x-slot:thead>
              <tr>
                <th>#</th>
                <x-sortable-header field="name" label="Nama" />
                <x-sortable-header field="description" label="Deskripsi" />
                <th>Menu</th>
                <x-sortable-header field="updated_at" label="Tanggal Diperbarui" />
                <th>Aksi</th>
              </tr>
            </x-slot>

            <x-slot:tbody>
              @forelse ($roles as $role)
                <tr>
                  <td>{{ $loop->iteration + $roles->firstItem() - 1 }}</td>
                  <td class="fw-medium">{{ $role->name }}</td>
                  <td>{{ $role->description ?? '-' }}</td>
                  <td>
                    @if ($role->name === 'admin')
                      <span class="badge bg-red-lt text-red-lt-fg">Semua Menu</span>
                    @else
                      @php
                        $sorted = $role->permissions
                            ->unique('menu')
                            ->sortBy(fn($item) => array_search($item->menu_group, $groupOrder))
                            ->pluck('menu');
                      @endphp

                      @foreach ($sorted as $menu)
                        <x-common.badge :label="$menu" randomize light />
                      @endforeach
                    @endif
                  </td>
                  <td>{{ $role->updated_at?->format('d/m/Y H:i') }}</td>
                  <td>
                    @if ($role->name !== 'admin')
                      <a href="{{ route('e-billing.settings.roles.edit', $role) }}" class="btn btn-icon btn-primary"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat detail">
                        <i class="ti ti-eye"></i>
                      </a>
                      {{-- @endcan --}}
                      <button class="btn btn-icon btn-danger"
                        onclick="deleteConfirm('{{ route('e-billing.settings.roles.destroy', $role) }}')"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                        <i class="ti ti-trash"></i>
                      </button>
                    @else
                      <div style="height:36px"></div>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center">Tidak ada role yang terdaftar.</td>
                </tr>
              @endforelse
            </x-slot>
          </x-datatable>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
