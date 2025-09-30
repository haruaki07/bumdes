@php
  $authenticated = false;
  $guard = 'web';
  $logoutUrl = route('logout');
  if (Auth::guard('ebil')->check()) {
      $authenticated = true;
      $guard = 'ebil';
      $logoutUrl = route('e-billing.logout');
  }

  if (Auth::check()) {
      $authenticated = true;
  }

  $user = $authenticated ? auth($guard)->user() : null;
@endphp

@if ($authenticated)
  <div class="nav-item dropdown">
    <a href="#" class="nav-link d-flex lh-1 text-reset p-0 px-lg-2" data-bs-toggle="dropdown"
      aria-label="Open user menu">
      <span class="avatar avatar-sm">
        {{ get_initials($user->name) }}
      </span>
      <div class="d-none d-xl-block ps-2">
        <div>{{ $user->name }}</div>
        @if ($guard === 'ebil')
          <div class="mt-1 small text-muted">{{ $user->getRoleNames()->first() }}</div>
        @endif
      </div>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">

      @if ($guard === 'ebil')
        @php($settings_url = route('e-billing.settings.show', ['group' => 'account']))
      @else
        @php($settings_url = '#')
      @endif

      <a href="{{ $settings_url }}" class="dropdown-item">Pengaturan</a>
      <div class="dropdown-divider"></div>
      <a class="dropdown-item text-danger" href="#"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="ti ti-logout icon"></i>
        Keluar
      </a>

      <form id="logout-form" action="{{ $logoutUrl }}" method="POST" style="display: none;">
        @if (config('tablar.logout_method'))
          {{ method_field(config('tablar.logout_method')) }}
        @endif
        {{ csrf_field() }}
      </form>

    </div>
  </div>
@endauth
