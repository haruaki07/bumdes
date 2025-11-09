{{-- Repayment Modal --}}
<div class="modal modal-blur fade" id="repaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('funding-requests.repayments.store', $fundingRequest) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Bayar Cicilan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-primary mb-3">
            <div class="row w-full">
              <div class="col-6">
                <div class="text-muted small">Total Pendanaan</div>
                <div class="h3 mb-0">Rp {{ number_format($fundingRequest->disbursed_amount, 0, ',', '.') }}</div>
              </div>
              <div class="col-6">
                <div class="text-muted small">Sisa Pembayaran</div>
                <div
                  class="h3 mb-0 text-{{ $fundingRequest->getRemainingAmountAttribute() > 0 ? 'danger' : 'success' }}">
                  Rp {{ number_format($fundingRequest->getRemainingAmountAttribute(), 0, ',', '.') }}
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label required">Tanggal Pembayaran</label>
              <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label required">Jumlah Pembayaran</label>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" name="amount" class="form-control" placeholder="0" data-mask-currency required>
              </div>
              <div class="form-hint">Maksimal Rp
                {{ number_format($fundingRequest->getRemainingAmountAttribute(), 0, ',', '.') }}</div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label required">Metode Pembayaran</label>
            <select name="payment_method" class="form-select" required>
              <option value="transfer">Transfer Bank</option>
              <option value="tunai">Tunai</option>
              <option value="lainnya">Lainnya</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label required">Bukti Pembayaran</label>
            <input type="file" name="proof_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
            <div class="form-hint">Unggah bukti transfer atau kuitansi pembayaran. Format: PDF, JPG, PNG | Maksimal 5MB
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Tambahkan catatan (opsional)">{{ old('notes') }}</textarea>
          </div>

          <div class="alert alert-warning">
            <div class="d-flex gap-2">
              <div class="alert-icon"><i class="ti ti-alert-triangle icon"></i></div>
              <div>
                <h4 class="alert-title">Penting</h4>
                <ul class="mb-0 alert-description">
                  <li>Pastikan bukti pembayaran jelas dan terbaca</li>
                  <li>Pembayaran akan diverifikasi oleh operator</li>
                  <li>Anda dapat melakukan pembayaran sebagian (cicilan) atau lunas sekaligus</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-orange">
            <i class="icon ti ti-coin"></i> Kirim Pembayaran
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
