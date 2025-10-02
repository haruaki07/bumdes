<x-e-billing::layouts.panel>
  @include('e-billing::settings.partials._header')

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')
      <div class="card overflow-hidden">
        <div class="row g-0">
          @include('e-billing::settings.partials._menu')

          <form action="{{ route('e-billing.settings.update', ['group' => $group]) }}" method="POST"
            class="col-12 col-md-9 d-flex flex-column">
            @csrf
            @method('PUT')
            <div class="card-body">
              <h2 class="mb-4">{{ $group->label() }}</h2>

              <h3 class="card-title">API Keys Xendit</h3>
              <p class="card-subtitle">
                Silahkan buka
                <a href="https://dashboard.xendit.co/settings/developers" class="link">di sini</a>
                untuk mendapatkan secret key dan webhook token
              </p>

              <div class="mb-3">
                <label class="form-label required">Secret key</label>
                <input type="text" name="secret" class="form-control @error('secret') is-invalid @enderror"
                  value="{{ old('secret') ?? $settings->secret }}" required placeholder="Masukkan secret key">
                <div id="passwordHelpBlock" class="form-text">
                  Secret key digunakan untuk keperluan pembayaran tagihan oleh pelanggan. <a href="#"
                    class="link" data-bs-toggle="modal" data-bs-target="#secretKeyGuideModal">Panduan</a>
                </div>
                @error('secret')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="mb-3">
                <label class="form-label required">Webhook token</label>
                <input type="text" name="webhook_token"
                  class="form-control @error('webhook_token') is-invalid @enderror"
                  value="{{ old('webhook_token') ?? $settings->webhook_token }}" required
                  placeholder="Masukkan webhook token">
                <div id="passwordHelpBlock" class="form-text">
                  Webhook token digunakan untuk keperluan verifikasi status pembayaran yang dilakukan oleh pelanggan. <a
                    href="#" class="link" data-bs-toggle="modal" data-bs-target="#webhookGuideModal">Panduan</a>
                </div>
                @error('webhook_token')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Webhook URL</label>
                <div class="input-group">
                  <input type="text" readonly value="{{ route('e-billing.webhooks.xendit') }}" class="form-control">
                  <button type="button" class="btn btn-icon btn-outline-secondary" type="button"
                    data-bs-toggle="tooltip" data-bs-placement="top" title="Salin URL"
                    onclick="navigator.clipboard.writeText('{{ route('e-billing.webhooks.xendit') }}')"><i
                      class="ti ti-copy ti-sm"></i></button>
                </div>
              </div>
            </div>
            <div class="card-footer bg-transparent mt-auto">
              <div class="btn-list justify-content-end">
                <button type="reset" class="btn"> Reset </button>
                <button type="submit" class="btn btn-primary"> Simpan </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal modal-lg fade" id="secretKeyGuideModal" tabindex="-1">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Panduan Membuat Secret Key</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ol>
            <li>Login ke dashboard Xendit.</li>
            <li>Masuk ke menu <strong>Settings</strong> &gt; <strong>Developers</strong>.</li>
            <li>
              Pada bagian <strong>API Keys</strong>, klik tombol <strong>Generate Secret Key</strong>.
              <img src="{{ asset('assets/e-billing/images/settings/xendit_secret_keys.png') }}" alt="Secret Key Page"
                class="img-fluid my-3" loading="lazy">
            </li>
            <li>
              Beri nama <strong>Secret Key</strong> pada kolom <strong>API key name</strong>, lalu sesuaikan
              <strong>Permission</strong> dengan minimal sebagai berikut:
              <img src="{{ asset('assets/e-billing/images/settings/xendit_secret_keys_generate.jpeg') }}"
                alt="Secret Key Generate" class="d-block w-50 my-3" loading="lazy">
            </li>
            <li>Setelah itu, klik tombol <strong>Generate Key</strong>.</li>
          </ol>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn ms-auto" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal modal-lg fade" id="webhookGuideModal" tabindex="-1">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Panduan Mendapatkan Webhook Token</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <h3>Langkah-langkah Mendapatkan Webhook Token:</h3>
          <ol>
            <li>Login ke dashboard Xendit.</li>
            <li>Masuk ke menu <strong>Settings</strong> &gt; <strong>Developers</strong>.</li>
            <li>
              Pada bagian <strong>API Keys</strong>, klik tombol <strong>View Webhook Verification Token</strong>.
              <img src="{{ asset('assets/e-billing/images/settings/xendit_webhook_token.jpeg') }}"
                alt="Webhook Token Page" class="img-fluid my-3" loading="lazy">
            </li>
            <li>Salin kode token.</li>
          </ol>

          <h3 class="mt-4">Mengatur Webhook URL:</h3>
          <ol>
            <li>Masih pada halaman yang sama.</li>
            <li>Pada bagian <strong>Webhook URL</strong>, cari <strong>Payment Request V3</strong>.</li>
            <li>
              Tambahkan URL webhook berikut pada <strong>Payment Status</strong>:
              <br />
              <code>{{ route('e-billing.webhooks.xendit') }}</code>
              <img src="{{ asset('assets/e-billing/images/settings/xendit_webhook_url.jpeg') }}" alt="Webhook URL"
                class="img-fluid my-3" loading="lazy">
            </li>
            <li>Setelah itu, klik tombol <strong>Test and Save</strong>.</li>
          </ol>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn ms-auto" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
