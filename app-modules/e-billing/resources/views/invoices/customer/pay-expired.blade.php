<div class="empty">
  <div class="empty-img">
    <img src="{{ asset('assets/images/misc/cat.webp') }}" width="180" alt="Transaction" />
  </div>
  <p class="empty-title">Pembayaran Gagal!</p>
  <p class="empty-subtitle text-secondary">
    Sesi telah kadaluarsa. Silakan coba lagi.
  </p>
  <div class="empty-action">
    <a href="{{ route('e-billing.invoice.customer-show', $invoice->customer->customer_id) }}"
      class="btn btn-outline-primary">
      <i class="ti ti-arrow-left icon"></i>
      Kembali ke Tagihan
    </a>
  </div>
</div>
