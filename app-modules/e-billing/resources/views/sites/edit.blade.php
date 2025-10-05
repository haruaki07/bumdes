<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Data Master
          </div>
          <h2 class="page-title">
            Edit Site
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <x-button class="btn-secondary" onclick="history.back()">Kembali</x-button>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')
      <div class="row row-deck row-cards">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Informasi Site</h3>
            </div>
            <div class="card-body">
              <form method="POST" action="{{ route('e-billing.master-data.sites.update', $site) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                  <label class="form-label required" for="code">Kode</label>
                  <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                    name="code" value="{{ old('code', $site->code) }}" maxlength="50"
                    placeholder="Masukkan kode site">
                  @error('code')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label required" for="name">Nama Site</label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    name="name" value="{{ old('name', $site->name) }}" maxlength="100"
                    placeholder="Masukkan nama site" required>
                  @error('name')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label" for="description">Deskripsi</label>
                  <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                    rows="3" placeholder="Masukkan deskripsi site (opsional)">{{ old('description', $site->description) }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                  @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
