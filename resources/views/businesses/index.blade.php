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
                        @can('create', \App\Models\Business::class)
                            <a href="{{ route('businesses.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                                <i class="icon ti ti-plus"></i>
                                Tambah Usaha
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
                                            @can('update', $business)
                                                <a href="{{ route('businesses.show', $business) }}"
                                                    class="btn btn-icon btn-primary">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            @endcan
                                            @can('update', $business)
                                                <a href="{{ route('businesses.edit', $business) }}"
                                                    class="btn btn-icon btn-warning">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                            @endcan
                                            @if ($business->status === 'pending')
                                                @can('approve', $business)
                                                    <form action="{{ route('businesses.approve', $business) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-icon btn-success"
                                                            onclick="return confirm('Apakah Anda yakin ingin menyetujui usaha ini?')">
                                                            <i class="ti ti-check"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @can('reject', $business)
                                                    <button type="button" class="btn btn-icon btn-danger"
                                                        data-bs-toggle="modal" data-bs-target="#rejectModal{{ $business->id }}">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                @endcan
                                            @endif
                                            @can('delete', $business)
                                                <form action="{{ route('businesses.destroy', $business) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-danger"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>

                                        @if ($business->status === 'pending')
                                            @can('reject', $business)
                                                <!-- Reject Modal -->
                                                <div class="modal fade" id="rejectModal{{ $business->id }}" tabindex="-1"
                                                    aria-labelledby="rejectModalLabel{{ $business->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('businesses.reject', $business) }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="business_id"
                                                                    value="{{ $business->id }}">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="rejectModalLabel{{ $business->id }}">Tolak Usaha
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Apakah Anda yakin ingin menolak usaha
                                                                        <strong>{{ $business->name }}</strong>?
                                                                    </p>
                                                                    <div class="mb-3">
                                                                        <label for="rejection_reason{{ $business->id }}"
                                                                            class="form-label">Alasan Penolakan <span
                                                                                class="text-danger">*</span></label>
                                                                        <textarea class="form-control @error('rejection_reason') is-invalid @enderror" id="rejection_reason{{ $business->id }}"
                                                                            name="rejection_reason" rows="4" required placeholder="Masukkan alasan penolakan...">{{ old('rejection_reason') }}</textarea>
                                                                        @error('rejection_reason')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-danger">Tolak
                                                                        Usaha</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endcan
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data usaha.</td>
                                </tr>
                            @endforelse
                        </x-slot>
                    </x-datatable>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->has('rejection_reason'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var businessId = '{{ old('business_id') }}';
                if (businessId) {
                    var modal = document.getElementById('rejectModal' + businessId);
                    if (modal) {
                        var bootstrapModal = new bootstrap.Modal(modal);
                        bootstrapModal.show();
                    }
                }
            });
        </script>
    @endif
@endsection
