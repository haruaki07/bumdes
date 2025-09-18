<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Pengaturan
          </div>
          <h2 class="page-title">
            Manajemen User
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <a href="{{ route('e-billing.settings.users.create') }}" class="btn btn-primary d-none d-sm-inline-block">
              <i class="icon ti ti-plus"></i>
              Tambah User
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
          <x-datatable tableId="usersTable" title="Daftar User" :data="$users">
            <x-slot:thead>
              <tr>
                <th>#</th>
                <x-sortable-header field="name" label="Nama" />
                <x-sortable-header field="email" label="Email" />
                <x-sortable-header field="role" label="Peran" />
                <th>Aksi</th>
              </tr>
            </x-slot>

            <x-slot:tbody>
              @forelse ($users as $user)
                <tr>
                  <td>{{ $loop->iteration + $users->firstItem() - 1 }}</td>
                  <td class="fw-medium">{{ $user->name }}</td>
                  <td>{{ $user->email }}</td>
                  <td><x-common.badge :color="$user->role->color()" label="{{ $user->role->label() }}" /></td>
                  <td>
                    <a href="{{ route('e-billing.settings.users.show', $user) }}" class="btn btn-icon btn-primary"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat detail">
                      <i class="ti ti-eye"></i>
                    </a>
                    {{-- @can('update', $user) --}}
                    <a href="{{ route('e-billing.settings.users.edit', $user) }}" class="btn btn-icon btn-warning"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                      <i class="ti ti-edit"></i>
                    </a>
                    {{-- @endcan --}}
                    <button class="btn btn-icon btn-danger"
                      onclick="deleteConfirm('{{ route('e-billing.settings.users.destroy', $user) }}')"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                      <i class="ti ti-trash"></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center">Tidak ada user yang terdaftar.</td>
                </tr>
              @endforelse
            </x-slot>
          </x-datatable>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
