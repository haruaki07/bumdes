@extends('tablar::page')

@section('title', 'Edit Profil')

@section('content')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">
            Edit Profil
          </h2>
          <div class="text-muted mt-1">Kelola informasi akun dan profil Anda</div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <div class="row row-cards">
        <div class="col-md-3">
          <!-- Profile Sidebar -->
          <div class="card">
            <div class="card-body text-center py-5">
              <div class="mb-3">
                <span class="avatar avatar-xl avatar-rounded bg-blue text-blue-fg">
                  {{ get_initials($user->name) }}
                </span>
              </div>
              <h3 class="m-0 mb-1">{{ $user->name }}</h3>
              <div class="text-muted mb-3">{{ $user->email }}</div>
              <div class="mt-3">
                <span class="badge bg-blue-lt">{{ ucfirst($user->role) }}</span>
              </div>
            </div>
          </div>

          <!-- Quick Stats for Warga -->
          @if ($user->role === 'warga' && $profile)
            <div class="card mt-3">
              <div class="card-header">
                <h3 class="card-title">Informasi Singkat</h3>
              </div>
              <div class="list-group list-group-flush">
                @if ($profile->nik)
                  <div class="list-group-item">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <i class="icon ti ti-user-square text-muted"></i>
                      </div>
                      <div class="col text-truncate">
                        <div class="text-muted small">NIK</div>
                        <div class="text-truncate">{{ $profile->nik }}</div>
                      </div>
                    </div>
                  </div>
                @endif
                @if ($profile->phone)
                  <div class="list-group-item">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <i class="icon ti ti-phone text-muted"></i>
                      </div>
                      <div class="col text-truncate">
                        <div class="text-muted small">WhatsApp</div>
                        <div class="text-truncate">{{ $profile->phone }}</div>
                      </div>
                    </div>
                  </div>
                @endif
              </div>
            </div>
          @endif
        </div>

        <div class="col-md-9">
          <!-- Tabs -->
          <div class="card">
            <div class="card-header">
              <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <a href="#tabs-account" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                    role="tab">
                    <i class="ti ti-user me-2"></i>
                    Akun
                  </a>
                </li>
                @if ($user->role === 'warga')
                  <li class="nav-item" role="presentation">
                    <a href="#tabs-warga-profile" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                      role="tab" tabindex="-1">
                      <i class="ti ti-user-square me-2"></i>
                      Profil Warga
                    </a>
                  </li>
                @endif
                <li class="nav-item" role="presentation">
                  <a href="#tabs-password" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                    tabindex="-1">
                    <i class="ti ti-lock me-2"></i>
                    Password
                  </a>
                </li>
              </ul>
            </div>
            <div class="card-body">
              <div class="tab-content">
                <!-- Account Tab -->
                <div class="tab-pane active show" id="tabs-account" role="tabpanel">
                  <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                      <label class="form-label required">Nama Lengkap</label>
                      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        placeholder="Masukkan nama lengkap" value="{{ old('name', $user->name) }}" required>
                      @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="mb-3">
                      <label class="form-label required">Email</label>
                      <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        placeholder="Masukkan email" value="{{ old('email', $user->email) }}" required>
                      @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                      <small class="form-hint">Email digunakan untuk login dan notifikasi</small>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Role</label>
                      <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                      <small class="form-hint">Role tidak dapat diubah</small>
                    </div>
                    <div class="d-flex justify-content-end">
                      <button type="submit" class="btn btn-primary">
                        <i class="icon ti ti-check"></i>
                        Simpan Perubahan
                      </button>
                    </div>
                  </form>
                </div>

                <!-- Warga Profile Tab -->
                @if ($user->role === 'warga')
                  <div class="tab-pane" id="tabs-warga-profile" role="tabpanel">
                    <form action="{{ route('profile.warga.update') }}" method="POST">
                      @csrf
                      @method('PUT')

                      <h3 class="mb-3">Data Pribadi</h3>
                      <div class="row mb-3">
                        <div class="col-md-6">
                          <label class="form-label required">NIK</label>
                          <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                            placeholder="Masukkan 16 digit NIK" value="{{ old('nik', $profile?->nik ?? '') }}"
                            maxlength="16" required>
                          @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Nomor KK</label>
                          <input type="text" name="kk" class="form-control @error('kk') is-invalid @enderror"
                            placeholder="Masukkan 16 digit Nomor KK" value="{{ old('kk', $profile?->kk ?? '') }}"
                            maxlength="16">
                          @error('kk')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="row mb-3">
                        <div class="col-md-6">
                          <label class="form-label required">Nomor WhatsApp</label>
                          <input type="text" name="phone"
                            class="form-control @error('phone') is-invalid @enderror" placeholder="Contoh: 081234567890"
                            value="{{ old('phone', $profile?->phone ?? '') }}" data-mask-phone required>
                          @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Jenis Kelamin</label>
                          <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L"
                              {{ old('gender', $profile?->gender ?? '') == 'L' ? 'selected' : '' }}>
                              Laki-laki
                            </option>
                            <option value="P"
                              {{ old('gender', $profile?->gender ?? '') == 'P' ? 'selected' : '' }}>
                              Perempuan
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
                            class="form-control @error('place_of_birth') is-invalid @enderror"
                            placeholder="Masukkan tempat lahir"
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
                            <option value="Islam"
                              {{ old('religion', $profile?->religion ?? '') == 'Islam' ? 'selected' : '' }}>
                              Islam
                            </option>
                            <option value="Kristen"
                              {{ old('religion', $profile?->religion ?? '') == 'Kristen' ? 'selected' : '' }}>
                              Kristen</option>
                            <option value="Katolik"
                              {{ old('religion', $profile?->religion ?? '') == 'Katolik' ? 'selected' : '' }}>
                              Katolik</option>
                            <option value="Hindu"
                              {{ old('religion', $profile?->religion ?? '') == 'Hindu' ? 'selected' : '' }}>
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
                          <select name="marital_status"
                            class="form-select @error('marital_status') is-invalid @enderror">
                            <option value="">Pilih Status Perkawinan</option>
                            <option value="belum_kawin"
                              {{ old('marital_status', $profile?->marital_status ?? '') == 'belum_kawin' ? 'selected' : '' }}>
                              Belum
                              Kawin</option>
                            <option value="kawin"
                              {{ old('marital_status', $profile?->marital_status ?? '') == 'kawin' ? 'selected' : '' }}>
                              Kawin
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
                        <input type="text" name="occupation"
                          class="form-control @error('occupation') is-invalid @enderror"
                          placeholder="Masukkan pekerjaan"
                          value="{{ old('occupation', $profile?->occupation ?? '') }}">
                        @error('occupation')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>

                      <h3 class="mb-3">Alamat</h3>

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
                          <input type="text" name="kelurahan"
                            class="form-control @error('kelurahan') is-invalid @enderror"
                            placeholder="Masukkan kelurahan/desa"
                            value="{{ old('kelurahan', $profile?->kelurahan ?? '') }}">
                          @error('kelurahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="row mb-3">
                        <div class="col-md-4">
                          <label class="form-label">Kecamatan</label>
                          <input type="text" name="kecamatan"
                            class="form-control @error('kecamatan') is-invalid @enderror"
                            placeholder="Masukkan kecamatan"
                            value="{{ old('kecamatan', $profile?->kecamatan ?? '') }}">
                          @error('kecamatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                        <div class="col-md-4">
                          <label class="form-label">Kabupaten/Kota</label>
                          <input type="text" name="kabupaten"
                            class="form-control @error('kabupaten') is-invalid @enderror"
                            placeholder="Masukkan kabupaten/kota"
                            value="{{ old('kabupaten', $profile?->kabupaten ?? '') }}">
                          @error('kabupaten')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                        <div class="col-md-4">
                          <label class="form-label">Provinsi</label>
                          <input type="text" name="provinsi"
                            class="form-control @error('provinsi') is-invalid @enderror"
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

                      <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                          <i class="icon ti ti-check"></i>
                          Simpan Perubahan
                        </button>
                      </div>
                    </form>
                  </div>
                @endif

                <!-- Password Tab -->
                <div class="tab-pane" id="tabs-password" role="tabpanel">
                  <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                      <label class="form-label required">Password Saat Ini</label>
                      <input type="password" name="current_password"
                        class="form-control @error('current_password') is-invalid @enderror"
                        placeholder="Masukkan password saat ini" required>
                      @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="mb-3">
                      <label class="form-label required">Password Baru</label>
                      <input type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Masukkan password baru (minimal 8 karakter)" required>
                      @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                      <small class="form-hint">Minimal 8 karakter</small>
                    </div>
                    <div class="mb-3">
                      <label class="form-label required">Konfirmasi Password Baru</label>
                      <input type="password" name="password_confirmation" class="form-control"
                        placeholder="Masukkan ulang password baru" required>
                    </div>
                    <div class="alert alert-info" role="alert">
                      <div class="d-flex gap-2">
                        <div class="alert-icon">
                          <i class="ti ti-info-circle icon"></i>
                        </div>
                        <div>
                          <h4 class="alert-title">Tips keamanan password</h4>
                          <div class="text-muted">
                            <ul class="mb-0">
                              <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                              <li>Jangan gunakan informasi pribadi yang mudah ditebak</li>
                              <li>Gunakan password yang berbeda untuk setiap akun</li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex justify-content-end">
                      <button type="submit" class="btn btn-primary">
                        <i class="icon ti ti-key"></i>
                        Ubah Password
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
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

    // Password visibility toggle (optional enhancement)
    document.querySelectorAll('input[type="password"]').forEach(input => {
      const wrapper = input.parentElement;
      if (!wrapper.querySelector('.password-toggle')) {
        // Add toggle button if needed
      }
    });
  </script>
@endsection
