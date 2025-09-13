<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">Layanan</div>
          <h2 class="page-title">Buat Tiket</h2>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')
      <div class="card">
        <div class="card-body">
          <form action="{{ route('e-billing.tickets.store') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-6">
              <label class="form-label">Pelanggan</label>
              <select class="form-select" name="customer_id" required>
                <option value="">-- Pilih --</option>
                @foreach ($customers as $c)
                  <option value="{{ $c->id }}" @selected(($selectedCustomerId ?? null) == $c->id)>{{ $c->name }}
                    ({{ $c->customer_id }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Prioritas</label>
              <select class="form-select" name="priority" required>
                @foreach ($priorities as $p)
                  <option value="{{ $p->value }}">{{ $p->label() }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Subjek</label>
              <input type="text" class="form-control" name="subject" required maxlength="255">
            </div>
            <div class="col-12">
              <label class="form-label">Pesan Pertama (opsional)</label>
              <textarea class="form-control" name="message" rows="5" placeholder="Jelaskan masalah"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Petugas (opsional)</label>
              <select class="form-select" name="assigned_to">
                <option value="">-- Tidak ada --</option>
                @foreach ($users as $u)
                  <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <button class="btn btn-primary">Simpan</button>
              <a href="{{ route('e-billing.tickets.index') }}" class="btn btn-secondary">Batal</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
