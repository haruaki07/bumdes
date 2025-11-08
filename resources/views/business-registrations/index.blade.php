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
            Pengajuan Usaha
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            @can('create', \App\Models\BusinessRegistration::class)
              <a href="{{ route('business-registrations.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                <i class="icon ti ti-plus"></i>
                Tambah Pengajuan Usaha
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
          <x-datatable tableId="businessRegistrationsTable" title="Daftar Pengajuan Usaha" :data="$registrations">
            <x-slot:thead>
              <tr>
                <x-sortable-header field="name" label="Nama Usaha" />
                <x-sortable-header field="businessType.name" label="Jenis" />
                <x-sortable-header field="applicant.name" label="Pemohon" />
                <x-sortable-header field="location" label="Lokasi" />
                <x-sortable-header field="status" label="Status" />
                <th>Dokumen</th>
                <x-sortable-header field="created_at" label="Tanggal Pengajuan" />
                <th>Aksi</th>
              </tr>
            </x-slot>

            <x-slot:tbody>
              @forelse ($registrations as $registration)
                <tr>
                  <td class="fw-medium">{{ $registration->name }}</td>
                  <td>{{ $registration->businessType->name }}</td>
                  <td>{{ $registration->applicant->name }}</td>
                  <td>{{ $registration->location }}</td>
                  <td>
                    <x-modules.business-registration.status-badge :status="$registration->status" />
                  </td>
                  <td class="text-center">
                    @if ($registration->document_url)
                      <i class="icon ti ti-file-check text-success" title="Dokumen tersedia"></i>
                    @else
                      <i class="icon ti ti-file-off text-muted" title="Tidak ada dokumen"></i>
                    @endif
                  </td>
                  <td>{{ $registration->created_at->format('d M Y H:i') }}</td>
                  <td>
                    <div class="btn-list flex-nowrap">
                      @can('view', $registration)
                        <a href="{{ route('business-registrations.show', $registration) }}" class="btn"
                          title="Lihat Detail">
                          Detail
                        </a>
                      @endcan
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center">Tidak ada data pengajuan usaha.</td>
                </tr>
              @endforelse
            </x-slot>
          </x-datatable>
        </div>
      </div>
    </div>
  </div>
@endsection
