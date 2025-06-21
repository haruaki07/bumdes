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
                        Usaha
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('businesses.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                            <i class="icon ti ti-plus"></i>
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
                    <x-datatable tableId="businessesTable" title="Daftar Usaha" :data="$businesses">
                        <x-slot:thead>
                            <tr>
                                <x-sortable-header field="name" label="Nama Usaha" />
                                <x-sortable-header field="businessType.name" label="Jenis" />
                                <x-sortable-header field="owner.name" label="Pemilik" />
                                <x-sortable-header field="location" label="Lokasi" />
                                <x-sortable-header field="status" label="Status" />
                                <x-sortable-header field="created_at" label="Tanggal Pengajuan" />
                                <th class="w-1">Aksi</th>
                            </tr>
                        </x-slot>

                        <x-slot:tbody>
                            @forelse ($businesses as $business)
                                <tr>
                                    <td>{{ $business->name }}</td>
                                    <td>{{ $business->businessType->name }}</td>
                                    <td>{{ $business->owner->name }}</td>
                                    <td>{{ $business->location }}</td>
                                    <td>
                                        <x-modules.business.status-badge :status="$business->status" />
                                    </td>
                                    <td>{{ $business->created_at->format('d M Y H:i') }}</td>
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
                                            <form action="{{ route('businesses.destroy', $business) }}" method="POST"
                                                class="d-inline">
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
                        </x-slot>
                    </x-datatable>
                </div>
            </div>
        </div>
    </div>
@endsection
