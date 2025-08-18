<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Data Master
          </div>
          <h2 class="page-title">
            Tambah Pelanggan Baru
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a href="{{ route('e-billing.master-data.customers.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')
      <form action="{{ route('e-billing.master-data.customers.update', $customer->id) }}" method="POST">
        <div class="row row-deck row-cards">
          @csrf
          @method('PUT')
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Data Diri Pelanggan</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">ID Pelanggan</label>
                    <input type="text" name="customer_id"
                      class="form-control @error('customer_id') is-invalid @enderror"
                      value="{{ old('customer_id') ?? $customer->customer_id }}" required
                      placeholder="12345@wificlp.id">
                    @error('customer_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Nama Pelanggan</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                      value="{{ old('name') ?? $customer->name }}" required placeholder="Masukkan nama lengkap...">
                    @error('name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label required">Email</label>
                  <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') ?? $customer->email }}" required placeholder="Masukkan email...">
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label required">Nomor HP</label>
                  <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone') ?? $customer->phone }}" required placeholder="08512345">
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label required">Site</label>
                  <select name="site_id" class="form-select @error('site_id') is-invalid @enderror" required
                    data-tom-select>
                    <option value="">Pilih Site</option>
                    @foreach ($sites as $site)
                      <option value="{{ $site->id }}"
                        {{ old('site_id') ?? $customer->site_id == $site->id ? 'selected' : '' }}>
                        {{ $site->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('site_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div>
                  <label class="form-label required">Alamat Pelanggan</label>
                  @php $oldAddress = old('address') ?? $customer->address; @endphp
                  <div id="addressDisplay" class="text-muted mb-2"
                    @if (!$oldAddress) style="display:none;" @endif>
                    {{ $oldAddress }}
                  </div>
                  <button type="button" class="btn" data-bs-toggle="modal"
                    data-bs-target="#addressModal">Ganti</button>

                  <input style="opacity:0;height:1px;display:block;pointer-events:none;" tabindex="-1" name="address"
                    id="addressInput" value="{{ old('address') ?? $customer->address }}" required>
                  <input type="hidden" name="latitude" id="latitudeInput"
                    value="{{ old('latitude') ?? $customer->latitude }}">
                  <input type="hidden" name="longitude" id="longitudeInput"
                    value="{{ old('longitude') ?? $customer->longitude }}">

                  @error('address')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                  @error('latitude')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                  @error('longitude')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>

                <address-modal
                  api-key="{{ config('services.google.maps_api_key') ?? 'AIzaSyCw0GxwYh8kT-pVOYwoh33l0oXMgChS63A' }}"
                  default-lat="-7.72738" default-lng="109.0078" address="{{ old('address') ?? $customer->address }}"
                  lat="{{ old('latitude') ?? $customer->latitude }}"
                  lng="{{ old('longitude') ?? $customer->longitude }}"></address-modal>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Data Langganan/Paket</h3>
              </div>
              <div class="card-body">

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Paket</label>
                    <select name="package_id" id="packageSelect"
                      class="form-select @error('package_id') is-invalid @enderror" required data-tom-select>
                      <option value="">Pilih Paket</option>
                      @foreach ($packages as $package)
                        <option value="{{ $package->id }}"
                          data-price="Rp{{ number_format($package->price, 0, ',', '.') }}"
                          data-bandwidth="{{ $package->bandwidth }}" data-due="{{ $package->due }}"
                          {{ old('package_id') ?? $customer->package_id == $package->id ? 'selected' : '' }}>
                          {{ $package->name }}
                        </option>
                      @endforeach
                    </select>
                    @error('package_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Bandwidth</label>
                    <input id="bandwidthInput" class="form-control" disabled>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Price</label>
                    <input id="priceInput" class="form-control" disabled>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Tanggal jatuh tempo</label>
                    <input type="number" name="due" id="dueInput"
                      class="form-control @error('due') is-invalid @enderror" required
                      value="{{ old('due') ?? $customer->due }}">
                    <div class="form-text">
                      Tanggal jatuh tempo dapat diubah jika diperlukan. Isi dengan angka 1-31.
                    </div>
                    @error('due')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Data Perangkat</h3>
              </div>
              <div class="card-body">

                <div class="mb-3">
                  <label class="form-label required">Perangkat</label>
                  <select name="device_id" class="form-select @error('device_id') is-invalid @enderror" required
                    data-tom-select>
                    <option value="">Pilih Perangkat</option>
                    @foreach ($devices as $device)
                      <option value="{{ $device->id }}"
                        {{ old('device_id') ?? $customer->device_id == $device->id ? 'selected' : '' }}>
                        {{ $device->brand }} - {{ $device->model }}
                      </option>
                    @endforeach
                  </select>
                  @error('device_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Serial Number (Opsional)</label>
                    <input type="text" name="serial_number"
                      class="form-control @error('serial_number') is-invalid @enderror"
                      value="{{ old('serial_number') ?? $customer->serial_number }}">
                    @error('serial_number')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">MAC Address (Opsional)</label>
                    <input type="text" name="mac_address"
                      class="form-control @error('mac_address') is-invalid @enderror"
                      value="{{ old('mac_address') ?? $customer->mac_address }}" placeholder="00:1A:2B:3C:4D:5E"
                      pattern="^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$">
                    @error('mac_address')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>


          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <button type="submit" class="btn btn-primary">Simpan</button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  @vite(['resources/js/leaflet.js'])
  <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCw0GxwYh8kT-pVOYwoh33l0oXMgChS63A&libraries=places&v=weekly"
    async defer></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const el = document.querySelector('address-modal');
      const addressInput = document.getElementById('addressInput');
      const latInput = document.getElementById('latitudeInput');
      const lngInput = document.getElementById('longitudeInput');
      const display = document.getElementById('addressDisplay');

      if (!el) return;

      el.addEventListener('address-apply', function(ev) {
        const {
          address,
          lat,
          lng
        } = ev.detail || {};
        addressInput.value = address || '';
        display.textContent = address || '';
        display.style.display = address ? '' : 'none';

        if (lat && lng) {
          latInput.value = lat;
          lngInput.value = lng;
        } else {
          latInput.value = '';
          lngInput.value = '';
        }
      });

      const pkgSelect = document.getElementById('packageSelect');

      const updatePackageInfo = () => {
        const dueInput = document.getElementById('dueInput');
        const bandwidthInput = document.getElementById('bandwidthInput');
        const priceInput = document.getElementById('priceInput');

        const opt = pkgSelect.options[pkgSelect.selectedIndex];
        if (!opt?.value || !opt.dataset) {
          dueInput.value = '';
          bandwidthInput.value = '';
          priceInput.value = '';
          return;
        }

        const price = opt.dataset.price;
        const bandwidth = opt.dataset.bandwidth;
        const due = opt.dataset.due;

        dueInput.value = due || '-';
        bandwidthInput.value = bandwidth + " Mbps" || '-';
        priceInput.value = price || '-';
      }

      pkgSelect.addEventListener('change', updatePackageInfo);
      updatePackageInfo();

      document.querySelectorAll('[data-tom-select]').forEach(select => {
        new TomSelect(select, {
          maxItems: 1
        });
      });
    });
  </script>
</x-e-billing::layouts.panel>
