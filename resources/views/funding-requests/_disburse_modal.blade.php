{{-- Disburse Modal --}}
<div class="modal modal-blur fade" id="disburseModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('funding-requests.disburse', $fundingRequest) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Pencairan Dana</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label required">Tanggal Pencairan</label>
              <input type="date" name="disbursement_date" class="form-control" max="{{ date('Y-m-d') }}"
                value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label required">Jumlah Dicairkan</label>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" name="amount" class="form-control" value="{{ $fundingRequest->amount }}" required
                  data-mask-currency />
              </div>
              <div class="form-hint">Maksimal Rp {{ number_format($fundingRequest->amount, 0, ',', '.') }}</div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label required">Metode Pembayaran</label>
            <select name="payment_method" class="form-select" id="paymentMethod" required>
              <option value="transfer">Transfer Bank</option>
              <option value="tunai">Tunai</option>
            </select>
          </div>

          <div id="bankDetails">
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">Nama Bank</label>
                <input type="text" name="bank_name" class="form-control" placeholder="Bank BCA">
              </div>

              <div class="col-md-4 mb-3">
                <label class="form-label">Nomor Rekening</label>
                <input type="text" name="account_number" class="form-control" placeholder="1234567890">
              </div>

              <div class="col-md-4 mb-3">
                <label class="form-label">Atas Nama</label>
                <input type="text" name="account_holder_name" class="form-control"
                  value="{{ $fundingRequest->user->name }}" placeholder="Nama Penerima">
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Bukti Pencairan (Opsional)</label>
            <input type="file" name="proof_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            <div class="form-hint">Format: PDF, JPG, PNG | Maksimal 5MB</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Tambahkan catatan pencairan (opsional)">{{ old('notes') }}</textarea>
          </div>

          <div class="alert alert-info">
            <div class="d-flex gap-2">
              <div class="alert-icon"><i class="ti ti-info-circle icon"></i></div>
              <div>
                Setelah dicairkan, pemohon akan menerima notifikasi dan dapat mulai melakukan pembayaran cicilan.
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-purple">
            <i class="icon ti ti-cash"></i> Cairkan Dana
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
  <script>
    document.getElementById('paymentMethod').addEventListener('change', function() {
      const bankDetails = document.getElementById('bankDetails');
      const inputs = bankDetails.querySelectorAll('input');

      if (this.value === 'transfer') {
        bankDetails.style.display = 'block';
        inputs.forEach(input => input.setAttribute('required', 'required'));
      } else {
        bankDetails.style.display = 'none';
        inputs.forEach(input => input.removeAttribute('required'));
      }
    });
  </script>
@endpush
