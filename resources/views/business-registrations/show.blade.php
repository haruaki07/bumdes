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
            Detail Pengajuan Usaha
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            @if ($businessRegistration->status === 'pending' && $businessRegistration->canBeRevised())
              @can('approve', $businessRegistration)
                <form action="{{ route('business-registrations.approve', $businessRegistration) }}" method="POST"
                  class="d-inline m-0">
                  @csrf
                  <button type="submit" class="btn btn-success"
                    onclick="return confirm('Apakah Anda yakin ingin menyetujui pengajuan usaha ini?')">
                    <i class="icon ti ti-check"></i>
                    Setujui Pengajuan
                  </button>
                </form>
              @endcan
              @can('reject', $businessRegistration)
                <button type="button" class="btn btn-danger d-inline-block" data-bs-toggle="modal"
                  data-bs-target="#rejectModal">
                  <i class="icon ti ti-x"></i>
                  Tolak Pengajuan
                </button>
              @endcan
            @endif
            @if ($businessRegistration->canBeRevised())
              @can('revise', $businessRegistration)
                <a href="{{ route('business-registrations.revise', $businessRegistration) }}" class="btn btn-info">
                  <i class="icon ti ti-refresh"></i>
                  Revisi Pengajuan
                </a>
              @endcan
            @endif
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
              <h3 class="card-title">Informasi Pengajuan Usaha</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">Nama Usaha</div>
                  <div class="datagrid-content">{{ $businessRegistration->name }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Jenis Usaha</div>
                  <div class="datagrid-content">{{ $businessRegistration->businessType->name }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Pemohon</div>
                  <div class="datagrid-content">{{ $businessRegistration->applicant->name }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Status</div>
                  <div class="datagrid-content">
                    <x-modules.business-registration.status-badge :status="$businessRegistration->status" />
                  </div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Lokasi</div>
                  <div class="datagrid-content">{{ $businessRegistration->location }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Nomor Telepon</div>
                  <div class="datagrid-content">{{ $businessRegistration->contact_phone }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Email</div>
                  <div class="datagrid-content">{{ $businessRegistration->contact_email ?: '-' }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Tanggal Pengajuan</div>
                  <div class="datagrid-content">
                    {{ $businessRegistration->created_at->format('d/m/Y H:i') }}</div>
                </div>

                @if ($businessRegistration->status === 'approved')
                  <div class="datagrid-item">
                    <div class="datagrid-title">Disetujui Oleh</div>
                    <div class="datagrid-content">{{ $businessRegistration->approver->name }}</div>
                  </div>

                  <div class="datagrid-item">
                    <div class="datagrid-title">Tanggal Persetujuan</div>
                    <div class="datagrid-content">
                      {{ $businessRegistration->approved_at->format('d/m/Y H:i') }}</div>
                  </div>

                  @if ($businessRegistration->business)
                    <div class="datagrid-item">
                      <div class="datagrid-title">Usaha Terdaftar</div>
                      <div class="datagrid-content">
                        <a href="{{ route('businesses.show', $businessRegistration->business) }}"
                          class="btn btn-sm btn-primary">
                          Lihat Usaha
                        </a>
                      </div>
                    </div>
                  @endif
                @endif

                @if ($businessRegistration->isRevision())
                  <div class="datagrid-item">
                    <div class="datagrid-title">Revisi Dari</div>
                    <div class="datagrid-content">
                      <a href="{{ route('business-registrations.show', $businessRegistration->parent) }}"
                        class="btn btn-sm btn-info">
                        Lihat Pengajuan Asli
                      </a>
                    </div>
                  </div>
                @endif
              </div>

              <div class="mt-4">
                <div class="datagrid-title">Deskripsi</div>
                <div class="datagrid-content">{{ $businessRegistration->description }}</div>
              </div>

              @if ($businessRegistration->rejection_reason)
                <div class="mt-4">
                  <div class="datagrid-title">Alasan Penolakan</div>
                  <div class="datagrid-content text-danger">{{ $businessRegistration->rejection_reason }}
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>

        <!-- Timeline Section -->
        <div class="col-12 mt-3">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Timeline Pengajuan</h3>
            </div>
            <div class="card-body">
              <div class="timeline">
                @foreach ($timeline as $item)
                  <div class="timeline-event">
                    <div class="timeline-event-icon border">
                      @if ($item->action === 'submitted')
                        <i class="icon ti ti-plus text-blue"></i>
                      @elseif ($item->action === 'approved')
                        <i class="icon ti ti-check text-green"></i>
                      @elseif ($item->action === 'rejected')
                        <i class="icon ti ti-x text-red"></i>
                      @elseif ($item->action === 'revised')
                        <i class="icon ti ti-refresh text-cyan"></i>
                      @elseif ($item->action === 'updated')
                        <i class="icon ti ti-edit text-yellow"></i>
                      @else
                        <i class="icon ti ti-circle text-muted"></i>
                      @endif
                    </div>
                    <div class="card timeline-event-card">
                      <div class="card-body">
                        <div class="text-secondary float-end">
                          {{ $item->performer->name }} —
                          {{ $item->created_at->format('d/m/Y H:i') }}
                        </div>
                        <h4>
                          {{ ucfirst($item->action) }}
                          @if ($item->action === 'revised')
                            <span class="badge bg-info text-info-fg">Revisi
                              #{{ $item->metadata['revision_number'] }}</span>
                          @endif
                        </h4>
                        <p class="text-secondary">
                          {{ $item->description }}
                        </p>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if ($businessRegistration->status === 'pending')
    @can('reject', $businessRegistration)
      <!-- Reject Modal -->
      <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form action="{{ route('business-registrations.reject', $businessRegistration) }}" method="POST">
              @csrf
              <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Tolak Pengajuan Usaha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p>Apakah Anda yakin ingin menolak pengajuan usaha
                  <strong>{{ $businessRegistration->name }}</strong>?
                </p>
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
                <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
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
