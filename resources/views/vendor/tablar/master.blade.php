@php
  $dataTheme = '';

  if (!empty($themeConfig)) {
      foreach ($themeConfig as $key => $value) {
          if ($value !== '') {
              $dataTheme .= " data-bs-{$key}=\"{$value}\"";
          }
      }
  }
@endphp

<!doctype html>
<html lang="{{ Config::get('app.locale') }}" {!! config('tablar.layout') == 'rtl' ? 'dir="rtl"' : '' !!} {!! $dataTheme !!}>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
  <link rel="manifest" href="/site.webmanifest">

  {{-- Custom Meta Tags --}}
  @yield('meta_tags')
  {{-- Title --}}
  <title>
    @yield('title_prefix', config('tablar.title_prefix', ''))
    @yield('title', config('tablar.title', 'Tablar'))
    @yield('title_postfix', config('tablar.title_postfix', ''))
  </title>

  <!-- Fonts -->
  <style>
    @import url("https://rsms.me/inter/inter.css");
  </style>

  <!-- CSS/JS files -->
  @if (config('tablar', 'vite'))
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/sass/tabler.scss', 'resources/sass/tabler-icons.scss'])
  @endif

  {{-- Livewire Styles --}}
  @if (config('tablar.livewire'))
    @livewireStyles
  @endif

  {{-- Custom Stylesheets (post Tablar) --}}
  @yield('tablar_css')

</head>
@yield('body')

{{-- Livewire Script --}}
@if (config('tablar.livewire'))
  @livewireScripts
@endif

@yield('tablar_js')

</html>
