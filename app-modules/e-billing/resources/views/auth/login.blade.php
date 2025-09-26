@php
  use Modules\EBilling\Settings\EBillingBusinessProfileSettings;

  $settings = app(EBillingBusinessProfileSettings::class);
@endphp

@extends('tablar::auth.layout')
@section('title', 'Login')
@section('content')
  <div class="container container-tight py-4 my-auto">
    <div class="card card-md">
      <div class="card-body">
        <div class="text-center mb-5">
          <h2 class="h2">Selamat datang di E-Billing!</h2>
          <p class="text-secondary">Silakan masukkan kredensial Anda di bawah ini untuk melanjutkan.</p>
        </div>
        <form action="{{ route('e-billing.login') }}" method="post" autocomplete="off" novalidate>
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
    </div>
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
