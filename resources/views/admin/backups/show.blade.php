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
            Detail Backup #{{ $backup->id }}
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <a href="{{ route('admin.backups.index') }}" class="btn btn-outline-secondary">
              <i class="icon ti ti-arrow-left"></i>
              Kembali
            </a>

            @if ($backup->status->value === 'completed' && $backup->fileExists())
              <a href="{{ route('admin.backups.download', $backup) }}" class="btn btn-success">
                <i class="icon ti ti-download"></i>
                Download
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

              <button type="button" class="btn btn-warning" onclick="confirmRestore({{ $param }})">
                <i class="icon ti ti-restore"></i>
                Restore
              </button>
            @endif

            <button type="button" class="btn btn-danger"
              onclick="deleteConfirm('{{ route('admin.backups.destroy', $backup) }}', 'DELETE', {'message': 'Apakah Anda yakin ingin menghapus backup ini?'})">
              <i class="icon ti ti-trash"></i>
              Hapus
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <div class="row">
        <!-- Backup Details -->
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Informasi Backup</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">ID</div>
                  <div class="datagrid-content">{{ $backup->id }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Nama</div>
                  <div class="datagrid-content">{{ $backup->name }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Tipe</div>
                  <div class="datagrid-content">
                    <x-common.badge :color="$backup->type->color()" light>
                      <i class="icon ti ti-sm ti-{{ $backup->type->icon() }}"></i>
                      {{ $backup->type->label() }}
                    </x-common.badge>
                  </div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Status</div>
                  <div class="datagrid-content">
                    <x-common.badge :color="$backup->status->color()" :label="$backup->status->label()" />
                  </div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Cara Backup</div>
                  <div class="datagrid-content text-muted">
                    {{ $backup->is_manual ? 'Manual' : 'Terjadwal' }}
                  </div>
                </div>
                @if ($backup->file_path)
                  <div class="datagrid-item">
                    <div class="datagrid-title">Nama File</div>
                    <div class="datagrid-content">
                      <code>{{ $backup->file_name }}</code>
                      @if (!$backup->fileExists())
                        <span class="badge bg-danger ms-2">File tidak ditemukan</span>
                      @endif
                    </div>
                  </div>
                  <div class="datagrid-item">
                    <div class="datagrid-title">Ukuran File</div>
                    <div class="datagrid-content">{{ $backup->file_size_formatted }}</div>
                  </div>
                  <div class="datagrid-item">
                    <div class="datagrid-title">Lokasi</div>
                    <div class="datagrid-content">
                      <small class="text-muted">{{ realpath($backup->file_path) }}</small>
                    </div>
                  </div>
                @endif
                <div class="datagrid-item">
                  <div class="datagrid-title">Dibuat Oleh</div>
                  <div class="datagrid-content">
                    @if ($backup->createdBy)
                      {{ $backup->createdBy->name }}
                      <small class="text-muted">({{ $backup->createdBy->email }})</small>
                    @else
                      <span class="text-muted">System</span>
                    @endif
                  </div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Tanggal Dibuat</div>
                  <div class="datagrid-content">{{ $backup->created_at }}</div>
                </div>
                @if ($backup->started_at)
                  <div class="datagrid-item">
                    <div class="datagrid-title">Mulai</div>
                    <div class="datagrid-content">{{ $backup->started_at }}</div>
                  </div>
                @endif
                @if ($backup->completed_at)
                  <div class="datagrid-item">
                    <div class="datagrid-title">Selesai</div>
                    <div class="datagrid-content">{{ $backup->completed_at }}</div>
                  </div>
                @endif
                @if ($backup->duration)
                  <div class="datagrid-item">
                    <div class="datagrid-title">Durasi</div>
                    <div class="datagrid-content">
                      <x-common.badge :color="'info'" :label="$backup->duration" light />
                    </div>
                  </div>
                @endif
                @if ($backup->error_message)
                  <div class="datagrid-item">
                    <div class="datagrid-title">Pesan Error</div>
                    <div class="datagrid-content">
                      <div class="alert alert-danger mb-0">
                        {{ $backup->error_message }}
                      </div>
                    </div>
                  </div>
                @endif
                @if ($backup->metadata)
                  <div class="datagrid-item">
                    <div class="datagrid-title">Metadata</div>
                    <div class="datagrid-content">
                      <pre class="mb-0"><code>{{ json_encode($backup->metadata, JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@include('admin.backups._restore_scripts')
