@extends('tablar::auth.layout')

@section('title', $title ?? 'Tidak ditemukan')

@section('content')
  <div class="empty">
    <div class="empty-header">404</div>
    <p class="empty-title">{{ $title ?? 'Halaman tidak ditemukan' }}</p>
    <p class="empty-subtitle text-secondary">
      {{ $message ?? 'Sumber yang Anda cari tidak tersedia atau telah dipindahkan.' }}
    </p>
  </div>
@endsection
