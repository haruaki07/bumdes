@extends('tablar::page')

@section('content')
  <!-- Page header -->
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Dashboard Operator
          </div>
          <h2 class="page-title">
            Selamat Datang, {{ Auth::user()->name }}
          </h2>
        </div>
      </div>
    </div>
  </div>

  <!-- Page body -->
  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <div class="row row-deck row-cards">
        <!-- Quick Actions -->
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                Aksi Cepat
              </h3>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-3">
                  <a href="{{ route('funding-requests.index') }}" class="card card-link card-link-pop">
                    <div class="card-body text-center">
                      <div class="text-warning mb-3">
                        <i class="ti ti-coin icon fs-1"></i>
                      </div>
                      <h3 class="card-title">Kelola Pendanaan</h3>
                      <p class="text-secondary small">Proses pengajuan pendanaan</p>
                    </div>
                  </a>
                </div>
                <div class="col-md-3">
                  <a href="{{ route('businesses.index') }}" class="card card-link card-link-pop">
                    <div class="card-body text-center">
                      <div class="text-success mb-3">
                        <i class="ti ti-building-store icon fs-1"></i>
                      </div>
                      <h3 class="card-title">Kelola Usaha</h3>
                      <p class="text-secondary small">Lihat semua usaha</p>
                    </div>
                  </a>
                </div>
                <div class="col-md-3">
                  <a href="{{ route('users.index') }}" class="card card-link card-link-pop">
                    <div class="card-body text-center">
                      <div class="text-info mb-3">
                        <i class="ti ti-users icon fs-1"></i>
                      </div>
                      <h3 class="card-title">Kelola Warga</h3>
                      <p class="text-secondary small">Manajemen pengguna</p>
                    </div>
                  </a>
                </div>
                <div class="col-md-3">
                  <a href="{{ route('dashboard') }}" class="card card-link card-link-pop">
                    <div class="card-body text-center">
                      <div class="text-primary mb-3">
                        <i class="ti ti-chart-bar icon fs-1"></i>
                      </div>
                      <h3 class="card-title">Laporan</h3>
                      <p class="text-secondary small">Lihat dashboard lengkap</p>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-warning text-white avatar">
                    <i class="ti ti-alert-circle"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">
                    {{ $stats['pending_funding_count'] }} Menunggu
                  </div>
                  <div class="text-secondary">
                    Pendanaan perlu diproses
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-success text-white avatar">
                    <i class="ti ti-building-store"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">
                    {{ $stats['total_businesses'] }} Usaha
                  </div>
                  <div class="text-secondary">
                    Total usaha terdaftar
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-primary text-white avatar">
                    <i class="ti ti-check"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">
                    {{ $stats['active_businesses'] }} Aktif
                  </div>
                  <div class="text-secondary">
                    Usaha aktif beroperasi
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-indigo text-white avatar">
                    <i class="ti ti-cash"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">
                    Rp {{ number_format($stats['total_funding'], 0, ',', '.') }}
                  </div>
                  <div class="text-secondary">
                    Total dana dicairkan
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Funding Requests (Priority) -->
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-warning-lt">
              <h3 class="card-title">
                Pengajuan Pendanaan Menunggu Persetujuan
              </h3>
              <div class="card-actions">
                <span class="badge bg-warning text-white">{{ $pendingFunding->count() }} Pending</span>
              </div>
            </div>
            @if ($pendingFunding->count() > 0)
              <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                  <thead>
                    <tr>
                      <th>Usaha</th>
                      <th>Pemilik</th>
                      <th>Jumlah</th>
                      <th>Tujuan</th>
                      <th>Tanggal Ajuan</th>
                      <th class="w-1">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($pendingFunding as $funding)
                      <tr>
                        <td>
                          <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2 bg-warning-lt">
                              <i class="ti ti-building-store"></i>
                            </div>
                            <div>
                              <div class="fw-bold">{{ $funding->business->name ?? '-' }}</div>
                              <div class="text-muted small">{{ $funding->business->businessType->name ?? '-' }}</div>
                            </div>
                          </div>
                        </td>
                        <td>
                          <div>{{ $funding->business->owner->name ?? '-' }}</div>
                          <div class="text-muted small">{{ $funding->business->owner->email ?? '-' }}</div>
                        </td>
                        <td>
                          <div class="fw-bold text-warning">Rp {{ number_format($funding->amount, 0, ',', '.') }}</div>
                        </td>
                        <td>
                          <div class="text-truncate" style="max-width: 200px;">{{ $funding->purpose }}</div>
                        </td>
                        <td class="text-muted">
                          {{ $funding->created_at->diffForHumans() }}
                          <div class="small">{{ $funding->created_at->locale('id')->translatedFormat('d M Y H:i') }}
                          </div>
                        </td>
                        <td>
                          <div class="btn-list flex-nowrap">
                            <a href="{{ route('funding-requests.show', $funding) }}" class="btn btn-sm btn-primary">
                              <i class="ti ti-eye me-1"></i>Review
                            </a>
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="card-body text-center py-4">
                <div class="text-success mb-2">
                  <i class="ti ti-circle-check icon fs-1"></i>
                </div>
                <p class="text-muted mb-0">Tidak ada pengajuan pendanaan yang menunggu</p>
              </div>
            @endif
          </div>
        </div>

        <!-- Recent Approved Funding -->
        @if ($recentApproved->count() > 0)
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="ti ti-check me-2"></i>Pendanaan Disetujui (Perlu Tindak Lanjut)
                </h3>
              </div>
              <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                  <thead>
                    <tr>
                      <th>Usaha</th>
                      <th>Jumlah</th>
                      <th>Status</th>
                      <th>Tanggal Update</th>
                      <th class="w-1">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($recentApproved as $funding)
                      <tr>
                        <td>
                          <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2 bg-success-lt">
                              <i class="ti ti-building-store"></i>
                            </div>
                            <div>
                              <div class="fw-bold">{{ $funding->business->name ?? '-' }}</div>
                              <div class="text-muted small">{{ $funding->business->owner->name ?? '-' }}</div>
                            </div>
                          </div>
                        </td>
                        <td>
                          <div class="fw-bold">Rp {{ number_format($funding->amount, 0, ',', '.') }}</div>
                        </td>
                        <td>
                          <span
                            class="badge bg-{{ $funding->status->color() }}-lt text-{{ $funding->status->color() }}">
                            {{ $funding->status->label() }}
                          </span>
                        </td>
                        <td class="text-muted">
                          {{ $funding->updated_at->diffForHumans() }}
                        </td>
                        <td>
                          <a href="{{ route('funding-requests.show', $funding) }}" class="btn btn-sm btn-icon">
                            <i class="ti ti-eye"></i>
                          </a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        @endif

        <!-- Recent Businesses -->
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                Usaha Terdaftar Terbaru
              </h3>
              <div class="card-actions">
                <a href="{{ route('businesses.index') }}" class="btn btn-primary">
                  Lihat Semua
                  <i class="ti ti-chevron-right ms-1"></i>
                </a>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-vcenter card-table table-hover">
                <thead>
                  <tr>
                    <th>Nama Usaha</th>
                    <th>Jenis</th>
                    <th>Pemilik</th>
                    <th>Status</th>
                    <th>Tanggal Daftar</th>
                    <th class="w-1">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($recentBusinesses as $business)
                    <tr>
                      <td>
                        <div class="fw-bold">{{ $business->name }}</div>
                      </td>
                      <td>{{ $business->businessType->name ?? '-' }}</td>
                      <td>
                        <div>{{ $business->owner->name ?? '-' }}</div>
                        <div class="text-muted small">{{ $business->owner->email ?? '-' }}</div>
                      </td>
                      <td>
                        <span
                          class="badge bg-{{ \App\Enums\BusinessStatus::from($business->status)->color() }}-lt text-{{ \App\Enums\BusinessStatus::from($business->status)->color() }}">
                          {{ \App\Enums\BusinessStatus::from($business->status)->label() }}
                        </span>
                      </td>
                      <td class="text-muted">{{ $business->created_at->locale('id')->translatedFormat('d M Y') }}</td>
                      <td>
                        <a href="{{ route('businesses.show', $business) }}" class="btn btn-icon btn-primary"
                          data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Detail Usaha">
                          <i class="ti ti-eye"></i>
                        </a>
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
