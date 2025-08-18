@extends('tablar::auth.layout')
@section('title', 'Login')
@section('content')
  <div class="container container-tight py-4">
    {{-- <div class="text-center mb-1 mt-5">
      <a href="" class="navbar-brand navbar-brand-autodark">
        <img src="{{ asset(config('tablar.auth_logo.img.path', 'assets/logo.svg')) }}" height="36" alt=""></a>
    </div> --}}
    <div class="card card-md">
      <div class="card-body">
        <div class="text-center mb-5">
          <h2 class="h2">Selamat datang di {{ config('app.name') }}!</h2>
          <p class="text-secondary">Silakan masukkan kredensial Anda di bawah ini untuk masuk, atau <a
              href="{{ route('register') }}">daftar</a> jika Anda belum memiliki akun.</p>
        </div>
        <form action="{{ $url ?? route('login') }}" method="post" autocomplete="off" novalidate>
          @csrf
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
              placeholder="Masukkan email" autocomplete="off" value="{{ old('email') }}" required>
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-2">
            <label class="form-label">
              Password
              <span class="form-label-description">
                <a href="{{ route('password.request') }}" tabindex="-1" data-bs-toggle="tooltip"
                  title="Klik untuk mengatur ulang password" data-bs-placement="top">Lupa password?</a>
              </span>
            </label>
            <div class="input-group input-group-flat">
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="Kata sandi" autocomplete="off">
              <span class="input-group-text">
                <a href="#" class="link-secondary" title="Tampilkan password" data-bs-toggle="tooltip"
                  data-bs-placement="top" data-bs-trigger="hover" tabindex="-1" id="passwordToggle">
                  <span class="icon ti ti-eye"></span>
                </a>
              </span>
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="mb-2">
            <label class="form-check">
              <input type="checkbox" class="form-check-input" />
              <span class="form-check-label">Ingat saya di perangkat ini</span>
            </label>
          </div>
          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Masuk</button>
          </div>
        </form>
      </div>
      {{-- <div class="hr-text">atau</div>
      <div class="card-body">
        <div class="row">
          <div class="col"><a href="#" class="btn btn-white w-100">
              <!-- Download SVG icon from http://tabler-icons.io/i/brand-github -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon text-github" width="24" height="24"
                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path
                  d="M9 19c-4.3 1.4 -4.3 -2.5 -6 -3m12 5v-3.5c0 -1 .1 -1.4 -.5 -2c2.8 -.3 5.5 -1.4 5.5 -6a4.6 4.6 0 0 0 -1.3 -3.2a4.2 4.2 0 0 0 -.1 -3.2s-1.1 -.3 -3.5 1.3a12.3 12.3 0 0 0 -6.2 0c-2.4 -1.6 -3.5 -1.3 -3.5 -1.3a4.2 4.2 0 0 0 -.1 3.2a4.6 4.6 0 0 0 -1.3 3.2c0 4.6 2.7 5.7 5.5 6c-.6 .6 -.6 1.2 -.5 2v3.5" />
              </svg>
              Login with Github
            </a></div>
          <div class="col"><a href="#" class="btn btn-white w-100">
              <!-- Download SVG icon from http://tabler-icons.io/i/brand-twitter -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon text-twitter" width="24" height="24"
                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path
                  d="M22 4.01c-1 .49 -1.98 .689 -3 .99c-1.121 -1.265 -2.783 -1.335 -4.38 -.737s-2.643 2.06 -2.62 3.737v1c-3.245 .083 -6.135 -1.395 -8 -4c0 0 -4.182 7.433 4 11c-1.872 1.247 -3.739 2.088 -6 2c3.308 1.803 6.913 2.423 10.034 1.517c3.58 -1.04 6.522 -3.723 7.651 -7.742a13.84 13.84 0 0 0 .497 -3.753c-.002 -.249 1.51 -2.772 1.818 -4.013z" />
              </svg>
              Login with Twitter
            </a></div>
        </div>
      </div> --}}
    </div>
    @if (Route::has('register'))
      <div class="text-center text-muted mt-3">
        Belum memiliki akun? <a href="{{ route('register') }}">Daftar</a>
      </div>
    @endif
  </div>
@endsection

@section('tablar_js')
  <script>
    const passwordToggle = document.querySelector('#passwordToggle');

    passwordToggle.addEventListener('click', function(event) {
      event.preventDefault();
      const tooltip = tabler.Tooltip.getInstance('#passwordToggle');
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
