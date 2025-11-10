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
            Backup & Restore
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <a href="{{ route('admin.backups.create') }}" class="btn btn-primary d-none d-sm-inline-block">
              <i class="icon ti ti-plus"></i>
              Buat Backup Baru
            </a>
            <div class="dropdown">
              <button type="button" class="btn btn-icon" data-bs-toggle="dropdown">
                <i class="icon ti ti-dots"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <a class="dropdown-item" href="#" onclick="cleanOldBackups()">
                  <i class="dropdown-item-icon icon ti ti-trash"></i>
                  Bersihkan
                </a>
                <a class="dropdown-item text-danger" href="#" onclick="cleanAllBackups()">
                  <i class="dropdown-item-icon text-danger icon ti ti-trash-off"></i>
                  Hapus Semua
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <!-- Statistics Cards -->
      <div class="row row-cards mb-3">
        <div class="col-sm-6 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="subheader">Total Backup</div>
              </div>
              <div class="h1 my-2">{{ $statistics['total'] }}</div>
              <div class="d-flex">
                <div class="text-muted">
                  Semua backup yang dibuat
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="subheader">Backup Berhasil</div>
              </div>
              <div class="h1 my-2 text-success">{{ $statistics['completed'] }}</div>
              <div class="d-flex">
                <div class="text-muted">
                  Backup yang selesai
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="subheader">Backup Gagal</div>
              </div>
              <div class="h1 my-2 text-danger">{{ $statistics['failed'] }}</div>
              <div class="d-flex">
                <div class="text-muted">
                  Backup yang gagal
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="subheader">Total Ukuran</div>
              </div>
              <div class="h1 my-2">
                @php
                  $size = $statistics['total_size'];
                  $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                  $power = $size > 0 ? floor(log($size, 1024)) : 0;
                  $formattedSize = round($size / pow(1024, $power), 2) . ' ' . $units[$power];
                @endphp
                {{ $formattedSize }}
              </div>
              <div class="d-flex">
                <div class="text-muted">
                  Total ukuran backup
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Last Backup Info -->
      @if ($statistics['last_backup'])
        <div class="row mb-3">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <i class="ti ti-sm ti-clock text-muted me-2"></i>
                  <div>
                    <strong>Backup Terakhir:</strong>
                    <a href="{{ route('admin.backups.show', ['backup' => $statistics['last_backup']]) }}"
                      class="text-reset">{{ $statistics['last_backup']->name }}</a>
                    -
                    <span class="text-muted">{{ $statistics['last_backup']->created_at->diffForHumans() }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endif

      <!-- Backups List -->
      <div class="row row-deck row-cards">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Daftar Backup</h3>
              <div class="card-actions">
                <form action="{{ route('admin.backups.index') }}" method="GET" class="d-flex gap-2">
                  <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Semua Tipe</option>
                    <option value="database" {{ request('type') == 'database' ? 'selected' : '' }}>Database</option>
                    <option value="files" {{ request('type') == 'files' ? 'selected' : '' }}>Files & Media</option>
                    <option value="full" {{ request('type') == 'full' ? 'selected' : '' }}>Full Backup</option>
                  </select>
                  <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Dalam Proses
                    </option>
                  </select>
                  <div class="input-group" style="min-width: 160px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari backup..."
                      value="{{ request('search') }}">
                    <button type="submit" class="btn btn-icon btn-primary">
                      <i class="icon ti ti-search"></i>
                    </button>
                  </div>
                </form>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table card-table table-vcenter text-nowrap datatable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th>Ukuran</th>
                    <th>Dibuat Oleh</th>
                    <th>Tanggal</th>
                    <th>Durasi</th>
                    <th class="text-end">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($backups as $backup)
                    <tr>
                      <td>{{ $loop->iteration + $backups->firstItem() - 1 }}</td>
                      <td>
                        <div>{{ $backup->name }}</div>
                        <small class="text-muted">
                          @if ($backup->is_manual)
                            Manual
                          @else
                            Terjadwal
                          @endif
                        </small>
                      </td>
                      <td>
                        <x-common.badge :color="$backup->type->color()" light>
                          <i class="icon ti ti-sm ti-{{ $backup->type->icon() }}"></i>
                          {{ $backup->type->label() }}
                        </x-common.badge>
                      </td>
                      <td>
                        <x-common.badge :color="$backup->status->color()" :label="$backup->status->label()" />
                      </td>
                      <td>{{ $backup->file_size_formatted }}</td>
                      <td>
                        @if ($backup->createdBy)
                          {{ $backup->createdBy->name }}
                        @else
                          <span class="text-muted">System</span>
                        @endif
                      </td>
                      <td>
                        {{ $backup->created_at }}
                      </td>
                      <td>{{ $backup->duration ?? '-' }}</td>
                      <td class="text-end">
                        <div class="btn-list flex-nowrap justify-content-end">
                          <a href="{{ route('admin.backups.show', $backup) }}" class="btn btn-icon btn-primary"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Detail Backup">
                            <i class="icon ti ti-eye"></i>
                          </a>

                          @if ($backup->status->value === 'completed' && $backup->fileExists())
                            <a href="{{ route('admin.backups.download', $backup) }}" class="btn btn-icon btn-success"
                              data-bs-toggle="tooltip" data-bs-placement="top" title="Unduh Backup">
                              <i class="icon ti ti-download"></i>
                            </a>

                            @php
                              $param = \Illuminate\Support\Js::from([
                                  'id' => $backup->id,
                                  'name' => $backup->name,
                                  'type' => [
                                      'label' => $backup->type->label(),
                                      'icon' => $backup->type->icon(),
                                      'color' => $backup->type->color(),
                                  ],
                                  'file_size_formatted' => $backup->file_size_formatted,
                                  'completed_at' => $backup->completed_at->toDateTimeString(),
                              ])->toHtml();
                            @endphp

                            <button type="button" class="btn btn-icon btn-warning"
                              onclick="confirmRestore({{ $param }})" data-bs-toggle="tooltip"
                              data-bs-placement="top" title="Restore Backup">
                              <i class="icon ti ti-restore"></i>
                            </button>
                          @endif


                          <button class="btn btn-icon btn-danger"
                            onclick="deleteConfirm('{{ route('admin.backups.destroy', $backup) }}', 'DELETE', {'message': 'Apakah Anda yakin ingin menghapus backup ini?'})"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Backup">
                            <i class="ti ti-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="9" class="text-center text-muted">Tidak ada data backup</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            @if ($backups->hasPages())
              <div class="card-footer d-flex align-items-center">
                {!! $backups->links('tablar::pagination') !!}
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@include('admin.backups._restore_scripts')

@push('js')
  <script>
    function cleanOldBackups() {
      deleteConfirm("{{ route('admin.backups.clean-old') }}", "POST", {
        title: 'Bersihkan Backup Lama?',
        message: 'Apakah Anda yakin ingin membersihkan backup lama sesuai dengan kebijakan retensi yang telah ditetapkan?',
        buttons: {
          cancel: {
            label: "Batal"
          },
          confirm: {
            label: "Ya",
            className: "btn-warning"
          },
        },
      });
    }

    function cleanAllBackups() {
      deleteConfirm("{{ route('admin.backups.clean') }}", "DELETE", {
        title: 'Hapus Semua Backup?',
        message: 'Apakah Anda yakin ingin menghapus semua backup? Tindakan ini tidak dapat dibatalkan.',
        buttons: {
          cancel: {
            label: "Batal"
          },
          confirm: {
            label: "Ya, Hapus Semua",
            className: "btn-danger"
          },
        },
      });
    }
  </script>
@endpush
