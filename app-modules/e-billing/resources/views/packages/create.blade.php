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
              {!! $form->render() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</x-e-billing::layouts.panel>
