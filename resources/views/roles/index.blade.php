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
            Manajemen Role
          </h2>
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
              <h3 class="card-title">Daftar Role</h3>
              <div class="ms-auto text-muted">
                Total: <strong>{{ $roles->sum('users_count') }} user</strong>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-vcenter card-table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Nama Role</th>
                    <th>Deskripsi</th>
                    <th>Jumlah User</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($roles as $role)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td class="fw-medium">
                        <x-common.badge :label="$role['name']" light :color="$role['color']" />
                      </td>
                      <td>{{ $role['description'] }}</td>
                      <td>
                        <x-common.badge :label="$role['users_count'] . ' user'" color="blue" />
                      </td>
                      <td>
                        <button class="btn btn-icon invisible"></button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
