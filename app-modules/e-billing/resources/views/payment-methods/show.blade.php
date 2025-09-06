@php
  use Modules\EBilling\Enums\PaymentMethodType;
  use Modules\EBilling\Enums\PaymentMethodFeeType;
@endphp

<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Pengaturan
          </div>
          <h2 class="page-title">
            Edit Metode Pembayaran
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a class="btn btn-secondary" href="{{ route('e-billing.settings.payment-methods.index') }}">Kembali</a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')
      <div class="row row-deck row-cards">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Informasi Metode Pembayaran</h3>
            </div>
            <div class="card-body">
              <form action="{{ route('e-billing.settings.payment-methods.update', $paymentMethod) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                  <label class="form-label">Jenis Pembayaran</label>
                  <input type="text" name="type" readonly class="form-control" value="{{ $paymentMethod->type }}">
                </div>

                <div class="row gx-5">
                  <div class="col-xl-8">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label required">Nama</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                          value="{{ old('name') ?? $paymentMethod->name }}" required
                          placeholder="Masukkan nama channel pembayaran">
                        @error('name')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="col-md-6 mb-3">
                        <label class="form-label">Kode Channel</label>
                        <input type="text" readonly class="form-control" value="{{ $paymentMethod->code }}">
                      </div>

                      @if ($paymentMethod->type === PaymentMethodType::BANK_TRANSFER)
                        <div class="col-md-6 mb-3">
                          <label class="form-label required">Nomor Rekening</label>
                          <input type="text" name="account_number"
                            class="form-control @error('account_number') is-invalid @enderror"
                            value="{{ old('account_number') ?? $paymentMethod->account_number }}"
                            placeholder="Masukkan nomor rekening" required>
                          @error('account_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      @endif

                      <div class="col-md-6 mb-3">
                        <div class="form-label">Status</div>
                        <label class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="is_active"
                            {{ $paymentMethod->is_active ? 'checked' : '' }} />
                          <span
                            class="form-check-label">{{ $paymentMethod->is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
                        </label>
                        @error('status')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-xl-4">
                    <div class="mb-3">
                      @if ($paymentMethod->brand_logo)
                        <label class="form-label">Logo</label>
                        <div class="d-flex align-items-end">
                          <img src="{{ asset($paymentMethod->brand_logo) }}" alt="Logo {{ $paymentMethod->name }}"
                            class="img-fluid pt-3 pe-3 flex-shrink-0" style="height:5rem;object-fit:cover"
                            loading="lazy" id="brandLogoPreview" />
                          <div class="d-flex flex-column">
                            <button type="button" class="btn btn-icon mb-1 d-none" data-bs-toggle="tooltip"
                              title="Reset Logo" data-bs-placement="top" id="brandLogoReset">
                              <i class="ti ti-refresh"></i>
                            </button>
                            <label class="btn btn-icon" data-bs-toggle="tooltip" title="Ubah Logo"
                              data-bs-placement="top">
                              <i class="ti ti-edit"></i>
                              <input type="file" name="brand_logo" class="d-none" accept=".jpg,.jpeg,.png,.svg"
                                id="brandLogoInput">
                            </label>
                          </div>
                        </div>
                      @else
                        <label class="form-label">Logo (opsional)</label>
                        <input type="file" name="brand_logo"
                          class="form-control form-dropzone @error('brand_logo') is-invalid @enderror"
                          accept=".jpg,.jpeg,.png,.svg">
                        @error('brand_logo')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      @endif
                    </div>
                  </div>
                </div>

                <div class="col-4">
                  <div class="mb-3">
                    <label class="form-label">Biaya Admin (opsional)</label>
                    <select name="fee_type" id="fee_type" class="form-select">
                      @php $feeType = old('fee_type', $paymentMethod->fee_type ?? 'NONE'); @endphp
                      @foreach (PaymentMethodFeeType::cases() as $type)
                        <option value="{{ $type->value }}" {{ $feeType === $type ? 'selected' : '' }}>
                          {{ $type->label() }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <div class="mb-3" id="feeAmountGroup">
                    <label class="form-label">Nilai Biaya</label>
                    <div class="input-group">
                      <span class="input-group-text" id="feeAmountPrefix">Rp</span>
                      <input type="number" step="0.01" min="0" name="fee_amount"
                        value="{{ old('fee_amount', $paymentMethod->fee_amount) }}" class="form-control"
                        placeholder="Masukkan nilai biaya">
                      <span class="input-group-text d-none" id="feeAmountSuffix">%</span>
                    </div>
                    <small class="text-secondary" id="feeHelpText"></small>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Deskripsi (opsional)</label>
                  <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                    placeholder="Masukkan deskripsi channel pembayaran">{{ old('description') ?? $paymentMethod->description }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mt-4">
                  <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('js')
    <script>
      let originalBrandLogo;
      document.getElementById('brandLogoInput')?.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;

        const preview = document.getElementById('brandLogoPreview');
        if (!originalBrandLogo) {
          originalBrandLogo = preview.src;
        }

        let rd = new FileReader();
        rd.onload = function(e) {
          preview.src = e.target.result;
        }
        rd.readAsDataURL(event.target.files[0]);

        document.getElementById('brandLogoReset').classList.remove('d-none');
      });

      document.getElementById('brandLogoReset')?.addEventListener('click', function() {
        const preview = document.getElementById('brandLogoPreview');
        const input = document.getElementById('brandLogoInput');

        preview.src = originalBrandLogo;
        input.value = '';

        this.classList.add('d-none');
      });

      // Fee field logic
      const feeTypeEl = document.getElementById('fee_type');
      const feeAmountGroup = document.getElementById('feeAmountGroup');
      const feeAmountPrefix = document.getElementById('feeAmountPrefix');
      const feeAmountSuffix = document.getElementById('feeAmountSuffix');
      const feeHelpText = document.getElementById('feeHelpText');

      function syncFeeUI() {
        if (!feeTypeEl) return;
        const val = feeTypeEl.value;
        if (val === 'NONE') {
          feeAmountGroup.classList.add('d-none');
        } else {
          feeAmountGroup.classList.remove('d-none');
          if (val === 'PERCENT') {
            feeAmountPrefix.classList.add('d-none');
            feeAmountSuffix.classList.remove('d-none');
            feeHelpText.textContent = 'Masukkan persentase biaya contoh: 2.5 untuk 2,5%';
          } else {
            feeAmountPrefix.classList.remove('d-none');
            feeAmountSuffix.classList.add('d-none');
            feeHelpText.textContent = 'Masukkan nominal biaya dalam rupiah';
          }
        }
      }
      syncFeeUI();
      feeTypeEl?.addEventListener('change', syncFeeUI);
    </script>
  @endpush
</x-e-billing::layouts.panel>
