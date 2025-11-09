@extends('tablar::page')

@section('title', 'Lengkapi Profil')

@section('content')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">
            Lengkapi Profil Anda
          </h2>
          <div class="text-muted mt-1">Silakan lengkapi data profil Anda untuk dapat mengakses semua layanan</div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <div class="row row-cards">
        <div class="col-md-12">
          <form action="{{ route('profile.store') }}" method="POST" class="card">
            @csrf
            <div class="card-header">
              <h3 class="card-title">Data Pribadi</h3>
            </div>
            <div class="card-body">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label required">NIK</label>
                  <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                    placeholder="Masukkan 16 digit NIK" value="{{ old('nik', $profile?->nik ?? '') }}" maxlength="16"
                    required>
                  @error('nik')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="form-hint">16 digit Nomor Induk Kependudukan</small>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Nomor KK</label>
                  <input type="text" name="kk" class="form-control @error('kk') is-invalid @enderror"
                    placeholder="Masukkan 16 digit Nomor KK" value="{{ old('kk', $profile?->kk ?? '') }}" maxlength="16">
                  @error('kk')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label required">Nomor WhatsApp</label>
                  <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                    placeholder="Contoh: 081234567890" value="{{ old('phone', $profile?->phone ?? '') }}" required>
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="form-hint">Format: 08xxxxxxxxxx atau +628xxxxxxxxxx</small>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Jenis Kelamin</label>
                  <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="L" {{ old('gender', $profile?->gender ?? '') == 'L' ? 'selected' : '' }}>Laki-laki
                    </option>
                    <option value="P" {{ old('gender', $profile?->gender ?? '') == 'P' ? 'selected' : '' }}>Perempuan
                    </option>
                  </select>
                  @error('gender')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Tempat Lahir</label>
                  <input type="text" name="place_of_birth"
                    class="form-control @error('place_of_birth') is-invalid @enderror" placeholder="Masukkan tempat lahir"
                    value="{{ old('place_of_birth', $profile?->place_of_birth ?? '') }}">
                  @error('place_of_birth')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label">Tanggal Lahir</label>
                  <input type="date" name="date_of_birth"
                    class="form-control @error('date_of_birth') is-invalid @enderror"
                    value="{{ old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d') ?? '') }}">
                  @error('date_of_birth')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Agama</label>
                  <select name="religion" class="form-select @error('religion') is-invalid @enderror">
                    <option value="">Pilih Agama</option>
                    <option value="Islam" {{ old('religion', $profile?->religion ?? '') == 'Islam' ? 'selected' : '' }}>
                      Islam
                    </option>
                    <option value="Kristen"
                      {{ old('religion', $profile?->religion ?? '') == 'Kristen' ? 'selected' : '' }}>
                      Kristen</option>
                    <option value="Katolik"
                      {{ old('religion', $profile?->religion ?? '') == 'Katolik' ? 'selected' : '' }}>
                      Katolik</option>
                    <option value="Hindu" {{ old('religion', $profile?->religion ?? '') == 'Hindu' ? 'selected' : '' }}>
                      Hindu
                    </option>
                    <option value="Buddha"
                      {{ old('religion', $profile?->religion ?? '') == 'Buddha' ? 'selected' : '' }}>
                      Buddha</option>
                    <option value="Konghucu"
                      {{ old('religion', $profile?->religion ?? '') == 'Konghucu' ? 'selected' : '' }}>
                      Konghucu</option>
                  </select>
                  @error('religion')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label">Status Perkawinan</label>
                  <select name="marital_status" class="form-select @error('marital_status') is-invalid @enderror">
                    <option value="">Pilih Status Perkawinan</option>
                    <option value="belum_kawin"
                      {{ old('marital_status', $profile?->marital_status ?? '') == 'belum_kawin' ? 'selected' : '' }}>
                      Belum
                      Kawin</option>
                    <option value="kawin"
                      {{ old('marital_status', $profile?->marital_status ?? '') == 'kawin' ? 'selected' : '' }}>Kawin
                    </option>
                    <option value="cerai_hidup"
                      {{ old('marital_status', $profile?->marital_status ?? '') == 'cerai_hidup' ? 'selected' : '' }}>
                      Cerai
                      Hidup</option>
                    <option value="cerai_mati"
                      {{ old('marital_status', $profile?->marital_status ?? '') == 'cerai_mati' ? 'selected' : '' }}>
                      Cerai
                      Mati</option>
                  </select>
                  @error('marital_status')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Pekerjaan</label>
                <input type="text" name="occupation" class="form-control @error('occupation') is-invalid @enderror"
                  placeholder="Masukkan pekerjaan" value="{{ old('occupation', $profile?->occupation ?? '') }}">
                @error('occupation')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="card-header">
              <h3 class="card-title">Alamat</h3>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label required">Alamat Lengkap</label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3"
                  placeholder="Masukkan alamat lengkap" required>{{ old('address', $profile?->address ?? '') }}</textarea>
                @error('address')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row mb-3">
                <div class="col-md-3">
                  <label class="form-label">RT</label>
                  <input type="text" name="rt" class="form-control @error('rt') is-invalid @enderror"
                    placeholder="001" value="{{ old('rt', $profile?->rt ?? '') }}" maxlength="5">
                  @error('rt')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-3">
                  <label class="form-label">RW</label>
                  <input type="text" name="rw" class="form-control @error('rw') is-invalid @enderror"
                    placeholder="001" value="{{ old('rw', $profile?->rw ?? '') }}" maxlength="5">
                  @error('rw')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label">Kelurahan/Desa</label>
                  <input type="text" name="kelurahan" class="form-control @error('kelurahan') is-invalid @enderror"
                    placeholder="Masukkan kelurahan/desa" value="{{ old('kelurahan', $profile?->kelurahan ?? '') }}">
                  @error('kelurahan')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4">
                  <label class="form-label">Kecamatan</label>
                  <input type="text" name="kecamatan" class="form-control @error('kecamatan') is-invalid @enderror"
                    placeholder="Masukkan kecamatan" value="{{ old('kecamatan', $profile?->kecamatan ?? '') }}">
                  @error('kecamatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-4">
                  <label class="form-label">Kabupaten/Kota</label>
                  <input type="text" name="kabupaten" class="form-control @error('kabupaten') is-invalid @enderror"
                    placeholder="Masukkan kabupaten/kota" value="{{ old('kabupaten', $profile?->kabupaten ?? '') }}">
                  @error('kabupaten')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-4">
                  <label class="form-label">Provinsi</label>
                  <input type="text" name="provinsi" class="form-control @error('provinsi') is-invalid @enderror"
                    placeholder="Masukkan provinsi" value="{{ old('provinsi', $profile?->provinsi ?? '') }}">
                  @error('provinsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Kode Pos</label>
                <input type="text" name="postal_code"
                  class="form-control @error('postal_code') is-invalid @enderror"
                  placeholder="Masukkan kode pos (5 digit)"
                  value="{{ old('postal_code', $profile?->postal_code ?? '') }}" maxlength="5">
                @error('postal_code')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="card-footer text-end">
              <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24"
                  height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M5 12l5 5l10 -10"></path>
                </svg>
                Simpan Profil
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    // Auto-format NIK and KK (numeric only)
    const nikInput = document.querySelector('input[name="nik"]');
    const kkInput = document.querySelector('input[name="kk"]');

    [nikInput, kkInput].forEach(input => {
      if (input) {
        input.addEventListener('input', function(e) {
          this.value = this.value.replace(/[^0-9]/g, '');
        });
      }
    });

    // Auto-format phone number
    const phoneInput = document.querySelector('input[name="phone"]');
    if (phoneInput) {
      phoneInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9+]/g, '');
      });
    }

    // Auto-format postal code
    const postalInput = document.querySelector('input[name="postal_code"]');
    if (postalInput) {
      postalInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
      });
    }
  </script>
@endsection
