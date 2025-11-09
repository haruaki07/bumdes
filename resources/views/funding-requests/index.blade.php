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
            {{ Auth::user()->role === 'warga' ? 'Pengajuan Pendanaan Saya' : 'Pengajuan Pendanaan' }}
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            @can('create', \App\Models\FundingRequest::class)
              <a href="{{ route('funding-requests.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                <i class="icon ti ti-plus"></i>
                Ajukan Pendanaan
              </a>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <!-- Summary Cards for Admin/Operator -->
      @if (Auth::user()->role !== 'warga')
        <div class="row row-cards mb-3">
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Pengajuan Baru</div>
                </div>
                <div class="h1 mb-0">{{ $fundingRequests->where('status.value', 'submitted')->count() }}</div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Disetujui</div>
                </div>
                <div class="h1 mb-0">
                  {{ $fundingRequests->whereIn('status.value', ['approved', 'mou_signed', 'ready_to_disburse'])->count() }}
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Dicairkan</div>
                </div>
                <div class="h1 mb-0">{{ $fundingRequests->whereIn('status.value', ['disbursed', 'repaying'])->count() }}
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Selesai</div>
                </div>
                <div class="h1 mb-0 text-success">{{ $fundingRequests->where('status.value', 'completed')->count() }}
                </div>
              </div>
            </div>
          </div>
        </div>
      @endif

      @include('tablar::common.alert')

      <div class="row row-deck row-cards">
        <div class="col-12">
          <x-datatable tableId="fundingRequestsTable" title="Daftar Pengajuan Pendanaan" :data="$fundingRequests">
            <x-slot:thead>
              <tr>
                <th>ID</th>
                @if (Auth::user()->role !== 'warga')
                  <x-sortable-header field="user.name" label="Pemohon" />
                @endif
                <x-sortable-header field="business.name" label="Usaha" />
                <th class="text-end">Jumlah</th>
                <x-sortable-header field="status" label="Status" />
                <th>Progress</th>
                <x-sortable-header field="created_at" label="Tanggal Pengajuan" />
                <th class="w-1">Aksi</th>
              </tr>
            </x-slot>

            <x-slot:tbody>
              @forelse ($fundingRequests as $fundingRequest)
                <tr>
                  <td>
                    <span class="text-muted">#{{ $fundingRequest->id }}</span>
                  </td>
                  @if (Auth::user()->role !== 'warga')
                    <td>
                      <div>{{ $fundingRequest->user->name }}</div>
                      <div class="text-muted small">{{ $fundingRequest->user->email }}</div>
                    </td>
                  @endif
                  <td>
                    <strong>{{ $fundingRequest->business->name }}</strong>
                    <div class="text-muted small">{{ $fundingRequest->business->businessType->name }}</div>
                  </td>
                  <td class="text-end">
                    <strong>Rp {{ number_format($fundingRequest->amount, 0, ',', '.') }}</strong>
                    @if ($fundingRequest->interest_rate > 0)
                      <div class="text-muted small">Bunga: {{ $fundingRequest->interest_rate }}%</div>
                    @endif
                  </td>
                  <td>
                    <x-modules.funding-request.status-badge :status="$fundingRequest->status" />
                  </td>
                  <td>
                    @if (
                        $fundingRequest->status->value === 'disbursed' ||
                            $fundingRequest->status->value === 'repaying' ||
                            $fundingRequest->status->value === 'completed')
                      @php
                        $progress =
                            $fundingRequest->disbursed_amount > 0
                                ? ($fundingRequest->getTotalRepaidAttribute() / $fundingRequest->disbursed_amount) * 100
                                : 0;
                      @endphp
                      <div style="min-width: 150px;">
                        <div class="d-flex justify-content-between mb-1">
                          <span class="small">{{ number_format($progress, 0) }}%</span>
                        </div>
                        <div class="progress progress-sm">
                          <div class="progress-bar bg-{{ $progress >= 100 ? 'success' : 'primary' }}"
                            style="width: {{ $progress }}%" role="progressbar">
                          </div>
                        </div>
                        <div class="small text-muted mt-1">
                          Rp {{ number_format($fundingRequest->getTotalRepaidAttribute(), 0, ',', '.') }} /
                          Rp {{ number_format($fundingRequest->disbursed_amount, 0, ',', '.') }}
                        </div>
                      </div>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    <span class="text-muted">{{ $fundingRequest->created_at->format('d M Y') }}</span>
                    <div class="small text-muted">{{ $fundingRequest->created_at->diffForHumans() }}</div>
                  </td>
                  <td>
                    <div class="btn-list flex-nowrap">
                      <a href="{{ route('funding-requests.show', $fundingRequest) }}" class="btn">
                        Detail
                      </a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="{{ Auth::user()->role !== 'warga' ? '8' : '7' }}" class="text-center py-5">
                    <div class="empty">
                      <div class="empty-icon">
                        <i class="ti ti-folder-off"></i>
                      </div>
                      <p class="empty-title">Belum ada pengajuan pendanaan</p>
                      @can('create', \App\Models\FundingRequest::class)
                        <p class="empty-subtitle text-muted">
                          Klik tombol "Ajukan Pendanaan" untuk membuat pengajuan baru
                        </p>
                        <div class="empty-action">
                          <a href="{{ route('funding-requests.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus"></i> Ajukan Pendanaan
                          </a>
                        </div>
                      @endcan
                    </div>
                  </td>
                </tr>
              @endforelse
            </x-slot>
          </x-datatable>
        </div>
      </div>
    </div>
  </div>
@endsection
