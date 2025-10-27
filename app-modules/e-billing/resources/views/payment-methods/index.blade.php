@php
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
            Metode Pembayaran
          </h2>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')
      <div class="row row-deck row-cards">
        <div class="col-12">
          <x-datatable tableId="paymentMethodsTable" title="Daftar Metode Pembayaran" :data="$paymentMethods">
            <x-slot:thead>
              <tr>
                <th>#</th>
                <x-sortable-header field="name" label="Nama" />
                <x-sortable-header field="code" label="Kode" />
                <x-sortable-header field="type" label="Jenis Pembayaran" />
                <th>Biaya Admin</th>
                <x-sortable-header field="is_active" label="Status" />
                <th>Aksi</th>
              </tr>
            </x-slot>

            <x-slot:tbody>
              @forelse ($paymentMethods as $paymentMethod)
                <tr>
                  <td>{{ $loop->iteration + $paymentMethods->firstItem() - 1 }}</td>
                  <td class="fw-medium">
                    <div class="d-flex align-items-center gap-3">
                      {{ $paymentMethod->name }}
                      @if ($paymentMethod->brand_logo)
                        <img src="{{ asset($paymentMethod->brand_logo) }}" alt="{{ $paymentMethod->name }}"
                          class="ms-2" style="height: 24px;">
                      @endif
                    </div>
                  </td>
                  <td>{{ $paymentMethod->code }}</td>
                  <td><x-common.badge :label="$paymentMethod->type->value" :color="$paymentMethod->type->color()" /></td>
                  <td>
                    @if ($paymentMethod->fee_type === PaymentMethodFeeType::FIXED)
                      Rp{{ number_format($paymentMethod->fee_amount, 0, ',', '.') }}
                    @elseif ($paymentMethod->fee_type === PaymentMethodFeeType::PERCENT)
                      {{ rtrim(rtrim(number_format($paymentMethod->fee_amount, 2, '.', ''), '0'), '.') }}%
                    @else
                      -
                    @endif
                  </td>
                  <td>
                    <form method="POST"
                      action="{{ route('e-billing.settings.payment-methods.update-status', $paymentMethod) }}"
                      id="statusForm{{ $paymentMethod->id }}">
                      @csrf
                      @method('PATCH')
                      <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active"
                          {{ $paymentMethod->is_active ? 'checked' : '' }}
                          @if (auth('ebil')->user()->cannot('update-payment-methods')) onclick="return false;" @endif />
                        <span class="form-check-label">{{ $paymentMethod->is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
                      </label>
                    </form>
                  </td>
                  <td>
                    <a href="{{ route('e-billing.settings.payment-methods.show', $paymentMethod) }}"
                      class="btn btn-icon btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                      title="Lihat detail">
                      <i class="ti ti-eye"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center">Tidak ada metode pembayaran tersedia.</td>
                </tr>
              @endforelse
            </x-slot>
          </x-datatable>
        </div>
      </div>
    </div>
  </div>

  @push('js')
    <script>
      let submitting = false;
      document.querySelectorAll('[id^="statusForm"]').forEach(form => {
        const checkbox = form.querySelector('input[name="is_active"]');
        checkbox.addEventListener('change', () => {
          if (submitting) return;
          submitting = true;
          form.submit();
          checkbox.disabled = true;
        });
      });
    </script>
  @endpush

</x-e-billing::layouts.panel>
