@extends('tablar::auth.layout')
@section('title', 'Register')
@section('content')
  <div class="container container-tight py-4 my-auto">
    <div class="card card-md">
      <div class="card-body">
        <div class="text-center mb-5">
          <h2 class="h2">Selamat datang di {{ config('app.name') }}!</h2>
          <p class="text-secondary">Silakan lengkapi formulir di bawah ini untuk mendaftar, atau <a
              href="{{ route('login') }}">masuk</a> jika Anda sudah memiliki akun.</p>
        </div>
        <form action="{{ route('register') }}" method="post" autocomplete="off" novalidate>
          @csrf
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
              placeholder="Masukkan nama" autocomplete="off" value="{{ old('name') }}" required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
              placeholder="Masukkan email" autocomplete="off" value="{{ old('email') }}" required>
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group input-group-flat">
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="Kata sandi" autocomplete="off">
              <span class="input-group-text">
                <a href="#" class="link-secondary" title="Tampilkan password" data-bs-toggle="tooltip"
                  data-bs-placement="top" data-bs-trigger="hover" tabindex="-1" id="passwordToggle1">
                  <span class="icon ti ti-eye"></span>
                </a>
              </span>
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="mb-2">
            <label class="form-label">Konfirmasi Password</label>
            <div class="input-group input-group-flat">
              <input type="password" name="password_confirmation"
                class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Kata sandi"
                autocomplete="off">
              <span class="input-group-text">
                <a href="#" class="link-secondary" title="Tampilkan password" data-bs-toggle="tooltip"
                  data-bs-placement="top" data-bs-trigger="hover" tabindex="-1" id="passwordToggle2">
                  <span class="icon ti ti-eye"></span>
                </a>
              </span>
              @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
          </div>
        </form>
      </div>
    </div>
    @if (Route::has('login'))
      <div class="text-center text-muted mt-3">
        Sudah memiliki akun? <a href="{{ route('login') }}">Masuk</a>
      </div>
    @endif
  </div>
@endsection

@section('tablar_js')
  <script>
    const passwordToggle1 = document.querySelector('#passwordToggle1');
    const passwordToggle2 = document.querySelector('#passwordToggle2');

    passwordToggle1.addEventListener('click', function(event) {
      event.preventDefault();
      const tooltip = tabler.Tooltip.getInstance('#passwordToggle1');
      const input = this.parentNode.previousElementSibling;
      if (input.type === 'password') {
        input.type = 'text';
        this.children[0].classList.remove('ti-eye');
        this.children[0].classList.add('ti-eye-off');
        tooltip.setContent({
          ".tooltip-inner": 'Sembunyikan password'
        });
      } else {
        input.type = 'password';
        this.children[0].classList.remove('ti-eye-off');
        this.children[0].classList.add('ti-eye');
        tooltip.setContent({
          ".tooltip-inner": 'Tampilkan password'
        });
      }
    });

    passwordToggle2.addEventListener('click', function(event) {
      event.preventDefault();
      const tooltip = tabler.Tooltip.getInstance('#passwordToggle2');
      const input = this.parentNode.previousElementSibling;
      if (input.type === 'password') {
        input.type = 'text';
        this.children[0].classList.remove('ti-eye');
        this.children[0].classList.add('ti-eye-off');
        tooltip.setContent({
          ".tooltip-inner": 'Sembunyikan password'
        });
      } else {
        input.type = 'password';
        this.children[0].classList.remove('ti-eye-off');
        this.children[0].classList.add('ti-eye');
        tooltip.setContent({
          ".tooltip-inner": 'Tampilkan password'
        });
      }
    });
  </script>
@endsection
