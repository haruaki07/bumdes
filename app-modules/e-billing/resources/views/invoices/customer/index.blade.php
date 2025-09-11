@section('title', 'Cek Tagihan')

<x-e-billing::layouts.blank>
  @push('css')
    <style>
      .search-wrapper {
        max-width: 520px;
        margin: 0 auto;
      }
    </style>
  @endpush

  <div class="container container-tight py-5 my-auto">
    <div class="card card-md search-wrapper">
      <div class="card-body">
        <div class="text-center mb-5">
          <h2 class="card-title">Cek Tagihan</h2>
          <div class="card-subtitle">Masukkan ID Pelanggan atau Nomor Tagihan</div>
        </div>

        <form id="invoiceSearchForm" autocomplete="on">
          <div class="mb-3">
            <label for="searchInput" class="form-label">ID / Nomor Tagihan</label>
            <input type="text" id="searchInput" name="q" class="form-control" placeholder="INV2025XXXXXX"
              required>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary" id="btnSearch">
              <i class="ti ti-search icon"></i> Cari
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @push('js')
    <script type="module">
      const form = document.getElementById('invoiceSearchForm');
      const input = document.getElementById('searchInput');
      const btn = document.getElementById('btnSearch');
      const baseUrl = "{{ route('e-billing.invoice.customer-show', ['customer_id' => 'PLACEHOLDER']) }}";
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const value = (input.value || '').trim();
        if (!value) {
          input.focus();
          return;
        }

        btn.disabled = true;
        const url = baseUrl.replace('PLACEHOLDER', encodeURIComponent(value));
        window.location.href = url;
      });
    </script>
  @endpush
</x-e-billing::layouts.blank>
