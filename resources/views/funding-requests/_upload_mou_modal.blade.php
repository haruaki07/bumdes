{{-- Upload MOU Modal --}}
<div class="modal modal-blur fade" id="uploadMouModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('funding-requests.upload-mou', $fundingRequest) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Unggah Dokumen MOU</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">Dokumen MOU (PDF)</label>
            <input type="file" name="mou_document" class="form-control" accept=".pdf" required>
            <div class="form-hint">Format: PDF, Maksimal 10MB</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Tambahkan catatan untuk pemohon (opsional)">{{ old('notes') }}</textarea>
          </div>

          <div class="alert alert-info">
            <div class="d-flex gap-2">
              <div class="alert-icon"><i class="icon ti ti-info-circle"></i></div>
              <div>
                <h4 class="alert-title">Panduan MOU</h4>
                <ul class="mb-0 alert-description">
                  <li>Pastikan MOU sudah ditandatangani oleh pihak BUMDes</li>
                  <li>MOU harus berisi syarat dan ketentuan yang jelas</li>
                  <li>Pemohon akan menerima notifikasi untuk menandatangani dan mengunggah kembali</li>
                  <li>Pemohon harus membubuhkan materai pada dokumen</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <i class="icon ti ti-file-upload"></i> Unggah MOU
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
