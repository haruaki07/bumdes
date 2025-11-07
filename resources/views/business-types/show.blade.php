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
            Detail Jenis Usaha
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            @can('update', $businessType)
              <a href="{{ route('business-types.edit', $businessType) }}" class="btn btn-warning">
                <i class="icon ti ti-edit"></i>
                Edit
              </a>
            @endcan
            @can('delete', $businessType)
              <button type="button" class="btn btn-danger"
                onclick="deleteConfirm(
                  '{{ route('business-types.destroy', $businessType) }}', 'DELETE',
                  { message: 'Apakah Anda yakin ingin menghapus jenis usaha ini?' }
                )">
                <i class="icon ti ti-trash"></i>
                Hapus
              </button>
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
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Informasi Jenis Usaha</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">Nama Jenis Usaha</div>
                  <div class="datagrid-content">{{ $businessType->name }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Deskripsi</div>
                  <div class="datagrid-content">{{ $businessType->description ?? '-' }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Status</div>
                  <div class="datagrid-content">
                    @if ($businessType->is_active)
                      <x-common.badge color="success" label="Aktif" />
                    @else
                      <x-common.badge color="danger" label="Nonaktif" />
                    @endif
                  </div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Jumlah Usaha</div>
                  <div class="datagrid-content">
                    <x-common.badge color="blue" label="{{ $businessType->businesses_count }} usaha" light />
                  </div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Tanggal Dibuat</div>
                  <div class="datagrid-content">{{ $businessType->created_at->format('d M Y H:i') }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Terakhir Diperbarui</div>
                  <div class="datagrid-content">{{ $businessType->updated_at->format('d M Y H:i') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        @if ($businessType->businesses_count > 0)
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Usaha Terkait ({{ $businessType->businesses_count }})</h3>
              </div>
              <div class="card-body">
                <div class="list-group">
                  @foreach ($businessType->businesses()->latest()->take(10)->get() as $business)
                    <a class="list-group-item list-group-item-action" href="{{ route('businesses.show', $business) }}">
                      <div class="row align-items-center">
                        <div class="col text-truncate">
                          <span href="{{ route('businesses.show', $business) }}" class="text-body d-block mb-2">
                            {{ $business->name }}
                          </span>
                          <div class="d-block text-muted text-truncate mt-n1">
                            {{ $business->owner->name }} • {{ $business->location }}
                          </div>
                        </div>
                        <div class="col-auto">
                          <x-modules.business.status-badge :status="$business->status" />
                        </div>
                      </div>
                    </a>
                  @endforeach
                </div>
                @if ($businessType->businesses_count > 10)
                  <div class="card-footer text-center">
                    <a href="{{ route('businesses.index', ['business_type_id' => $businessType->id]) }}"
                      class="btn btn-link">
                      Lihat Semua Usaha
                    </a>
                  </div>
                @endif
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
