@extends('tablar::auth.layout')

@section('title', 'E-Billing')

@section('tablar_css')
  @vite(['app-modules/e-billing/resources/css/e-billing.css'])
  @stack('css')
@endsection

@section('content')
  {{ $slot }}
@endsection

@section('tablar_js')
  @stack('js')
@endsection
