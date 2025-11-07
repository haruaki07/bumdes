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
            Jenis Usaha
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            @can('create', \App\Models\BusinessType::class)
              <a href="{{ route('business-types.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                <i class="icon ti ti-plus"></i>
                Tambah Jenis Usaha
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
          <x-datatable tableId="businessTypesTable" :data="$businessTypes">
            <x-slot:title>
              <div class="d-flex align-items-center">
                <span>Daftar Jenis Usaha</span>
                <div class="d-flex gap-2 align-items-center ms-3 text-muted">
                  <a class="btn btn-link fs-5 p-0 m-0 {{ request()->archive == null ? 'disabled' : '' }}"
                    href="{{ route('business-types.index') }}">
                    Semua
                  </a>
                  <a class="btn btn-link fs-5 p-0 m-0 {{ request()->archive == 'true' ? 'disabled' : '' }}"
                    href="{{ route('business-types.index', ['archive' => 'true']) }}">
                    Arsip
                  </a>
                </div>
              </div>
            </x-slot>
            <x-slot:thead>
              <tr>
                <x-sortable-header field="name" label="Nama Jenis Usaha" />
                <x-sortable-header field="description" label="Deskripsi" />
                <th>Jumlah Usaha</th>
                <x-sortable-header field="is_active" label="Status" />
                <x-sortable-header field="created_at" label="Tanggal Dibuat" />
                <th class="w-1">Aksi</th>
              </tr>
            </x-slot>

            <x-slot:tbody>
              @forelse ($businessTypes as $businessType)
                <tr>
                  <td>{{ $businessType->name }}</td>
                  <td>{{ $businessType->description ?? '-' }}</td>
                  <td>
                    <x-common.badge color="blue" label="{{ $businessType->businesses_count }} usaha" light />
                  </td>
                  <td>
                    @if ($businessType->is_active)
                      <x-common.badge color="success" label="Aktif" />
                    @else
                      <x-common.badge color="danger" label="Nonaktif" />
                    @endif
                  </td>
                  <td>{{ $businessType->created_at->format('d M Y H:i') }}</td>
                  <td>
                    @if (request()->archive != 'true')
                      <div class="btn-list flex-nowrap">
                        @can('view', $businessType)
                          <a href="{{ route('business-types.show', $businessType) }}" class="btn btn-icon btn-primary">
                            <i class="ti ti-eye"></i>
                          </a>
                        @endcan
                        @can('update', $businessType)
                          <a href="{{ route('business-types.edit', $businessType) }}" class="btn btn-icon btn-warning">
                            <i class="ti ti-edit"></i>
                          </a>
                        @endcan
                        @can('delete', $businessType)
                          <button type="button" class="btn btn-icon btn-danger"
                            onclick="deleteConfirm('{{ route('business-types.destroy', $businessType) }}', 'DELETE', {message: 'Data jenis usaha akan disembunyikan dari daftar aktif dan dapat dipulihkan kapan saja melalui menu Arsip.'})">
                            <i class="ti ti-trash"></i>
                          </button>
                        @endcan
                      </div>
                    @else
                      <div class="btn-list flex-nowrap">
                        @can('update', $businessType)
                          <button type="button" class="btn btn-icon btn-primary"
                            onclick="deleteConfirm(
                              '{{ route('business-types.restore', ['id' => $businessType->id]) }}',
                              'PUT',
                              {
                                title: 'Pulihkan Jenis Usaha?',
                                message: '<p>Data jenis usaha akan dipulihkan dan muncul kembali di daftar aktif.</p>',
                                buttons: {
                                  cancel: { label: 'Batal' },
                                  confirm: { label: 'Pulihkan', className: 'btn-primary' },
                                }
                              }
                            )"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Pulihkan">
                            <i class="ti ti-restore"></i>
                          </button>
                        @endcan
                        @can('delete', $businessType)
                          <button type="button" class="btn btn-icon btn-danger"
                            onclick="deleteConfirm(
                              '{{ route('business-types.destroy-trashed', ['id' => $businessType->id]) }}',
                              'DELETE',
                              {
                                title: 'Hapus Permanen Jenis Usaha?',
                                message: '<p>Data jenis usaha akan dihapus <b>secara permanen</b> dan <b>tidak dapat dipulihkan</b>.</p>'
                              }
                            )"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Permanen">
                            <i class="ti ti-trash"></i>
                          </button>
                        @endcan
                      </div>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center">Tidak ada data</td>
                </tr>
              @endforelse
            </x-slot>
          </x-datatable>
        </div>
      </div>
    </div>
  </div>
@endsection
