@extends('tablar::page')

@section('content')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Administrasi
          </div>
          <h2 class="page-title">
            Buat Backup Baru
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <a href="{{ route('admin.backups.index') }}" class="btn btn-outline-secondary d-none d-sm-inline-block">
              <i class="icon ti ti-arrow-left"></i>
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

      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Pilih Tipe Backup</h3>
            </div>
            <div class="card-body">
              <p class="text-muted mb-4">
                Pilih tipe backup yang ingin Anda buat. Proses backup akan berjalan di background dan Anda akan menerima
                notifikasi ketika selesai.
              </p>

              <form action="{{ route('admin.backups.store') }}" method="POST" id="create-backup-form">
                @csrf

                <div class="row g-3">
                  <!-- Database Backup -->
                  <div class="col-md-4">
                    <label class="form-check form-check-single-choice">
                      <input class="form-check-input" type="radio" name="type" value="database" required checked>
                      <span class="form-check-label">
                        <span class="form-check-icon">
                          <i class="icon ti ti-database text-primary"></i>
                        </span>
                        <span class="form-check-title">Database</span>
                        <span class="form-check-description">
                          Backup semua data database termasuk tabel dan struktur
                        </span>
                      </span>
                    </label>
                  </div>

                  <!-- Files Backup -->
                  <div class="col-md-4">
                    <label class="form-check form-check-single-choice">
                      <input class="form-check-input" type="radio" name="type" value="files" required>
                      <span class="form-check-label">
                        <span class="form-check-icon">
                          <i class="icon ti ti-folder text-info"></i>
                        </span>
                        <span class="form-check-title">Files & Media</span>
                        <span class="form-check-description">
                          Backup semua file yang diupload dan media
                        </span>
                      </span>
                    </label>
                  </div>

                  <!-- Full Backup -->
                  <div class="col-md-4">
                    <label class="form-check form-check-single-choice">
                      <input class="form-check-input" type="radio" name="type" value="full" required>
                      <span class="form-check-label">
                        <span class="form-check-icon">
                          <i class="icon ti ti-archive text-success"></i>
                        </span>
                        <span class="form-check-title">Full Backup</span>
                        <span class="form-check-description">
                          Backup lengkap database dan semua file
                        </span>
                      </span>
                    </label>
                  </div>
                </div>

                @error('type')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                <div class="mt-4">

                  <div class="alert alert-info">
                    <div class="d-flex gap-2">
                      <div class="alert-icon">
                        <i class="ti ti-info-circle"></i>
                      </div>
                      <div>
                        <h4 class="alert-title">Informasi Penting</h4>
                        <div class="alert-description">
                          <ul class="mb-0 mt-2">
                            <li>Backup akan berjalan di background</li>
                            <li>Anda akan menerima notifikasi email dan dashboard ketika selesai</li>
                            <li>Waktu backup tergantung ukuran data</li>
                            <li>Pastikan ada ruang penyimpanan yang cukup</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </form>
            </div>
            <div class="card-footer">
              <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.backups.index') }}" class="btn btn-ghost-secondary">Batal</a>
                <button class="btn btn-primary" data-bs-toggle="loading-button" data-bs-spinner-type="dots"
                  data-bs-disabled-on-loading="true" id="createBackupButton">
                  <i class="icon ti ti-device-floppy"></i>
                  Buat Backup
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <!-- Backup Configuration Info -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Konfigurasi Backup Otomatis</h3>
            </div>
            <div class="card-body">
              <dl class="row">
                <dt class="col-5">Backup Otomatis:</dt>
                <dd class="col-7">
                  <x-common.badge :color="config('backup.schedule.enabled') ? 'success' : 'secondary'" light>
                    {{ config('backup.schedule.enabled') ? 'Aktif' : 'Nonaktif' }}
                  </x-common.badge>
                </dd>

                @if (config('backup.schedule.enabled'))
                  <dt class="col-5">Frekuensi:</dt>
                  <dd class="col-7">{{ ucfirst(config('backup.schedule.frequency')) }}</dd>

                  <dt class="col-5">Waktu:</dt>
                  <dd class="col-7">{{ config('backup.schedule.time') }}</dd>
                @endif

                <dt class="col-5">Retensi:</dt>
                <dd class="col-7">
                  @if (config('backup.retention.enabled'))
                    Simpan {{ config('backup.retention.keep_last') }} backup terakhir atau
                    {{ config('backup.retention.keep_days') }} hari
                  @else
                    Tidak ada batas
                  @endif
                </dd>

                <dt class="col-5">Lokasi Penyimpanan:</dt>
                <dd class="col-7">
                  <code>{{ config('backup.paths.backup') }}</code>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script>
    document.getElementById('createBackupButton').addEventListener('click', function(event) {
      event.preventDefault();
      this._loadingButtonInstance.start();
      document.getElementById('create-backup-form').submit();
    });
  </script>
@endpush
