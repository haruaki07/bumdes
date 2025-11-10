@extends('tablar::page')

@section('content')
  <!-- Page header -->
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Dashboard
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
                <div class="col-md-4">
                  <a href="{{ route('businesses.create') }}" class="card card-link card-link-pop">
                    <div class="card-body text-center">
                      <div class="text-success mb-3">
                        <i class="ti ti-building-store icon fs-1"></i>
                      </div>
                      <h3 class="card-title">Daftar Usaha</h3>
                      <p class="text-secondary">Daftarkan usaha baru Anda</p>
                    </div>
                  </a>
                </div>
                <div class="col-md-4">
                  <a href="{{ route('funding-requests.create') }}" class="card card-link card-link-pop">
                    <div class="card-body text-center">
                      <div class="text-primary mb-3">
                        <i class="ti ti-coin icon fs-1"></i>
                      </div>
                      <h3 class="card-title">Ajukan Pendanaan</h3>
                      <p class="text-secondary">Ajukan pendanaan untuk usaha</p>
                    </div>
                  </a>
                </div>
                <div class="col-md-4">
                  <a href="{{ route('profile.edit') }}" class="card card-link card-link-pop">
                    <div class="card-body text-center">
                      <div class="text-info mb-3">
                        <i class="ti ti-user icon fs-1"></i>
                      </div>
                      <h3 class="card-title">Profil Saya</h3>
                      <p class="text-secondary">Kelola informasi profil</p>
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
                  <span class="bg-success text-white avatar">
                    <i class="ti ti-building-store"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">
                    {{ $myBusinesses->count() }} Usaha
                  </div>
                  <div class="text-secondary">
                    Total usaha Anda
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
                    <i class="ti ti-coin"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">
                    {{ $myFunding->count() }} Pengajuan
                  </div>
                  <div class="text-secondary">
                    Total pengajuan dana
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
                    <i class="ti ti-check"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">
                    {{ $myFunding->where('status', \App\Enums\FundingRequestStatus::COMPLETED)->count() }} Selesai
                  </div>
                  <div class="text-secondary">
                    Pendanaan lunas
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
                  <span class="bg-warning text-white avatar">
                    <i class="ti ti-clock"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">
                    {{ $myFunding->whereIn('status', [
                            \App\Enums\FundingRequestStatus::SUBMITTED,
                            \App\Enums\FundingRequestStatus::APPROVED,
                            \App\Enums\FundingRequestStatus::MOU_SIGNED,
                        ])->count() }}
                    Menunggu
                  </div>
                  <div class="text-secondary">
                    Perlu tindak lanjut
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- My Businesses -->
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                Usaha Saya
              </h3>
            </div>
            @if ($myBusinesses->count() > 0)
              <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                  <thead>
                    <tr>
                      <th>Nama Usaha</th>
                      <th>Jenis Usaha</th>
                      <th>Status</th>
                      <th>Tanggal Daftar</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($myBusinesses as $business)
                      <tr>
                        <td>
                          <a href="{{ route('businesses.show', $business) }}" class="text-reset fw-bold">
                            {{ $business->name }}
                          </a>
                        </td>
                        <td>{{ $business->businessType->name ?? '-' }}</td>
                        <td>
                          <span
                            class="badge bg-{{ \App\Enums\BusinessStatus::from($business->status)->color() }}-lt text-{{ \App\Enums\BusinessStatus::from($business->status)->color() }}">
                            {{ \App\Enums\BusinessStatus::from($business->status)->label() }}
                          </span>
                        </td>
                        <td class="text-muted">{{ $business->created_at->locale('id')->translatedFormat('d F Y') }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="card-body text-center py-5">
                <div class="empty">
                  <div class="empty-icon">
                    <i class="ti ti-building-store icon fs-1"></i>
                  </div>
                  <p class="empty-title">Belum ada usaha terdaftar</p>
                  <p class="empty-subtitle text-secondary">
                    Mulai dengan mendaftarkan usaha Anda untuk mengajukan pendanaan
                  </p>
                  <div class="empty-action">
                    <a href="{{ route('businesses.create') }}" class="btn btn-primary">
                      <i class="ti ti-plus"></i>
                      Daftarkan Usaha Baru
                    </a>
                  </div>
                </div>
              </div>
            @endif
          </div>
        </div>

        <!-- My Funding Requests -->
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                Pengajuan Pendanaan Saya
              </h3>
            </div>
            @if ($myFunding->count() > 0)
              <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                  <thead>
                    <tr>
                      <th>Usaha</th>
                      <th>Jumlah Pengajuan</th>
                      <th>Dicairkan</th>
                      <th>Status</th>
                      <th>Tanggal</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($myFunding as $funding)
                      <tr>
                        <td>
                          <a href="{{ route('businesses.show', $funding->business) }}"
                            class="fw-bold text-reset">{{ $funding->business->name ?? '-' }}</a>
                        </td>
                        <td>
                          Rp {{ number_format($funding->amount, 0, ',', '.') }}
                        </td>
                        <td>
                          @if ($funding->disbursed_amount > 0)
                            <div class="text-success">
                              Rp {{ number_format($funding->disbursed_amount, 0, ',', '.') }}</div>
                          @else
                            <span class="text-muted">-</span>
                          @endif
                        </td>
                        <td>
                          <span
                            class="badge bg-{{ $funding->status->color() }}-lt text-{{ $funding->status->color() }}">
                            {{ $funding->status->label() }}
                          </span>
                        </td>
                        <td class="text-muted">{{ $funding->created_at->locale('id')->translatedFormat('d F Y') }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="card-body text-center py-5">
                <div class="empty">
                  <div class="empty-icon">
                    <i class="ti ti-coin icon fs-1"></i>
                  </div>
                  <p class="empty-title">Belum ada pengajuan pendanaan</p>
                  <p class="empty-subtitle text-secondary">
                    Ajukan pendanaan untuk usaha Anda yang telah terdaftar
                  </p>
                  <div class="empty-action">
                    @if ($myBusinesses->count() > 0)
                      <a href="{{ route('funding-requests.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus"></i>
                        Ajukan Pendanaan
                      </a>
                    @else
                      <a href="{{ route('businesses.create') }}" class="btn btn-primary">
                        <i class="ti ti-building-store"></i>
                        Daftarkan Usaha Dulu
                      </a>
                    @endif
                  </div>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
