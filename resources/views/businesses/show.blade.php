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
                        @if ($business->status === 'pending')
                            @can('approve', $business)
                                <form action="{{ route('businesses.approve', $business) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success"
                                        onclick="return confirm('Apakah Anda yakin ingin menyetujui usaha ini?')">
                                        <i class="icon ti ti-check"></i>
                                        Setujui Usaha
                                    </button>
                                </form>
                            @endcan
                            @can('reject', $business)
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                    <i class="icon ti ti-x"></i>
                                    Tolak Usaha
                                </button>
                            @endcan
                        @endif
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

    @if ($business->status === 'pending')
        @can('reject', $business)
            <!-- Reject Modal -->
            <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('businesses.reject', $business) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="rejectModalLabel">Tolak Usaha</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Apakah Anda yakin ingin menolak usaha <strong>{{ $business->name }}</strong>?</p>
                                <div class="mb-3">
                                    <label for="rejection_reason" class="form-label">Alasan Penolakan <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control @error('rejection_reason') is-invalid @enderror" id="rejection_reason"
                                        name="rejection_reason" rows="4" required placeholder="Masukkan alasan penolakan...">{{ old('rejection_reason') }}</textarea>
                                    @error('rejection_reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Tolak Usaha</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    @endif

    @if ($errors->has('rejection_reason'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
                rejectModal.show();
            });
        </script>
    @endif
@endsection
