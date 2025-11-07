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
          <x-datatable tableId="businessTypesTable" title="Daftar Jenis Usaha" :data="$businessTypes">
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
                          onclick="deleteConfirm('{{ route('business-types.destroy', $businessType) }}', 'DELETE', {message: 'Apakah Anda yakin ingin menghapus jenis usaha ini?'})">
                          <i class="ti ti-trash"></i>
                        </button>
                      @endcan
                    </div>
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
