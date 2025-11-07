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
            Revisi Pengajuan Usaha
          </h2>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')
      <div class="row row-deck row-cards">
        <!-- Original Registration Info -->
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Informasi Pengajuan Asli</h3>
              <div class="card-actions">
                <a href="{{ route('business-registrations.show', $businessRegistration) }}" class="btn btn-secondary">
                  <i class="icon ti ti-arrow-left"></i>
                  Kembali ke Detail
                </a>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Nama Usaha (Asli)</label>
                    <input type="text" class="form-control" value="{{ $businessRegistration->name }}" readonly>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Jenis Usaha (Asli)</label>
                    <input type="text" class="form-control" value="{{ $businessRegistration->businessType->name }}"
                      readonly>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Deskripsi (Asli)</label>
                <textarea class="form-control" rows="3" readonly>{{ $businessRegistration->description }}</textarea>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="mb-3">
                    <label class="form-label">Lokasi (Asli)</label>
                    <input type="text" class="form-control" value="{{ $businessRegistration->location }}" readonly>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label class="form-label">Telepon (Asli)</label>
                    <input type="text" class="form-control" value="{{ $businessRegistration->contact_phone }}"
                      readonly>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label class="form-label">Email (Asli)</label>
                    <input type="text" class="form-control" value="{{ $businessRegistration->contact_email ?: '-' }}"
                      readonly>
                  </div>
                </div>
              </div>
              @if ($businessRegistration->rejection_reason)
                <div class="alert alert-danger">
                  <h4 class="alert-title">Alasan Penolakan:</h4>
                  <p class="mb-0">{{ $businessRegistration->rejection_reason }}</p>
                </div>
              @endif
            </div>
          </div>
        </div>

        <!-- Revision Form -->
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Form Revisi Pengajuan</h3>
              <div class="card-actions">
                <span class="badge bg-info text-info-fg">Revisi dari
                  #{{ $businessRegistration->getRootRegistration()->id }}</span>
              </div>
            </div>
            <div class="card-body">
              <form action="{{ route('business-registrations.store-revision', $businessRegistration) }}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Nama Usaha</label>
                      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $businessRegistration->name) }}" required
                        placeholder="Masukkan nama usaha">
                      @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Jenis Usaha</label>
                      <select name="business_type_id" class="form-select @error('business_type_id') is-invalid @enderror"
                        required>
                        <option value="">Pilih Jenis Usaha</option>
                        @foreach ($businessTypes as $type)
                          <option value="{{ $type->id }}"
                            {{ old('business_type_id', $businessRegistration->business_type_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                          </option>
                        @endforeach
                      </select>
                      @error('business_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label required">Deskripsi Usaha</label>
                  <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required
                    placeholder="Jelaskan detail usaha Anda...">{{ old('description', $businessRegistration->description) }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Lokasi</label>
                      <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                        value="{{ old('location', $businessRegistration->location) }}" required
                        placeholder="Alamat lokasi usaha">
                      @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Nomor Telepon</label>
                      <input type="text" name="contact_phone"
                        class="form-control @error('contact_phone') is-invalid @enderror"
                        value="{{ old('contact_phone', $businessRegistration->contact_phone) }}" required
                        placeholder="Nomor telepon yang dapat dihubungi">
                      @error('contact_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" name="contact_email"
                    class="form-control @error('contact_email') is-invalid @enderror"
                    value="{{ old('contact_email', $businessRegistration->contact_email) }}"
                    placeholder="Email (opsional)">
                  @error('contact_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-footer">
                  <button type="submit" class="btn btn-primary">
                    <i class="icon ti ti-refresh"></i>
                    Kirim Revisi
                  </button>
                  <a href="{{ route('business-registrations.show', $businessRegistration) }}"
                    class="btn btn-secondary">
                    Batal
                  </a>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Revision History -->
        @if ($businessRegistration->getRootRegistration()->revisions->isNotEmpty())
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Riwayat Revisi</h3>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-vcenter card-table">
                    <thead>
                      <tr>
                        <th>ID Revisi</th>
                        <th>Nama Usaha</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($businessRegistration->getRootRegistration()->revisions as $revision)
                        <tr class="{{ $revision->id === $businessRegistration->id ? 'table-active' : '' }}">
                          <td>#{{ $revision->id }}</td>
                          <td>{{ $revision->name }}</td>
                          <td>
                            <x-modules.business-registration.status-badge :status="$revision->status" />
                          </td>
                          <td>{{ $revision->created_at->format('d/m/Y H:i') }}</td>
                          <td>
                            <a href="{{ route('business-registrations.show', $revision) }}"
                              class="btn btn-sm btn-primary">
                              Lihat Detail
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
        @endif
      </div>
    </div>
  </div>
@endsection
