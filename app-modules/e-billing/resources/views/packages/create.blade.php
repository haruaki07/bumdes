<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Data Master
          </div>
          <h2 class="page-title">
            Tambah Paket
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
              <h3 class="card-title">Informasi Paket</h3>
            </div>
            <div class="card-body">
              <form action="{{ route('e-billing.master-data.packages.store') }}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Nama Paket</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                      value="{{ old('name') }}" required placeholder="Masukkan nama paket">
                    @error('name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Kode Paket</label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                      value="{{ old('code') }}" required placeholder="Masukkan kode paket">
                    @error('code')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Deskripsi</label>
                  <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                    placeholder="Masukkan deskripsi paket (opsional)">{{ old('description') }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label class="form-label required">Bandwidth (Mbps)</label>
                    <input type="number" name="bandwidth" class="form-control @error('bandwidth') is-invalid @enderror"
                      value="{{ old('bandwidth') }}" required placeholder="Masukkan bandwidth paket">
                    @error('bandwidth')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label required">Harga (Rp)</label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                      value="{{ old('price') }}" required placeholder="Masukkan harga paket" data-mask-currency>
                    @error('price')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label required">Tanggal Jatuh Tempo</label>
                    <input type="number" name="due" class="form-control @error('due') is-invalid @enderror"
                      required value="{{ old('due') }}" min="1" max="28"
                      placeholder="Masukkan tanggal jatuh tempo paket">
                    <div class="form-text">
                      Isi dengan angka 1-28.
                    </div>
                    @error('due')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</x-e-billing::layouts.panel>
