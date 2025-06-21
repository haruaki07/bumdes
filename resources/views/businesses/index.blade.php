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
                        Daftar Usaha
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('businesses.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Tambah Usaha
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
                            <h3 class="card-title">Daftar Usaha</h3>
                        </div>
                        <div class="card-body border-bottom py-3">

                            <div class="d-flex">
                                <div class="text-muted">
                                    Show
                                    <form class="mx-2 d-inline-block">
                                        <input type="number" name="limit" class="form-control form-control-sm"
                                            value="{{ request()->limit ?? $businesses->perPage() }}"
                                            aria-label="Jumlah data per halaman" style="width: 45px;">
                                        <input type="submit" hidden />
                                    </form>
                                    entries
                                </div>
                                <div class="ms-auto text-muted">
                                    Search:
                                    <form class="ms-2 d-inline-block">
                                        <input type="text" name="search" class="form-control form-control-sm"
                                            value="{{ request()->search ?? '' }}" aria-label="Cari usaha">
                                        <input type="submit" hidden />
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th>Nama Usaha</th>
                                        <th>Jenis</th>
                                        <th>Pemilik</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
                                        <th class="w-1">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($businesses as $business)
                                        <tr>
                                            <td>{{ $business->name }}</td>
                                            <td>{{ $business->businessType->name }}</td>
                                            <td>{{ $business->owner->name }}</td>
                                            <td>{{ $business->location }}</td>
                                            <td>
                                                <x-modules.business.status-badge :status="$business->status" />
                                            </td>
                                            <td>
                                                <div class="btn-list flex-nowrap">
                                                    <a href="{{ route('businesses.show', $business) }}"
                                                        class="btn btn-icon btn-primary">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    <a href="{{ route('businesses.edit', $business) }}"
                                                        class="btn btn-icon btn-warning">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <form action="{{ route('businesses.destroy', $business) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-icon btn-danger"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data usaha.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer d-flex align-items-center">
                            {!! $businesses->links('tablar::pagination') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
