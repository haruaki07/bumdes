<div class="empty">
  <div class="empty-img">
    <img src="{{ asset('assets/images/misc/payment.webp') }}" width="180" alt="Transaction" />
  </div>
  <p class="empty-title">Terima Kasih!</p>
  <p class="empty-subtitle text-secondary">
    Tagihan Anda dengan nomor {{ $invoice->invoice_number }} telah berhasil dibayar.
  </p>
  <table style="text-align: left;" class="fs-3 my-4">
    <tbody>
      <tr>
        <td width="200" class="fw-bold pb-4">Jumlah yang Dibayarkan</td>
        <td class="pb-4">
          Rp{{ number_format($invoice->amount + $invoice->paymentMethod->calculateFee($invoice->amount), 0, ',', '.') }}
        </td>
      </tr>
      <tr>
        <td class="fw-bold pb-4">Tanggal Dibayar</td>
        <td class="pb-4">
          {{ $invoice->paid_at ? $invoice->paid_at->locale('id')->format('j F Y') : '-' }}
        </td>
      </tr>
      <tr>
        <td class="fw-bold">Metode Pembayaran</td>
        <td>
          @if ($invoice->paymentMethod->brand_logo)
            <span class="payment payment-xs me-1"
              style="background-image:url('{{ asset($invoice->paymentMethod->brand_logo) }}');"></span>
          @endif
          {{ $invoice->paymentMethod->name }}
        </td>
      </tr>
    </tbody>
  </table>
  <div class="empty-action mt-3">
    <a href="{{ route('e-billing.invoice.customer-show', $invoice->invoice_number) }}" class="btn btn-outline-primary">
      <i class="ti ti-arrow-left icon"></i>
      Kembali ke Tagihan
    </a>
  </div>
</div>
