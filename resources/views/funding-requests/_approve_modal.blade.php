{{-- Approve Modal --}}
<div class="modal modal-blur fade" id="approveModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('funding-requests.approve', $fundingRequest) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Setujui Pengajuan Pendanaan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">Bunga (% per tahun)</label>
            <input type="number" name="interest_rate" class="form-control" step="0.01" min="0" max="100"
              value="{{ old('interest_rate', 0) }}" required>
            <div class="form-hint">Masukkan 0 jika tidak ada bunga</div>
          </div>

          <div class="mb-3">
            <label class="form-label required">Durasi Pembayaran (Bulan)</label>
            <input type="number" name="repayment_duration_months" class="form-control" min="1" max="60"
              value="{{ old('repayment_duration_months', $fundingRequest->repayment_duration_months ?? 12) }}" required>
            <div class="form-hint">Jangka waktu pembayaran dalam bulan</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Tambahkan catatan untuk pemohon (opsional)">{{ old('notes') }}</textarea>
          </div>

          <div class="alert alert-info">
            <div class="d-flex">
              <div><i class="ti ti-info-circle icon alert-icon"></i></div>
              <div>
                Setelah disetujui, sistem akan mengirim notifikasi ke pemohon dan Anda perlu mengunggah dokumen MOU.
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">
            <i class="icon ti ti-check"></i> Setujui Pengajuan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
