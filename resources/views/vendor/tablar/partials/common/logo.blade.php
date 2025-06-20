<a href="#">
  @if (config('tablar.auth_logo.enabled'))
    <img src="{{ asset(config('tablar.auth_logo.img.path')) }}" width="110" height="32"
      alt="{{ asset(config('tablar.title', 'Tablar')) }}" class="navbar-brand-image">
  @else
    {!! config('tablar.logo', config('tablar.title')) !!}
  @endif
</a>
