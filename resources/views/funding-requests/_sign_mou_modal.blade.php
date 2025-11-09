{{-- Sign MOU Modal --}}
<div class="modal modal-blur fade" id="signMouModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('funding-requests.sign-mou', $fundingRequest) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Tandatangani MOU</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          @if ($fundingRequest->mou_document)
            <div class="mb-3">
              <label class="form-label">Dokumen MOU dari BUMDes</label>
              <div class="card card-sm">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <span class="bg-primary text-white avatar me-3">
                      <i class="ti ti-file-text"></i>
                    </span>
                    <div class="flex-fill">
                      <div class="font-weight-medium">MOU_{{ $fundingRequest->id }}.pdf</div>
                      <div class="text-muted small">Diunggah oleh {{ $fundingRequest->mouUploadedBy->name }}</div>
                    </div>
                    <a href="{{ Storage::url($fundingRequest->mou_document) }}" target="_blank"
                      class="btn btn-ghost-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                      title="Unduh">
                      <i class="ti ti-download"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          @endif

          <div class="alert alert-warning mb-3">
            <div class="d-flex gap-2">
              <div class="alert-icon"><i class="icon ti ti-alert-triangle"></i></div>
              <div>
                <h4 class="alert-title">Langkah Penandatanganan</h4>
                <ol class="mb-0">
                  <li>Unduh dokumen MOU di atas</li>
                  <li>Cetak dokumen MOU</li>
                  <li>Tanda tangani dokumen</li>
                  <li>Bubuhkan materai Rp 10.000</li>
                  <li>Scan dokumen yang sudah ditandatangani</li>
                  <li>Unggah kembali dokumen scan di bawah ini</li>
                </ol>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label required">Dokumen MOU Bertanda Tangan + Materai</label>
            <input type="file" name="signature_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
            <div class="form-hint">Format: PDF, JPG, PNG | Maksimal 10MB</div>
          </div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="agreeTerms" required>
            <label class="form-check-label required" for="agreeTerms">
              Saya telah membaca, memahami, dan menyetujui semua syarat dan ketentuan yang tertera dalam MOU ini
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">
            <i class="icon ti ti-file-check"></i> Tandatangani & Unggah
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
