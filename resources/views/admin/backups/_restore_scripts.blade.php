@push('js')
  <script>
    function confirmRestore(backup) {
      bootbox.dialog({
        title: 'Terms Confirmation',
        message: `
          <form action="/backups/${backup.id}/restore" method="POST" id="confirmForm">
            @csrf
            <div class="alert alert-warning" role="alert">
              <div class="d-flex gap-2">
                <div class="alert-icon">
                  <i class="ti ti-alert-triangle"></i>
                </div>
                <div>
                  <h4 class="alert-title">Penting!</h4>
                  <div class="alert-description">
                    Tindakan ini akan menimpa data yang ada dengan data dari backup ini. Tindakan ini tidak dapat dibatalkan.
                  </div>
                </div>
              </div>
            </div>

            <dl class="row">
              <dt class="col-4" style="height: 32px;">Nama:</dt>
              <dd class="col-8">${backup.name}</dd>

              <dt class="col-4" style="height: 32px;">Tipe:</dt>
              <dd class="col-8">
                <div class="badge bg-${backup.type.color}-lt text-${backup.type.color}-lt-fg">
                  <i class="icon ti ti-sm ti-${backup.type.icon}"></i>
                  ${backup.type.label}
                </div>
              </dd>

              <dt class="col-4" style="height: 32px;">Ukuran:</dt>
              <dd class="col-8">${backup.file_size_formatted}</dd>

              <dt class="col-4" style="height: 32px;">Tanggal backup:</dt>
              <dd class="col-8">${backup.completed_at}</dd>
            </dl>
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="agreeCheckbox" required name="confirm">
              <label class="form-check-label" for="agreeCheckbox">
                Saya mengerti konsekuensi dari tindakan ini dan ingin melanjutkan.
              </label>
            </div>
          </form>`,
        buttons: {
          cancel: {
            label: 'Batal',
            className: 'btn-secondary',
            callback: function() {
              bootbox.hideAll();
            }
          },
          confirm: {
            label: 'Pulihkan Backup',
            className: 'btn-warning',
            callback: function() {
              const form = document.getElementById('confirmForm');
              if (!form.checkValidity()) {
                form.reportValidity(); // triggers built-in browser validation popup
                return false; // prevent modal from closing
              }
              form.submit();
            }
          }
        }
      });
    }
  </script>
@endpush
