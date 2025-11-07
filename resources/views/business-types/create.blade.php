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
            Tambah Jenis Usaha
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
              <h3 class="card-title">Form Tambah Jenis Usaha</h3>
            </div>
            <div class="card-body">
              <form action="{{ route('business-types.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label class="form-label required">Nama Jenis Usaha</label>
                  <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" required placeholder="Contoh: Toko Kelontong">
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label">Deskripsi</label>
                  <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4"
                    placeholder="Jelaskan tentang jenis usaha ini...">{{ old('description') }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                      {{ old('is_active', true) ? 'checked' : '' }}>
                    <span class="form-check-label">Aktif</span>
                  </label>
                  <small class="form-hint">Jenis usaha yang aktif dapat digunakan untuk pendaftaran usaha baru</small>
                </div>

                <div class="form-footer">
                  <button type="submit" class="btn btn-primary">
                    Simpan
                  </button>
                  <a href="{{ route('business-types.index') }}" class="btn btn-secondary">
                    Kembali
                  </a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
