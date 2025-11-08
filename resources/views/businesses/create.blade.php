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
            Tambah Usaha
          </h2>
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
              <h3 class="card-title">Form Tambah Usaha</h3>
            </div>
            <div class="card-body">
              <form action="{{ route('businesses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                  <label class="form-label required">Nama Usaha</label>
                  <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label required">Jenis Usaha</label>
                  <select name="business_type_id" class="form-select @error('business_type_id') is-invalid @enderror"
                    required>
                    <option value="">Pilih Jenis Usaha</option>
                    @foreach ($businessTypes as $type)
                      <option value="{{ $type->id }}" {{ old('business_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('business_type_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label required">Deskripsi</label>
                  <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description') }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label required">Lokasi</label>
                  <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                    value="{{ old('location') }}" required>
                  @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label required">Nomor Telepon</label>
                  <input type="text" name="contact_phone"
                    class="form-control @error('contact_phone') is-invalid @enderror" value="{{ old('contact_phone') }}"
                    required>
                  @error('contact_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" name="contact_email"
                    class="form-control @error('contact_email') is-invalid @enderror" value="{{ old('contact_email') }}">
                  @error('contact_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label">Dokumen Pendukung</label>
                  <input type="file" name="document"
                    class="form-control form-dropzone @error('document') is-invalid @enderror"
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                  <small class="form-hint">Format: PDF, DOC, DOCX, JPG, PNG. Max: 5MB</small>
                  @error('document')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-footer">
                  <button type="submit" class="btn btn-primary">Simpan</button>
                  <a href="{{ route('businesses.index') }}" class="btn btn-secondary">Batal</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script type="module">
    new Dropzone(".form-dropzone", {
      maxFileSize: 5 * 1024 * 1024,
    });
  </script>
@endpush
