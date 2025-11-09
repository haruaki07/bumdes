@extends('tablar::page')

@section('content')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">
            Ajukan Pendanaan Usaha
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a class="btn btn-secondary" href="{{ route('funding-requests.index') }}">Kembali</a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <div class="row g-3">
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Persyaratan</h3>
            </div>
            <div class="card-body">
              <div class="divide-y">
                <div class="py-2">
                  <div class="d-flex align-items-center">
                    <i class="ti ti-check text-success me-2"></i>
                    <div>Memiliki usaha terdaftar dan aktif</div>
                  </div>
                </div>
                <div class="py-2">
                  <div class="d-flex align-items-center">
                    <i class="ti ti-check text-success me-2"></i>
                    <div>Profil warga lengkap dan terverifikasi</div>
                  </div>
                </div>
                <div class="py-2">
                  <div class="d-flex align-items-center">
                    <i class="ti ti-check text-success me-2"></i>
                    <div>Tujuan pendanaan jelas dan terukur</div>
                  </div>
                </div>
                <div class="py-2">
                  <div class="d-flex align-items-center">
                    <i class="ti ti-check text-success me-2"></i>
                    <div>Bersedia menandatangani MOU</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card mt-3">
            <div class="card-header">
              <h3 class="card-title">Alur Proses</h3>
            </div>
            <div class="card-body">
              <ol class="list-unstyled">
                <li class="py-2">
                  <div class="d-flex">
                    <span class="badge w-6 h-6 bg-blue text-blue-fg me-3">1</span>
                    <div>
                      <strong>Pengajuan</strong>
                      <div class="text-muted small">Isi formulir pengajuan</div>
                    </div>
                  </div>
                </li>
                <li class="py-2">
                  <div class="d-flex">
                    <span class="badge w-6 h-6 bg-azure text-azure-fg me-3">2</span>
                    <div>
                      <strong>Peninjauan</strong>
                      <div class="text-muted small">Tim BUMDes meninjau</div>
                    </div>
                  </div>
                </li>
                <li class="py-2">
                  <div class="d-flex">
                    <span class="badge w-6 h-6 bg-indigo text-indigo-fg me-3">3</span>
                    <div>
                      <strong>MOU</strong>
                      <div class="text-muted small">Tanda tangan MOU</div>
                    </div>
                  </div>
                </li>
                <li class="py-2">
                  <div class="d-flex">
                    <span class="badge w-6 h-6 bg-success text-success-fg me-3">4</span>
                    <div>
                      <strong>Pencairan</strong>
                      <div class="text-muted small">Dana dicairkan</div>
                    </div>
                  </div>
                </li>
              </ol>
            </div>
          </div>
        </div>

        <div class="col-md-8">
          <form action="{{ route('funding-requests.store') }}" method="POST">
            @csrf

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Informasi Pengajuan</h3>
              </div>
              <div class="card-body">
                <div class="row mb-3">
                  <div class="col-12">
                    <div class="alert alert-info">
                      <div class="d-flex gap-2">
                        <div class="alert-icon">
                          <i class="ti ti-info-circle"></i>
                        </div>
                        <div>
                          <h4 class="alert-title">Informasi Penting</h4>
                          <div class="alert-description">
                            Pastikan informasi yang Anda masukkan akurat dan lengkap. Pengajuan akan ditinjau oleh tim
                            BUMDes
                            dan Anda akan menerima notifikasi melalui email.
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label required">Usaha</label>
                  <select name="business_id" class="form-select @error('business_id') is-invalid @enderror" required
                    data-tom-select>
                    <option value="">Pilih Usaha</option>
                    @foreach ($businesses as $business)
                      <option value="{{ $business->id }}" {{ old('business_id') == $business->id ? 'selected' : '' }}>
                        {{ $business->name }} - {{ $business->businessType->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('business_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  @if ($businesses->isEmpty())
                    <div class="form-hint text-danger">
                      Anda belum memiliki usaha aktif. <a href="{{ route('businesses.create') }}">Daftarkan usaha</a>
                      terlebih dahulu.
                    </div>
                  @endif
                </div>

                <div class="mb-3">
                  <label class="form-label required">Jumlah Pendanaan</label>
                  <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="text" name="amount" class="form-control @error('amount') is-invalid @enderror"
                      placeholder="0" value="{{ old('amount') }}" required data-mask-currency>
                    @error('amount')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-hint">
                    Minimal Rp 1.000.000 - Maksimal Rp 500.000.000. Jumlah akan ditinjau oleh tim BUMDes.
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Durasi Pembayaran (Opsional)</label>
                  <div class="input-group">
                    <input type="number" name="repayment_duration_months"
                      class="form-control @error('repayment_duration_months') is-invalid @enderror" placeholder="12"
                      min="1" max="60" value="{{ old('repayment_duration_months') }}">
                    <span class="input-group-text">Bulan</span>
                    @error('repayment_duration_months')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-hint">
                    Estimasi durasi pembayaran. Admin akan menentukan durasi akhir saat persetujuan. Kosongkan jika belum
                    pasti.
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label required">Tujuan Pendanaan</label>
                  <textarea name="purpose" rows="6" class="form-control @error('purpose') is-invalid @enderror"
                    placeholder="Jelaskan secara detail tujuan penggunaan dana..." required>{{ old('purpose') }}</textarea>
                  @error('purpose')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <div class="form-hint">
                    Minimal 50 karakter. Jelaskan secara rinci untuk apa dana akan digunakan (modal usaha, pembelian alat,
                    pengembangan produk, dll).
                  </div>
                </div>
              </div>

              <div class="card-footer text-end">
                <div class="d-flex justify-content-end gap-2">
                  <a href="{{ route('funding-requests.index') }}" class="btn btn-link">Batal</a>
                  <button type="submit" class="btn btn-primary" {{ $businesses->isEmpty() ? 'disabled' : '' }}>
                    <i class="icon ti ti-send"></i>
                    Ajukan Pendanaan
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script type="module">
    document.querySelectorAll('[data-tom-select]').forEach(select => {
      new TomSelect(select, {
        maxItems: 1
      });
    });
  </script>
@endpush
