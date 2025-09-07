@php
  $user = auth('ebil')->user();
@endphp

<x-e-billing::layouts.panel>
  @include('e-billing::settings.partials._header')

  <div class="page-body">
    <div class="container-xl">
      <div class="card overflow-hidden">
        <div class="row g-0">
          @include('e-billing::settings.partials._menu')

          <form action="{{ route('e-billing.settings.update', ['group' => $group]) }}" method="POST"
            class="col-12 col-md-9 d-flex flex-column">
            @csrf
            @method('PUT')
            <div class="card-body">
              <h2 class="mb-4">Akun</h2>

              <div class="row g-5">
                <div class="col-md-6">
                  <h3 class="card-title">Informasi Akun</h3>
                  <p class="card-subtitle">Akun ini digunakan untuk mengakses layanan e-Billing</p>
                  <div class="mb-3">
                    <label class="form-label required">Nama</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                      value="{{ old('name') ?? $user->name }}" required placeholder="Masukkan nama">
                    @error('name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="mb-3">
                    <label class="form-label required">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                      value="{{ old('email') ?? $user->email }}" required placeholder="Masukkan email">
                    @error('email')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="col-md-6">
                  <h3 class="card-title">Ganti Password</h3>
                  <p class="card-subtitle">Kosongkan jika tidak ingin mengubah password</p>

                  <div class="mb-3">
                    <label class="form-label">Password lama</label>
                    <input type="password" name="old_password"
                      class="form-control @error('old_password') is-invalid @enderror" value="{{ old('old_password') }}"
                      placeholder="Masukkan password lama">
                    @error('old_password')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Password baru</label>
                    <input type="password" name="new_password"
                      class="form-control @error('new_password') is-invalid @enderror" value="{{ old('new_password') }}"
                      placeholder="Masukkan password baru">
                    @error('new_password')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Konfirmasi password baru</label>
                    <input type="password" name="new_password_confirmation"
                      class="form-control @error('new_password_confirmation') is-invalid @enderror"
                      placeholder="Masukkan konfirmasi password baru">
                    @error('new_password_confirmation')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer bg-transparent mt-auto">
              <div class="btn-list justify-content-end">
                <button type="reset" class="btn"> Reset </button>
                <button type="submit" class="btn btn-primary"> Simpan </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
