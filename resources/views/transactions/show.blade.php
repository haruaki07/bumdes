@extends('tablar::page')

@section('content')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Manajemen Keuangan
          </div>
          <h2 class="page-title">
            Detail Transaksi
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            @if (!$transaction->isVerified())
              <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-warning">
                <i class="icon ti ti-edit"></i>
                Edit
              </a>

              <button class="btn btn-success" onclick="verifyTransaction()">
                <i class="icon ti ti-check"></i>
                Verifikasi
              </button>

              <button class="btn btn-danger" onclick="deleteTransaction()">
                <i class="icon ti ti-trash"></i>
                Hapus
              </button>
            @else
              <button class="btn btn-warning" onclick="unverifyTransaction()">
                <i class="icon ti ti-x"></i>
                Batalkan Verifikasi
              </button>
            @endif

            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
              Kembali
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <div class="row">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Informasi Transaksi</h3>
              <div class="card-actions">
                @if ($transaction->isVerified())
                  <x-common.badge color="success" label="Terverifikasi" light />
                @else
                  <x-common.badge color="warning" label="Belum Verifikasi" light />
                @endif
              </div>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">No. Referensi</div>
                  <div class="datagrid-content">
                    <x-common.badge color="primary" label="{{ $transaction->reference_number }}" light />
                  </div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Tanggal Transaksi</div>
                  <div class="datagrid-content">
                    {{ $transaction->transaction_date->locale('id')->translatedFormat('d F Y') }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Tipe</div>
                  <div class="datagrid-content">
                    @if ($transaction->type === 'income')
                      <x-common.badge color="success" label="Pendapatan" light />
                    @else
                      <x-common.badge color="danger" label="Beban" light />
                    @endif
                  </div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Jumlah</div>
                  <div class="datagrid-content">
                    <span class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }} h4 fw-bold">
                      Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                    </span>
                  </div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Kategori</div>
                  <div class="datagrid-content">
                    {{ $transaction->category->name }}
                    <small class="text-muted">({{ $transaction->category->code }})</small>
                    @if ($transaction->category->parent)
                      <div class="text-muted">{{ $transaction->category->parent->name }}</div>
                    @endif
                  </div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Deskripsi</div>
                  <div class="datagrid-content">
                    {{ $transaction->description }}
                  </div>
                </div>

                @if ($transaction->notes)
                  <div class="datagrid-item">
                    <div class="datagrid-title">Catatan</div>
                    <div class="datagrid-content">
                      {{ $transaction->notes }}
                    </div>
                  </div>
                @endif
              </div>

              @if ($transaction->fundingRequest)
                <div class="mt-3">
                  <label class="form-label text-muted">Terkait Permintaan Pendanaan</label>
                  <div class="card mb-0">
                    <div class="card-body">
                      <div class="row align-items-start align-items-sm-center flex-column flex-sm-row g-2">
                        <div class="col">
                          <h4 class="mb-1">{{ $transaction->fundingRequest->business->name }}</h4>
                          <div class="text-muted d-flex flex-wrap align-items-center gap-2">
                            Jumlah Disetujui: Rp
                            {{ number_format($transaction->fundingRequest->amount_approved, 0, ',', '.') }}

                            <x-common.badge :color="$transaction->fundingRequest->status->color()" light>
                              {{ $transaction->fundingRequest->status->label() }}
                            </x-common.badge>
                          </div>
                        </div>
                        <div class="col-auto">
                          <a href="{{ route('funding-requests.show', $transaction->fundingRequest) }}"
                            class="btn btn-primary">
                            <i class="icon ti ti-eye"></i>
                            Lihat Detail
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @endif

              @if ($transaction->document_path)
                <div class="mt-3">
                  <label class="form-label text-muted">Dokumen Pendukung</label>
                  <div class="card mb-0">
                    <div class="card-body">
                      <div class="row align-items-start align-items-sm-center flex-column flex-sm-row g-2">
                        <div class="col-auto">
                          @php
                            $extension = pathinfo($transaction->document_path, PATHINFO_EXTENSION);
                          @endphp
                          @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                            <i class="ti ti-photo"></i>
                          @else
                            <i class="ti ti-file-text"></i>
                          @endif
                        </div>
                        <div class="col">
                          <strong>{{ basename($transaction->document_path) }}</strong>
                          <div class="text-muted small">{{ strtoupper($extension) }} File</div>
                        </div>
                        <div class="col-auto btn-list">
                          <a href="{{ Storage::url($transaction->document_path) }}" class="btn btn-primary"
                            target="_blank">
                            <i class="icon ti ti-download"></i>
                            Download
                          </a>
                          @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                            <button type="button" class="btn btn-info" data-bs-toggle="modal"
                              data-bs-target="#imageModal">
                              <i class="icon ti ti-eye"></i>
                              Lihat
                            </button>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Informasi Sistem</h3>
            </div>
            <div class="card-body">
              <dl class="row">
                <dt class="col-5">Dibuat Oleh:</dt>
                <dd class="col-7">{{ $transaction->creator->name }}</dd>

                <dt class="col-5">Tanggal Dibuat:</dt>
                <dd class="col-7">{{ $transaction->created_at->format('d/m/Y H:i') }}</dd>

                <dt class="col-5">Terakhir Diubah:</dt>
                <dd class="col-7">{{ $transaction->updated_at->format('d/m/Y H:i') }}</dd>

                @if ($transaction->isVerified())
                  <dt class="col-5">Diverifikasi Oleh:</dt>
                  <dd class="col-7">{{ $transaction->verifier->name }}</dd>

                  <dt class="col-5">Tanggal Verifikasi:</dt>
                  <dd class="col-7">{{ $transaction->verified_at->format('d/m/Y H:i') }}</dd>
                @endif
              </dl>

              @if (!$transaction->isVerified())
                <div class="alert alert-warning">
                  <div class="d-flex gap-2">
                    <i class="icon ti ti-alert-circle alert-icon"></i>
                    <div>
                      <h4 class="alert-title">Peringatan</h4>
                      <div>
                        Transaksi ini belum diverifikasi. Klik tombol <strong>Verifikasi</strong> untuk memverifikasi.
                      </div>
                    </div>
                  </div>
                </div>
              @else
                <div class="alert alert-success mb-0">
                  <div class="d-flex gap-2">
                    <i class="icon ti ti-check alert-icon"></i>
                    <div>
                      <h4 class="alert-title">Informasi</h4>
                      <div>
                        Transaksi ini sudah diverifikasi dan tidak dapat diedit atau dihapus.
                      </div>
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

  {{-- Image Preview Modal --}}
  @if ($transaction->document_path)
    @php
      $extension = pathinfo($transaction->document_path, PATHINFO_EXTENSION);
    @endphp
    @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
      <div class="modal modal-blur fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Dokumen Pendukung</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
              <img src="{{ Storage::url($transaction->document_path) }}" alt="Document" class="img-fluid">
            </div>
            <div class="modal-footer">
              <a href="{{ Storage::url($transaction->document_path) }}" class="btn btn-primary" target="_blank">
                <i class="ti ti-download"></i>
                Download
              </a>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      </div>
    @endif
  @endif
