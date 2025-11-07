@extends('tablar::page')

@section('content')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Manajemen
          </div>
          <h2 class="page-title">
            Manajemen User
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            @can('create', \App\Models\User::class)
              <a href="{{ route('users.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                <i class="icon ti ti-plus"></i>
                Tambah User
              </a>
            @endcan
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
            <x-slot:headerRight>
              <div class="text-muted">
                Role:
                <div class="ms-2 d-inline-block">
                  <select class="form-select form-select-sm" aria-label="Filter role" id="roleFilter">
                    <option value="" {{ request()->role == null ? 'selected' : '' }}>Semua</option>
                    <option value="admin" {{ request()->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="operator" {{ request()->role == 'operator' ? 'selected' : '' }}>Operator</option>
                    <option value="warga" {{ request()->role == 'warga' ? 'selected' : '' }}>Warga</option>
                  </select>
                </div>
              </div>
            </x-slot>

            <x-slot:thead>
              <tr>
                <th>#</th>
                <x-sortable-header field="name" label="Nama" />
                <x-sortable-header field="email" label="Email" />
                <x-sortable-header field="role" label="Role" />
                <x-sortable-header field="created_at" label="Tanggal Dibuat" />
                <th class="w-1">Aksi</th>
              </tr>
            </x-slot>

            <x-slot:tbody>
              @forelse ($users as $user)
                <tr>
                  <td>{{ $loop->iteration + $users->firstItem() - 1 }}</td>
                  <td class="fw-medium">{{ $user->name }}</td>
                  <td>{{ $user->email }}</td>
                  <td>
                    @if ($user->role === 'admin')
                      <x-common.badge label="Admin" color="red" light />
                    @elseif ($user->role === 'operator')
                      <x-common.badge label="Operator" color="blue" light />
                    @else
                      <x-common.badge label="Warga" color="green" light />
                    @endif
                  </td>
                  <td>{{ $user->created_at->format('d/m/Y') }}</td>
                  <td>
                    @can('view', $user)
                      <a href="{{ route('users.show', $user) }}" class="btn btn-icon btn-primary" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="Lihat detail">
                        <i class="ti ti-eye"></i>
                      </a>
                    @endcan
                    @can('update', $user)
                      <a href="{{ route('users.edit', $user) }}" class="btn btn-icon btn-warning" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="Edit">
                        <i class="ti ti-edit"></i>
                      </a>
                    @endcan
                    @can('delete', $user)
                      <button class="btn btn-icon btn-danger"
                        onclick="deleteConfirm('{{ route('users.destroy', $user) }}')" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="Hapus">
                        <i class="ti ti-trash"></i>
                      </button>
                    @endcan
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center">Tidak ada user yang terdaftar.</td>
                </tr>
              @endforelse
            </x-slot>
          </x-datatable>
        </div>
      </div>
    </div>
  </div>

  @push('js')
    <script type="module">
      const roleFilter = document.getElementById('roleFilter');
      if (roleFilter) {
        roleFilter.addEventListener('change', function() {
          const url = new URL(window.location.href);
          if (this.value) {
            url.searchParams.set('role', this.value);
          } else {
            url.searchParams.delete('role');
          }
          window.location.href = url.toString();
        });
      }
    </script>
  @endpush
@endsection
