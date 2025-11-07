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
            Detail User
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            @can('update', $user)
              <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                <i class="icon ti ti-edit"></i>
                Edit
              </a>
            @endcan
            @can('delete', $user)
              <button type="button" class="btn btn-danger" onclick="deleteConfirm('{{ route('users.destroy', $user) }}')">
                <i class="icon ti ti-trash"></i>
                Hapus
              </button>
            @endcan
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
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
                  <div class="datagrid-content">
                    @if ($user->role === 'admin')
                      <x-common.badge label="Admin" color="red" />
                    @elseif ($user->role === 'operator')
                      <x-common.badge label="Operator" color="blue" />
                    @else
                      <x-common.badge label="Warga" color="green" />
                    @endif
                  </div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Tanggal Dibuat</div>
                  <div class="datagrid-content">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Terakhir Diperbarui</div>
                  <div class="datagrid-content">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
