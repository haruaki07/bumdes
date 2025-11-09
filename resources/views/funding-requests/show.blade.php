@php
  use App\Enums\FundingRequestStatus;
@endphp

@extends('tablar::page')

@section('content')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">
            Detail Pengajuan Pendanaan #{{ $fundingRequest->id }}
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            {{-- Actions based on status and role --}}
            @can('approve', $fundingRequest)
              <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                <i class="icon ti ti-check"></i> Setujui
              </button>
            @endcan

            @can('reject', $fundingRequest)
              <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="icon ti ti-x"></i> Tolak
              </button>
            @endcan

            @can('uploadMou', $fundingRequest)
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadMouModal">
                <i class="icon ti ti-file-upload"></i> Unggah MOU
              </button>
            @endcan

            @can('signMou', $fundingRequest)
              <button type="button" class="btn btn-indigo" data-bs-toggle="modal" data-bs-target="#signMouModal">
                <i class="icon ti ti-file-check"></i> Tandatangani MOU
              </button>
            @endcan

            @can('disburse', $fundingRequest)
              <button type="button" class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#disburseModal">
                <i class="icon ti ti-cash"></i> Cairkan Dana
              </button>
            @endcan

            @can('recordRepayment', $fundingRequest)
              <button type="button" class="btn btn-orange" data-bs-toggle="modal" data-bs-target="#repaymentModal">
                <i class="icon ti ti-coin"></i> Bayar Cicilan
              </button>
            @endcan
            <a class="btn btn-secondary" href="{{ route('funding-requests.index') }}">Kembali</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <div class="row row-cards">
        {{-- Main Information --}}
        <div class="col-lg-8">
          <div class="card mb-3">
            <div class="card-header">
              <h3 class="card-title">Informasi Pengajuan</h3>
              <div class="card-actions">
                <x-modules.funding-request.status-badge :status="$fundingRequest->status" />
              </div>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">Pemohon</div>
                  <div class="datagrid-content">
                    <div>{{ $fundingRequest->user->name }}</div>
                    <div class="text-muted small">{{ $fundingRequest->user->email }}</div>
                  </div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Usaha</div>
                  <div class="datagrid-content">
                    <div>
                      <a href="{{ route('businesses.show', $fundingRequest->business) }}" class="text-decoration-none">
                        <strong>{{ $fundingRequest->business->name }}</strong>
                      </a>
                      <div class="text-muted small">{{ $fundingRequest->business->businessType->name }}</div>
                    </div>
                  </div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Jumlah Pendanaan</div>
                  <div class="datagrid-content">
                    <span class="fs-2 fw-bold text-primary">Rp
                      {{ number_format($fundingRequest->amount, 0, ',', '.') }}</span>
                  </div>
                </div>

                @if ($fundingRequest->interest_rate > 0)
                  <div class="datagrid-item">
                    <div class="datagrid-title">Bunga</div>
                    <div class="datagrid-content">{{ $fundingRequest->interest_rate }}% per tahun</div>
                  </div>
                @endif

                @if ($fundingRequest->repayment_duration_months)
                  <div class="datagrid-item">
                    <div class="datagrid-title">Durasi Pembayaran</div>
                    <div class="datagrid-content">{{ $fundingRequest->repayment_duration_months }} bulan</div>
                  </div>
                @endif

                <div class="datagrid-item">
                  <div class="datagrid-title">Tanggal Pengajuan</div>
                  <div class="datagrid-content">
                    {{ $fundingRequest->created_at->locale('id')->translatedFormat('d M Y H:i') }}
                    <span class="text-muted small">({{ $fundingRequest->created_at->diffForHumans() }})</span>
                  </div>
                </div>

                @if ($fundingRequest->approved_at)
                  <div class="datagrid-item">
                    <div class="datagrid-title">Disetujui Oleh</div>
                    <div class="datagrid-content">
                      {{ $fundingRequest->approvedBy->name }}
                      <span
                        class="text-muted small">({{ $fundingRequest->approved_at->locale('id')->translatedFormat('d M Y H:i') }})</span>
                    </div>
                  </div>
                @endif

                @if ($fundingRequest->rejected_at)
                  <div class="datagrid-item">
                    <div class="datagrid-title">Ditolak Oleh</div>
                    <div class="datagrid-content">
                      {{ $fundingRequest->rejectedBy->name }}
                      <span class="text-muted small">({{ $fundingRequest->rejected_at->format('d M Y H:i') }})</span>
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>

          <div class="card mb-3">
            <div class="card-header">
              <h3 class="card-title">Tujuan Pendanaan</h3>
            </div>
            <div class="card-body">
              <p class="mb-0" style="white-space: pre-line;">{{ $fundingRequest->purpose }}</p>
            </div>
          </div>

          @if ($fundingRequest->rejection_reason)
            <div class="card mb-3 card-danger">
              <div class="card-header">
                <h3 class="card-title">Alasan Penolakan</h3>
              </div>
              <div class="card-body">
                <p class="mb-0" style="white-space: pre-line;">{{ $fundingRequest->rejection_reason }}</p>
              </div>
            </div>
          @endif

          {{-- Documents --}}
          @if ($fundingRequest->mou_document || $fundingRequest->signature_document)
            <div class="card mb-3">
              <div class="card-header">
                <h3 class="card-title">Dokumen</h3>
              </div>
              <div class="card-body">
                <div class="row g-2">
                  @if ($fundingRequest->mou_document)
                    <div class="col-6">
                      <div class="card card-sm">
                        <div class="card-body">
                          <div class="d-flex align-items-center">
                            <span class="bg-primary text-white avatar me-3">
                              <i class="ti ti-file-text"></i>
                            </span>
                            <div class="flex-fill">
                              <div class="font-weight-medium">MOU Pendanaan</div>
                              <div class="text-muted small">
                                Diunggah {{ $fundingRequest->mou_uploaded_at?->diffForHumans() }}
                              </div>
                            </div>
                            <a href="{{ Storage::url($fundingRequest->mou_document) }}" target="_blank"
                              class="btn btn-ghost-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="Unduh">
                              <i class="ti ti-download"></i>
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endif

                  @if ($fundingRequest->signature_document)
                    <div class="col-6">
                      <div class="card card-sm">
                        <div class="card-body">
                          <div class="d-flex align-items-center">
                            <span class="bg-success text-white avatar me-3">
                              <i class="ti ti-file-check"></i>
                            </span>
                            <div class="flex-fill">
                              <div class="font-weight-medium">MOU Bertanda Tangan</div>
                              <div class="text-muted small">
                                Ditandatangani {{ $fundingRequest->mou_signed_at?->diffForHumans() }}
                              </div>
                            </div>
                            <a href="{{ Storage::url($fundingRequest->signature_document) }}" target="_blank"
                              class="btn btn-ghost-success btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="Unduh">
                              <i class="ti ti-download"></i>
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          @endif

          {{-- Repayment History --}}
          @if (in_array($fundingRequest->status->value, ['disbursed', 'repaying', 'completed']))
            <x-modules.funding-request.repayment-history :fundingRequest="$fundingRequest" />
          @endif

          {{-- Timeline --}}
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Riwayat Aktivitas</h3>
            </div>
            <div class="card-body">
              @forelse($fundingRequest->timelines as $timeline)
                <x-modules.funding-request.timeline-item :timeline="$timeline" />
              @empty
                <div class="empty">
                  <p class="empty-title">Belum ada aktivitas</p>
                </div>
              @endforelse
            </div>
          </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
          @if (in_array($fundingRequest->status->value, ['disbursed', 'repaying', 'completed']))
            <div class="card mb-3">
              <div class="card-header">
                <h3 class="card-title">Ringkasan Pembayaran</h3>
              </div>
              <div class="card-body">
                <div class="row g-2">
                  <div class="col-12">
                    <div class="card card-sm card-primary">
                      <div class="card-body">
                        <div class="text-muted small mb-1">Dana Dicairkan</div>
                        <div class="h2 mb-0">Rp {{ number_format($fundingRequest->disbursed_amount, 0, ',', '.') }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="card card-sm card-success">
                      <div class="card-body">
                        <div class="text-muted small mb-1">Terbayar</div>
                        <div class="h3 mb-0">Rp
                          {{ number_format($fundingRequest->getTotalRepaidAttribute(), 0, ',', '.') }}</div>
                      </div>
                    </div>
                  </div>
                  <div class="col-6">
                    <div
                      class="card card-sm {{ $fundingRequest->getRemainingAmountAttribute() > 0 ? 'card-danger' : 'card-success' }}">
                      <div class="card-body">
                        <div class="text-muted small mb-1">Sisa</div>
                        <div class="h3 mb-0">Rp
                          {{ number_format($fundingRequest->getRemainingAmountAttribute(), 0, ',', '.') }}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endif

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Status Proses</h3>
            </div>
            <div class="card-body">
              @php
                function formatDate($carbon)
                {
                    return $carbon->copy()->locale('id')->translatedFormat('d M Y');
                }
              @endphp
              <div class="steps steps-vertical" style="margin: 0; padding: 0; border: none;">
                <div
                  class="step-item {{ $fundingRequest->status === FundingRequestStatus::SUBMITTED ? 'active' : '' }}">
                  <div class="h4 m-0">Pengajuan Dibuat</div>
                  <div class="text-muted">{{ formatDate($fundingRequest->created_at) }}</div>
                </div>

                <div class="step-item {{ $fundingRequest->status === FundingRequestStatus::APPROVED ? 'active' : '' }}">
                  <div class="h4 m-0">Disetujui Admin</div>
                  @if ($fundingRequest->approved_at)
                    <div class="text-muted">{{ formatDate($fundingRequest->approved_at) }}</div>
                  @endif
                </div>

                <div
                  class="step-item {{ $fundingRequest->status === FundingRequestStatus::MOU_SIGNED || $fundingRequest->status === FundingRequestStatus::READY_TO_DISBURSE ? 'active' : '' }}">
                  <div class="h4 m-0">MOU Ditandatangani</div>
                  @if ($fundingRequest->mou_signed_at)
                    <div class="text-muted">{{ formatDate($fundingRequest->mou_signed_at) }}</div>
                  @endif
                </div>

                <div
                  class="step-item {{ $fundingRequest->status === FundingRequestStatus::DISBURSED || $fundingRequest->status === FundingRequestStatus::REPAYING ? 'active' : '' }}">
                  <div class="h4 m-0">Dana Dicairkan</div>
                  @if ($fundingRequest->disbursement_date)
                    <div class="text-muted">{{ formatDate($fundingRequest->disbursement_date) }}</div>
                  @endif
                </div>

                <div
                  class="step-item {{ $fundingRequest->status === FundingRequestStatus::COMPLETED ? 'active' : '' }}">
                  <div class="h4 m-0">Selesai / Lunas</div>
                  @php
                    $completeTimeline = $fundingRequest
                        ->timelines()
                        ->where('action', FundingRequestStatus::COMPLETED)
                        ->first();
                  @endphp
                  @if ($completeTimeline)
                    <div class="text-muted">
                      {{ formatDate($completeTimeline->created_at) }}
                    </div>
                  @endif
                </div>
              </div>

              @if ($fundingRequest->status === FundingRequestStatus::REJECTED)
                <div class="alert alert-danger mt-3 mb-0">
                  <div class="d-flex">
                    <div><i class="ti ti-x icon alert-icon"></i></div>
                    <div>
                      <h4 class="alert-title">Pengajuan Ditolak</h4>
                      <div class="text-muted">{{ formatDate($fundingRequest->rejected_at) }}</div>
                    </div>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Include Modals --}}
  @include('funding-requests._approve_modal')
  @include('funding-requests._reject_modal')
  @include('funding-requests._upload_mou_modal')
  @include('funding-requests._sign_mou_modal')
  @include('funding-requests._disburse_modal')
  @include('funding-requests._repayment_modal')
@endsection