@endsection

@push('js')
  <script>
    function verifyTransaction() {
      deleteConfirm("{{ route('transactions.verify', $transaction->id) }}", "PATCH", {
        title: 'Verifikasi Transaksi?',
        message: 'Apakah Anda yakin ingin memverifikasi transaksi ini?',
        buttons: {
          cancel: {
            label: "Batal"
          },
          confirm: {
            label: "Ya",
            className: "btn-primary"
          },
        },
      });
    }

    function unverifyTransaction() {
      deleteConfirm("{{ route('transactions.unverify', $transaction->id) }}",
        "PATCH", {
          title: 'Batalkan Verifikasi Transaksi?',
          message: 'Apakah Anda yakin ingin membatalkan verifikasi transaksi ini?',
          buttons: {
            cancel: {
              label: "Batal"
            },
            confirm: {
              label: "Ya, Batalkan",
              className: "btn-warning"
            },
          },
        });
    }

    function deleteTransaction() {
      deleteConfirm("{{ route('transactions.destroy', $transaction->id) }}",
        "DELETE", {
          title: 'Hapus Transaksi?',
          message: 'Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.',
          buttons: {
            cancel: {
              label: "Batal"
            },
            confirm: {
              label: "Ya, Hapus",
              className: "btn-danger"
            },
          },
        });
    }
  </script>
@endpush
