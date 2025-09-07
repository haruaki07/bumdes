<x-e-billing::layouts.panel>
  @include('e-billing::settings.partials._header')

  <div class="page-body">
    <div class="container-xl">
      <div class="card overflow-hidden">
        <div class="row g-0">
          @include('e-billing::settings.partials._menu')

          <form id="settingsForm" action="{{ route('e-billing.settings.update', ['group' => $group->value]) }}"
            method="POST" enctype="multipart/form-data" class="col-12 col-md-9 d-flex flex-column">
            @csrf
            @method('PUT')
            <div class="card-body">
              <h2 class="mb-4">Profil Usaha</h2>

              <h3 class="card-title">Informasi Usaha</h3>
              <p class="card-subtitle">
                Informasi di bawah digunakan untuk keperluan tagihan/faktur dan halaman pembayaran
              </p>

              <div class="row">
                <div class="col-12 mb-3">
                  <label class="form-label">Logo usaha</label>
                  <div class="row align-items-center">
                    <div class="col-auto"><span id="logoPreview" class="avatar avatar-xl"
                        style="background-image: url({{ asset($settings->logo) }})"> </span></div>
                    <div class="col-auto">
                      <label class="btn btn-icon" data-bs-toggle="tooltip" title="Ganti logo" data-bs-placement="top">
                        <i class="ti ti-edit"></i>
                        <input id="logoInput" type="file" name="logo" class="d-none"
                          accept=".jpg,.jpeg,.png,.svg">
                      </label>
                    </div>
                    @error('logo')
                      <div class="col-auto">
                        <div class="invalid-feedback">{{ $message }}</div>
                      </div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label required">Nama usaha</label>
                  <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') ?? $settings->name }}" required
                    placeholder="Masukkan nama channel pembayaran">
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label required">Keterangan usaha</label>
                  <input type="text" name="description"
                    class="form-control @error('description') is-invalid @enderror"
                    value="{{ old('description') ?? $settings->description }}" required
                    placeholder="Masukkan keterangan usaha">
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label required">Nomor telepon</label>
                  <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone') ?? $settings->phone }}" required placeholder="Masukkan nomor telepon">
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label required">Alamat email</label>
                  <input type="text" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') ?? $settings->email }}" required placeholder="Masukkan alamat email">
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label required">Alamat usaha</label>
                  <textarea name="address" class="form-control @error('address') is-invalid @enderror" required
                    placeholder="Masukkan alamat usaha (Jl. Contoh No. 123)">{{ old('address') ?? $settings->address }}</textarea>
                  @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
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

  @push('js')
    <script type="module">
      const logoInput = document.getElementById('logoInput');
      const logoPreview = document.getElementById('logoPreview');

      logoInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = (e) => {
            logoPreview.style.backgroundImage = `url(${e.target.result})`;
          };
          reader.readAsDataURL(file);
        }
      });

      const settingsForm = document.getElementById('settingsForm');
      settingsForm.addEventListener('reset', (event) => {
        logoPreview.style.backgroundImage = 'url({{ asset($settings->logo) }})';
      });
    </script>
  @endpush
</x-e-billing::layouts.panel>
