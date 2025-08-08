@extends('tablar::page')

@section('title', 'E-Billing')
@section('logo')
  <a href="{{ route('e-billing.dashboard') }}">E-Billing</a>
@endsection

@section('content')
  {{ $slot }}
@endsection
