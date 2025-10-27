@php
  $provider = request('provider', $settings->provider ?? null);
  $canUpdate = auth('ebil')->user()->can('update-system-settings');
@endphp

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

              <h3 class="card-title">API Provider</h3>
              <p class="card-subtitle">Silakan pilih penyedia API yang akan digunakan untuk integrasi WhatsApp.</p>

              <div class="mb-3">
                <label class="form-check">
                  <input type="checkbox" name="enabled" value="1" class="form-check-input"
                    {{ old('enabled', $settings->enabled ?? false) ? 'checked' : '' }}
                    @if (!$canUpdate) onclick="return false;" @endif>
                  <span class="form-check-label">Aktif</span>
                </label>
              </div>

              <div class="mb-3">
                <label class="form-label required">API Provider</label>
                <select name="provider" class="form-select @error('provider') is-invalid @enderror" required
                  id="providerSelect" data-tom-select @if (!$canUpdate) disabled @endif>
                  <option value="">Pilih</option>
                  @foreach (['waha' => 'WAHA (Unofficial)', 'wablas' => 'Wablas.com (Unofficial)'] as $value => $label)
                    <option value="{{ $value }}" {{ old('provider', $provider) == $value ? 'selected' : '' }}>
                      {{ $label }}
                    </option>
                  @endforeach
                </select>
                @error('provider')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              @if ($provider === 'waha')
                <div id="wahaSettings">
                  <h3 class="card-title mt-4">Nomor WhatsApp Terhubung</h3>
                  <p class="card-subtitle">Sambungkan nomor Whatsapp Anda untuk mengirim notifikasi via WhatsApp.</p>

                  <div class="card p-4 col-md-6">
                    <div class="d-flex gap-3">
                      <span class="avatar avatar-lg rounded-circle bg-success-lt"><i
                          class="ti ti-brand-whatsapp"></i></span>
                      <div class="flex-fill">
                        <div class="card-title mb-1">
                          <span id="whatsappNumber"></span>
                          <span class="placeholder col-7" style="height:1.2em;display:inline-block"></span>
                        </div>
                        <span class="card-subtitle">default</span>
                      </div>
                      <div>
                        <span class="badge d-none" id="sessionStatusBadge">Loading...</span>
                        <div class="placeholder" style="width:75px;height:20px"></div>
                      </div>
                    </div>


                    @if ($canUpdate)
                      <div class="btn-list mt-4">
                        <span data-bs-toggle="tooltip" title="Hubungkan" data-bs-placement="top" id="btnConnect"
                          class="d-none">
                          <button type="button" class="btn btn-ghost-primary btn-icon" data-bs-toggle="modal"
                            data-bs-target="#connectWhatsappModal">
                            <i class="ti ti-qrcode"></i>
                          </button>
                        </span>
                        <button type="button" class="btn btn-ghost-secondary btn-icon" data-bs-toggle="tooltip"
                          title="Refresh" data-bs-placement="top" id="btnRefresh">
                          <i class="ti ti-refresh"></i>
                        </button>
                        <button type="button" class="btn btn-ghost-danger btn-icon d-none" data-bs-toggle="tooltip"
                          title="Logout" data-bs-placement="top" id="btnLogout">
                          <i class="ti ti-logout"></i>
                        </button>
                      </div>
                    @endif
                  </div>
                </div>
              @elseif ($provider === 'wablas')
                <div id="wablasSettings">
                  <h3 class="card-title mt-4">Menggunakan Wablas</h3>
                  <p class="card-subtitle">Pastikan anda memiliki akun Wablas beserta dengan device yang
                    sudah terhubung.</p>

                  <div class="mb-3">
                    <label class="form-label required">Server</label>
                    <div class="form-selectgroup">
                      @php
                        $servers = [
                            'solo' => 'Solo',
                            'pati' => 'Pati',
                            'kudus' => 'Kudus',
                            'jogja' => 'Jogja',
                            'jkt' => 'Jakarta',
                            'tegal' => 'Tegal',
                            'bdg' => 'Bandung',
                            'sby' => 'Surabaya',
                            'texas' => 'Texas',
                            'deu' => 'Germany',
                        ];
                      @endphp
                      @foreach ($servers as $key => $label)
                        <label class="form-selectgroup-item">
                          <input type="radio" name="wablas_server" value="{{ $key }}"
                            class="form-selectgroup-input"
                            {{ $key === old('wablas_server', $settings->wablas_server) ? 'checked' : '' }}
                            @if (!$canUpdate) onclick="return false;" @endif />
                          <span class="form-selectgroup-label">{{ $label }}</span>
                        </label>
                      @endforeach
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md">
                      <label class="form-label required">API Key/Token</label>
                      <input type="text" name="wablas_api_key"
                        class="form-control @error('wablas_api_key') is-invalid @enderror"
                        value="{{ old('wablas_api_key') ?? $settings->wablas_api_key }}" required
                        placeholder="Masukkan API key/token" @if (!$canUpdate) readonly @endif>
                      @error('wablas_api_key')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="col-md">
                      <label class="form-label required">Secret Key</label>
                      <input type="text" name="wablas_secret_key"
                        class="form-control @error('wablas_secret_key') is-invalid @enderror"
                        value="{{ old('wablas_secret_key') ?? $settings->wablas_secret_key }}" required
                        placeholder="Masukkan secret key" @if (!$canUpdate) readonly @endif>
                      @error('wablas_secret_key')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  @if ($canUpdate)
                    <button type="button" class="btn" id="btnCheckConnection" data-bs-toggle="loading-button"
                      data-bs-disabled-on-loading="true" data-bs-spinner-type="dots">Cek Koneksi</button>
                  @endif
                </div>
              @endif

              <hr class="my-4">
              <h3 class="card-title">Template Pesan Pengingat Invoice</h3>
              <p class="card-subtitle">Atur format pesan WhatsApp yang dikirim ke pelanggan saat pengingat tagihan.
                Gunakan placeholder di bawah ini. Hindari informasi sensitif.</p>

              <div class="row g-3">
                <div class="col-md-7">
                  <label class="form-label required">Template</label>
                  <textarea name="invoice_reminder_template" rows="6" class="form-control font-monospace" placeholder="Ketik..."
                    required @if (!$canUpdate) readonly @endif>{{ old('invoice_reminder_template', $settings->invoice_reminder_template ?? '') }}</textarea>
                  <small class="text-muted">Maks 500 karakter.</small>
                  @error('invoice_reminder_template')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror

                  <label class="form-label mt-3">Placeholder Tersedia</label>
                  <div class="table-responsive">
                    <table class="table table-sm table-hover">
                      <thead>
                        <tr>
                          <th>Placeholder</th>
                          <th>Deskripsi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><code>{customer_name}</code></td>
                          <td>Nama pelanggan</td>
                        </tr>
                        <tr>
                          <td><code>{invoice_number}</code></td>
                          <td>Nomor tagihan</td>
                        </tr>
                        <tr>
                          <td><code>{invoice_amount}</code></td>
                          <td>Jumlah (Rp100.000)</td>
                        </tr>
                        <tr>
                          <td><code>{invoice_due_date}</code></td>
                          <td>Tanggal jatuh tempo</td>
                        </tr>
                        <tr>
                          <td><code>{invoice_grace_period_end_date}</code></td>
                          <td>Akhir masa tenggang</td>
                        </tr>
                        <tr>
                          <td><code>{invoice_status}</code></td>
                          <td>Status tagihan</td>
                        </tr>
                        <tr>
                          <td><code>{invoice_public_url}</code></td>
                          <td>Link pembayaran tagihan</td>
                        </tr>
                        <tr>
                          <td><code>{package_name}</code></td>
                          <td>Nama paket</td>
                        </tr>
                        <tr>
                          <td><code>{business_name}</code></td>
                          <td>Nama perusahaan</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-md-5">
                  <label class="form-label">Preview</label>
                  <div id="waTemplatePreview" class="border rounded p-2 bg-success-lt small"
                    style="white-space:pre-wrap; min-height:36px; font-family: system-ui, sans-serif;">
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer bg-transparent mt-auto">
              <div class="btn-list justify-content-end">
                <button type="reset" class="btn"
                  @if (!$canUpdate) style="pointer-events: none; visibility: hidden;" @endif> Reset
                </button>
                <button type="submit" class="btn btn-primary"
                  @if (!$canUpdate) style="pointer-events: none; visibility: hidden;" @endif> Simpan
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @if ($provider === 'waha')
    <div class="modal fade" id="connectWhatsappModal" tabindex="-1">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Hubungkan WhatsApp</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="accordion" id="connectWhatsappAccordion">
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingQr">
                  <button class="accordion-button py-2 px-3 fs-4" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseQr" aria-expanded="true" aria-controls="collapseQr">
                    Scan QR
                  </button>
                </h2>
                <div id="collapseQr" class="accordion-collapse collapse show" aria-labelledby="headingQr"
                  data-bs-parent="#connectWhatsappAccordion">
                  <div class="accordion-body px-3">
                    <div class="text-muted mb-2">Scan Kode QR untuk menghubungkan nomor WhatsApp Anda.</div>

                    <ol>
                      <li>Buka WhatsApp di handphone Anda</li>
                      <li>
                        Tekan tanda <strong>titik tiga <i class="ti ti-sm ti-dots-vertical"></i></strong>
                        atau
                        <strong>pengaturan <i class="ti ti-sm ti-settings"></i></strong>
                      </li>
                      <li>Tekan <strong>Perangkat Tertaut (<em>Linked Devices</em>)</strong> dan <strong>Tautkan
                          perangkat</strong></li>
                      <li>Scan kode QR di bawah ini</li>
                    </ol>

                    <div class="d-flex justify-content-center mb-2 mt-4">
                      <button type="button" class="btn btn-action" id="btnRefreshQr">
                        <i class="ti ti-reload"></i>
                      </button>
                    </div>
                    <div
                      class="ratio ratio-1x1 border rounded bg-light d-flex align-items-center justify-content-center overflow-hidden"
                      style="max-width:300px; margin:auto; position:relative;">
                      <div id="qrLoading" class="d-flex flex-column align-items-center justify-content-center gap-2">
                        <div class="spinner-border text-primary" role="status" aria-label="Loading QR"></div>
                        <div class="small mt-2">Memuat QR...</div>
                      </div>
                      <img id="qrImage" alt="QR Code" style="max-width:100%; max-height:100%; display:none;" />
                    </div>
                    <small id="qrError" class="text-danger d-none">Gagal memuat QR. Silakan coba lagi.</small>
                  </div>
                </div>
              </div>

              <div class="accordion-item">
                <h2 class="accordion-header" id="headingCode">
                  <button class="accordion-button collapsed py-2 px-3 fs-4" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseCode" aria-expanded="false" aria-controls="collapseCode">
                    Masukkan Kode
                  </button>
                </h2>
                <div id="collapseCode" class="accordion-collapse collapse" aria-labelledby="headingCode"
                  data-bs-parent="#connectWhatsappAccordion">
                  <div class="accordion-body px-3">
                    <div class="text-muted mb-2">Gunakan kode untuk menghubungkan nomor WhatsApp Anda.</div>

                    <ol>
                      <li>Buka WhatsApp di handphone Anda</li>
                      <li>
                        Tekan tanda <strong>titik tiga <i class="ti ti-sm ti-dots-vertical"></i></strong>
                        atau
                        <strong>pengaturan <i class="ti ti-sm ti-settings"></i></strong>
                      </li>
                      <li>Tekan <strong>Perangkat Tertaut (<em>Linked Devices</em>)</strong> dan <strong>Tautkan
                          perangkat</strong></li>
                      <li>Tautkan dengan nomor telepon dan masukkan kode</li>
                    </ol>
                    <div class="mb-3 mt-4">
                      <p>Masukkan nomor WhatsApp Anda</p>
                      <div class="d-flex gap-3">
                        <div class="flex-fill">
                          <input type="text" id="phoneNumberInput" class="form-control"
                            placeholder="62xxxxxxxxxxx">
                        </div>
                        <div class="flex-shrink-0">
                          <button type="button" class="btn btn-primary" id="btnRequestCode">
                            <i class="ti ti-send icon"></i>
                            Kirim Kode
                          </button>
                        </div>
                      </div>
                    </div>
                    <div class="mb-3 d-none" id="authCodeWrapper">
                      <div class="alert alert-info justify-content-center mb-1">
                        <span id="authCode" class="font-monospace fs-1">XXXX-XXXX</span>
                      </div>
                      <span class="small text-muted">Masukkan kode di atas, kode akan hangus dalam 20 detik.</span>
                    </div>
                  </div>
                </div>
              </div>
            </div> <!-- /accordion -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn ms-auto" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  @endif

  @push('js')
    <script type="module">
      const providerSelect = document.getElementById('providerSelect');

      const templateTextarea = document.querySelector('textarea[name="invoice_reminder_template"]');
      const previewBox = document.getElementById('waTemplatePreview');

      providerSelect?.addEventListener('change', function() {
        const selectedProvider = this.value;
        window.location.href = '{{ route('e-billing.settings.show', ['group' => 'whatsapp']) }}' + (selectedProvider ?
          '?provider=' + selectedProvider : '');
      });

      const sampleData = {
        '{customer_name}': 'Budi',
        '{invoice_number}': 'INV2025090001',
        '{invoice_amount}': 'Rp150.000',
        '{invoice_due_date}': '10 Sep 2025',
        '{invoice_grace_period_end_date}': '15 Sep 2025',
        '{invoice_status}': 'UNPAID',
        '{invoice_public_url}': 'https://contoh.test/invoice/INV2025090001',
        '{package_name}': 'Paket 10Mbps',
        '{business_name}': 'BUMDes Internet'
      };

      function renderPreview() {
        if (!templateTextarea || !previewBox) return;
        let text = templateTextarea.value || '';
        Object.entries(sampleData).forEach(([k, v]) => {
          text = text.split(k).join(v);
        });
        if (!text.trim()) {
          previewBox.innerHTML = '';
        } else {
          previewBox.textContent = text;
        }
      }
      templateTextarea?.addEventListener('input', renderPreview);
      renderPreview();

      document.querySelectorAll('[data-tom-select]').forEach(select => {
        new TomSelect(select, {
          maxItems: 1
        });
      });
    </script>

    @if ($provider === 'waha')
      <script type="module">
        const can = {
          update: {{ $canUpdate ? 'true' : 'false' }}
        };


        const providerSelect = document.querySelector('select[name="provider"]');
        const wahaBox = document.getElementById('wahaSettings');
        const whatsappNumber = document.getElementById('whatsappNumber');
        const statusBadge = document.getElementById('sessionStatusBadge');
        const btnConnect = document.getElementById('btnConnect');
        const btnRefresh = document.getElementById('btnRefresh');
        const btnLogout = document.getElementById('btnLogout');

        const btnRefreshQr = document.getElementById('btnRefreshQr');
        const imgQr = document.getElementById('qrImage');
        const qrLoading = document.getElementById('qrLoading');
        const qrError = document.getElementById('qrError');

        const btnRequestCode = document.getElementById('btnRequestCode');
        const phoneNumberInput = document.getElementById('phoneNumberInput');
        const authCodeWrapper = document.getElementById('authCodeWrapper');
        const authCode = document.getElementById('authCode');

        function toggle() {
          const p = providerSelect.value;
          wahaBox.style.display = p === 'waha' ? '' : 'none';
        }
        providerSelect?.addEventListener('change', toggle);
        toggle();

        function setStatus(status) {
          if (!can.update) return;

          let connected = false;
          let label = "-";
          let color = "secondary";

          switch (status) {
            case 'WORKING':
              connected = true;
              label = 'Terhubung';
              color = 'success';
              break;
            case 'STARTING':
            case 'SCAN_QR_CODE':
            case 'STOPPED':
            case 'FAILED':
              label = 'Tidak Terhubung';
              color = 'danger';
              break;
          }

          statusBadge.className = `badge bg-${color}-lt text-${color}-lt-fg`;
          statusBadge.textContent = label;
          if (connected) {
            btnLogout.classList.toggle('d-none', false);
            btnConnect.classList.toggle('d-none', true);
          } else {
            btnLogout.classList.toggle('d-none', true);
            btnConnect.classList.toggle('d-none', false);
          }
        }

        async function refreshStatus() {
          try {
            const res = await fetch('{{ route('e-billing.whatsapp.waha.status') }}');
            const data = await res.json();
            setStatus(data.status || data.state || data.session?.status);
            document.querySelectorAll('.placeholder').forEach(el => el.style.display = 'none');
            if (data.status === 'WORKING') {
              whatsappNumber.textContent = data.me?.id?.split("@")?.[0] || '-';
            } else {
              whatsappNumber.textContent = '-';
            }
            return data;
          } catch (e) {
            setStatus('FAILED');
          }
        }

        btnRefresh?.addEventListener('click', refreshStatus);
        btnLogout?.addEventListener('click', async () => {
          try {
            const confirmed = await new Promise((resolve) => {
              bootbox.confirm({
                title: 'Konfirmasi Logout',
                message: 'Apakah Anda yakin ingin logout dari WhatsApp? Anda perlu menghubungkan ulang dengan memindai QR atau kode.',
                buttons: {
                  cancel: {
                    className: 'btn',
                    label: 'Batal'
                  },
                  confirm: {
                    className: 'btn-danger',
                    label: 'Ya, logout'
                  }
                },
                callback: function(result) {
                  resolve(result);
                }
              });
            })

            if (confirmed) {
              await fetch('{{ route('e-billing.whatsapp.waha.logout') }}', {
                method: 'POST'
              });
              await refreshStatus();
            }
          } catch (e) {
            alert('Gagal logout: ' + e.message);
          }
        });

        function loadQr() {
          if (!imgQr) return;
          // reset states
          qrError?.classList.add('d-none');
          imgQr.style.display = 'none';
          qrLoading?.classList.remove('d-none');

          // build URL with cache-buster; include optional params() if available
          const base = '{{ route('e-billing.whatsapp.waha.qr') }}';
          let url = base;
          try {
            const u = new URL(base, window.location.origin);
            if (typeof params === 'function') {
              const extra = params() || {};
              Object.entries(extra).forEach(([k, v]) => {
                if (v != null && v !== '') u.searchParams.set(k, v);
              });
            }
            u.searchParams.set('_t', Date.now());
            url = u.toString();
          } catch (e) {
            // fallback: append cache-buster
            url = base + '?_t=' + Date.now();
          }

          imgQr.onload = () => {
            qrLoading?.classList.add('d-none');
            imgQr.style.display = '';
            qrError?.classList.add('d-none');
          };
          imgQr.onerror = () => {
            qrLoading?.classList.add('d-none');
            imgQr.style.display = 'none';
            qrError?.classList.remove('d-none');
          };

          imgQr.src = url;
        }

        btnRefreshQr?.addEventListener('click', throttle(loadQr, 300));

        const connectModalEl = document.getElementById('connectWhatsappModal');
        connectModalEl?.addEventListener('show.bs.modal', () => {
          // Auto-fetch QR on modal show
          loadQr();
        });
        connectModalEl?.addEventListener('hidden.bs.modal', () => {
          // Optional reset
          if (imgQr) imgQr.style.display = 'none';
          qrLoading?.classList.add('d-none');
          qrError?.classList.add('d-none');
        });

        let authCodeInterval = null;

        function startAuthCodeCountdown(seconds) {
          let countdown = seconds - 1; // start from seconds-1 since we show immediately
          authCodeWrapper.querySelector('span.small').textContent =
            `Masukkan kode di atas, kode akan hangus dalam ${seconds} detik.`;
          authCodeInterval = setInterval(() => {
            authCodeWrapper.querySelector('span.small').textContent =
              `Masukkan kode di atas, kode akan hangus dalam ${countdown} detik.`;
            countdown--;
            if (countdown < 0) {
              clearInterval(authCodeInterval);
              authCodeWrapper.classList.add('d-none');
            }
          }, 1000);
        }

        btnRequestCode?.addEventListener('click', async () => {
          try {

            if (!phoneNumberInput.value) {
              setConnectAlert('Silakan masukkan nomor WhatsApp terlebih dahulu.', 'danger');
              return;
            }

            authCodeInterval && clearInterval(authCodeInterval);
            authCodeWrapper.classList.add('d-none');

            btnRequestCode.disabled = true;
            const body = {
              phoneNumber: phoneNumberInput.value
            };
            const res = await fetch('{{ route('e-billing.whatsapp.waha.request-code') }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify(body)
            });

            if (!res.ok) {
              const errorData = await res.json().catch(() => ({}));
              throw new Error(errorData.message || 'Request failed with status ' + res.status);
            }

            const data = await res.json();
            authCode.textContent = data.code;
            authCodeWrapper.classList.remove('d-none');
            startAuthCodeCountdown(20);
          } catch (e) {
            setConnectAlert('Gagal mengirim kode: ' + e.message, 'danger');
          } finally {
            btnRequestCode.disabled = false;
          }
        });

        function setConnectAlert(message, type = 'info') {
          const alertContainer = document.querySelector('#connectWhatsappModal .modal-body');
          alertContainer.insertAdjacentHTML('afterbegin', `
          <div class="alert alert-important alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        `);
        };

        refreshStatus();
      </script>
    @elseif ($provider === 'wablas')
      <script type="module">
        const btnCheckConnection = document.getElementById('btnCheckConnection');

        btnCheckConnection?.addEventListener('click', async () => {
          try {
            btnCheckConnection._loadingButtonInstance.start();
            const creds = {
              server: document.querySelector('input[name="wablas_server"]:checked')?.value,
              api_key: document.querySelector('input[name="wablas_api_key"]').value,
              secret_key: document.querySelector('input[name="wablas_secret_key"]').value,
            }
            if (!creds.server || creds.api_key.trim() === '' || creds.secret_key.trim() === '') {
              throw new Error('Silakan lengkapi semua kredensial terlebih dahulu.');
            }

            const res = await fetch('{{ route('e-billing.settings.whatsapp.device-info') }}?' + new URLSearchParams(
              creds), {
              headers: {
                'Accept': 'application/json'
              }
            });
            if (!res.ok) {
              const errorData = await res.json().catch(() => ({}));
              throw new Error(errorData.message || 'Request failed with status ' + res.status);
            }
            const data = await res.json();

            const infoHtml = `
  <div class="card shadow-none">
    <div class="card-body">
      <h3 class="card-title">Informasi Device</h3>
      <dl class="row mb-0">
        <dt class="col-sm-4">Nama Device</dt>
        <dd class="col-sm-8">${data.name || '-'}</dd>

        <dt class="col-sm-4">Serial</dt>
        <dd class="col-sm-8">${data.serial || '-'}</dd>

        <dt class="col-sm-4">Nomor HP</dt>
        <dd class="col-sm-8">${data.sender || '-'}</dd>

        <dt class="col-sm-4">Aktif</dt>
        <dd class="col-sm-8">
          ${data.active
            ? '<span class="badge bg-primary-lt">Aktif</span>'
            : '<span class="badge bg-muted-lt">Tidak Aktif</span>'}
        </dd>

        <dt class="col-sm-4">Status</dt>
        <dd class="col-sm-8">
          ${data.status === "connected"
            ? `<span class="badge bg-success-lt">Terhubung</span>`
            : `<span class="badge bg-danger-lt">Tidak terhubung</span>`}
        </dd>

        <dt class="col-sm-4">Quota</dt>
        <dd class="col-sm-8">${data.quota ?? 0} pesan</dd>

        <dt class="col-sm-4">Tanggal Kadaluarsa</dt>
        <dd class="col-sm-8">${data.expired_date || '-'}</dd>
      </dl>
    </div>
  </div>
`;

            bootbox.alert({
              title: 'Informasi Device Wablas',
              message: infoHtml,
              size: 'large',
              buttons: {
                ok: {
                  label: 'Tutup',
                  className: 'btn-primary'
                }
              }
            });
          } catch (e) {
            bootbox.alert({
              title: 'Cek Koneksi Gagal',
              message: e.message,
              buttons: {
                ok: {
                  label: 'Tutup',
                  className: 'btn-primary'
                }
              }
            });
          } finally {
            btnCheckConnection._loadingButtonInstance.stop();
          }
        });
      </script>
    @endif
  @endpush
</x-e-billing::layouts.panel>
