<!doctype html>
<html lang="{{ Config::get('app.locale') }}">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
  <link rel="manifest" href="/site.webmanifest">
  <title>@yield('title')</title>

  <!-- CSS/JS files -->
  @if (config('tablar', 'vite'))
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/sass/tabler.scss', 'resources/sass/tabler-icons.scss'])
  @endif
  {{-- Custom Stylesheets (post Tablar) --}}
  @yield('tablar_css')

</head>

<body class="d-flex flex-column">
  <div class="page">
    @yield('content')
  </div>

  @yield('tablar_js')

</html>
