<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Pengaturan
          </div>
          <h2 class="page-title">
            Tambah User
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a href="{{ route('e-billing.settings.users.index') }}" class="btn btn-secondary">Kembali</a>
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
              <h3 class="card-title">Data User</h3>
            </div>
            <div class="card-body">
              <form action="{{ route('e-billing.settings.users.store') }}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Nama</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                      value="{{ old('name') }}" required>
                    @error('name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                      value="{{ old('email') }}" required>
                    @error('email')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Role</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                      <option value="">Pilih role</option>

                      @foreach (\Modules\EBilling\Enums\UserRole::cases() as $role)
                        <option value="{{ $role->value }}" {{ old('role') == $role->value ? 'selected' : '' }}>
                          {{ $role->label() }}</option>
                      @endforeach
                    </select>
                    @error('role')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Password</label>
                    <div class="input-group">
                      <input type="text" name="password" class="form-control @error('password') is-invalid @enderror"
                        required id="password">
                      <button type="button" class="btn btn-outline-secondary"
                        onclick="document.getElementById('password').value = Math.random().toString(36).slice(2, 10)">
                        Acak
                      </button>
                    </div>
                    @error('password')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
