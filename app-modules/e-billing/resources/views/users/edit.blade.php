<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Pengaturan
          </div>
          <h2 class="page-title">
            Edit User
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
              <form action="{{ route('e-billing.settings.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Nama</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                      value="{{ old('name', $user->name) }}" required placeholder="Nama lengkap">
                    @error('name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                      value="{{ old('email', $user->email) }}" required placeholder="email@example.com">
                    @error('email')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Role</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required
                      data-tom-select>
                      <option value="">Pilih Role</option>
                      @foreach ($roles as $role)
                        <option value="{{ $role->name }}"
                          {{ old('role', $user->getRoleNames()->first()) == $role->name ? 'selected' : '' }}>
                          {{ $role->name }}
                        </option>
                      @endforeach
                    </select>
                    @error('role')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                      <input type="text" name="password" class="form-control @error('password') is-invalid @enderror"
                        id="password" placeholder="Password minimal 8 karakter">
                      <button type="button" class="btn btn-outline-secondary"
                        onclick="document.getElementById('password').value = Math.random().toString(36).slice(2, 10)">
                        Acak
                      </button>
                    </div>
                    <div class="form-text">Kosongkan jika tidak diubah</div>
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

  @push('js')
    <script type="module">
      document.querySelectorAll('[data-tom-select]').forEach(select => {
        new TomSelect(select, {
          maxItems: 1
        });
      });
    </script>
  @endpush
</x-e-billing::layouts.panel>
