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
                        Detail Usaha
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        @can('update', $business)
                            <a href="{{ route('businesses.edit', $business) }}" class="btn btn-warning">
                                <i class="icon ti ti-edit"></i>
                                Edit Usaha
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Usaha</h3>
                        </div>
                        <div class="card-body">
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Nama Usaha</div>
                                    <div class="datagrid-content">{{ $business->name }}</div>
                                </div>

                                <div class="datagrid-item">
                                    <div class="datagrid-title">Jenis Usaha</div>
                                    <div class="datagrid-content">{{ $business->businessType->name }}</div>
                                </div>

                                <div class="datagrid-item">
                                    <div class="datagrid-title">Pemilik</div>
                                    <div class="datagrid-content">{{ $business->owner->name }}</div>
                                </div>

                                <div class="datagrid-item">
                                    <div class="datagrid-title">Status</div>
                                    <div class="datagrid-content">
                                        <x-modules.business.status-badge :status="$business->status" />
                                    </div>
                                </div>

                                <div class="datagrid-item">
                                    <div class="datagrid-title">Lokasi</div>
                                    <div class="datagrid-content">{{ $business->location }}</div>
                                </div>

                                <div class="datagrid-item">
                                    <div class="datagrid-title">Nomor Telepon</div>
                                    <div class="datagrid-content">{{ $business->contact_phone }}</div>
                                </div>

                                <div class="datagrid-item">
                                    <div class="datagrid-title">Email</div>
                                    <div class="datagrid-content">{{ $business->contact_email ?: '-' }}</div>
                                </div>

                                <div class="datagrid-item">
                                    <div class="datagrid-title">Tanggal Registrasi</div>
                                    <div class="datagrid-content">{{ $business->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="datagrid-title">Deskripsi</div>
                                <div class="datagrid-content">{{ $business->description }}</div>
                            </div>

                            @if ($business->rejection_reason)
                                <div class="mt-4">
                                    <div class="datagrid-title">Alasan Penolakan</div>
                                    <div class="datagrid-content text-danger">{{ $business->rejection_reason }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($business->fundingRequests->isNotEmpty())
                    <div class="col-12 mt-3">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Riwayat Pengajuan Dana</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jumlah</th>
                                                <th>Tujuan</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($business->fundingRequests as $request)
                                                <tr>
                                                    <td>{{ $request->created_at->format('d/m/Y') }}</td>
                                                    <td>Rp {{ number_format($request->amount, 0, ',', '.') }}</td>
                                                    <td>{{ $request->purpose }}</td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'warning') }}">
                                                            {{ ucfirst($request->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
