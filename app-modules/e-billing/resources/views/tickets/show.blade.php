<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">Layanan</div>
          <h2 class="page-title">Detail Tiket</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <a href="{{ route('e-billing.tickets.index') }}" class="btn btn-secondary">Kembali</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <div class="row row-cards">
        <div class="col-12 col-lg-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Percakapan</h3>
            </div>
            <div class="card-body">
              <div class="timeline">
                @forelse ($ticket->messages as $msg)
                  <div class="timeline-item">
                    <div class="row">
                      <div class="col-auto">
                        <div class="timeline-item-icon bg-primary"></div>
                      </div>
                      <div class="col">
                        <div class="timeline-item-description">
                          <strong>{{ $msg->user?->name ?? ($msg->author_name ?? 'Pengguna') }}</strong>
                          <span class="text-muted"> • {{ $msg->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="timeline-item-content">
                          <p class="mb-0">{{ $msg->message }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="text-muted">Belum ada pesan.</div>
                @endforelse
              </div>
            </div>
            <div class="card-footer">
              <form action="{{ route('e-billing.tickets.messages.store', $ticket) }}" method="POST">
                @csrf
                <div class="mb-2">
                  <textarea name="message" rows="3" class="form-control" placeholder="Tulis balasan..." required></textarea>
                </div>
                <button class="btn btn-primary">Kirim</button>
              </form>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Ringkasan</h3>
            </div>
            <div class="card-body">
              <div class="datagrid">
                <div class="datagrid-item">
                  <div class="datagrid-title">Kode</div>
                  <div class="datagrid-content">{{ $ticket->code }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Subjek</div>
                  <div class="datagrid-content">{{ $ticket->subject }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Pelanggan</div>
                  <div class="datagrid-content">{{ $ticket->customer->name }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Prioritas</div>
                  <div class="datagrid-content"><x-common.badge :color="$ticket->priority->color()" :label="$ticket->priority->label()" /></div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Status</div>
                  <div class="datagrid-content"><x-common.badge :color="$ticket->status->color()" :label="$ticket->status->label()" /></div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">Dibuat</div>
                  <div class="datagrid-content">{{ $ticket->created_at->diffForHumans() }}</div>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Ubah Status</h3>
            </div>
            <div class="card-body">
              <form action="{{ route('e-billing.tickets.update', $ticket) }}" method="POST" class="row g-2">
                @csrf
                @method('PUT')
                <div class="col-12">
                  <label class="form-label">Prioritas</label>
                  <select class="form-select" name="priority" required>
                    @foreach (Modules\EBilling\Enums\TicketPriority::cases() as $p)
                      <option value="{{ $p->value }}" @selected($p === $ticket->priority)>{{ $p->label() }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Status</label>
                  <select class="form-select" name="status" required>
                    @foreach (Modules\EBilling\Enums\TicketStatus::cases() as $s)
                      <option value="{{ $s->value }}" @selected($s === $ticket->status)>{{ $s->label() }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Petugas</label>
                  <select class="form-select" name="assigned_to">
                    <option value="">-- Tidak ada --</option>
                    @foreach ($users as $u)
                      <option value="{{ $u->id }}" @selected($ticket->assigned_to === $u->id)>{{ $u->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Subjek</label>
                  <input type="text" class="form-control" name="subject" required maxlength="255"
                    value="{{ $ticket->subject }}">
                </div>
                <div class="col-12">
                  <button class="btn btn-primary">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
