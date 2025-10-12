@extends('tablar::page')

@section('title', 'E-Billing')
@section('logo')
  <a href="{{ route('e-billing.dashboard') }}">E-Billing</a>
@endsection

@push('css')
  @vite(['app-modules/e-billing/resources/css/e-billing.css', 'app-modules/e-billing/resources/js/main.js'])
@endpush

@section('content')
  {{ $slot }}
@endsection
