{{-- Reject Modal --}}
<div class="modal modal-blur fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('funding-requests.reject', $fundingRequest) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Tolak Pengajuan Pendanaan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">Alasan Penolakan</label>
            <textarea name="rejection_reason" class="form-control" rows="4" required
              placeholder="Jelaskan alasan penolakan secara detail...">{{ old('rejection_reason') }}</textarea>
            <div class="form-hint">Minimal 10 karakter. Alasan akan dikirim ke pemohon.</div>
          </div>

          <div class="alert alert-danger">
            <div class="d-flex">
              <div><i class="ti ti-alert-circle icon alert-icon"></i></div>
              <div>
                <h4 class="alert-title">Perhatian</h4>
                Pengajuan yang ditolak tidak dapat diubah lagi. Pemohon dapat mengajukan kembali dengan perbaikan.
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">
            <i class="icon ti ti-x"></i> Tolak Pengajuan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
