<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">Layanan</div>
          <h2 class="page-title">Tiket Pelanggan</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a href="{{ route('e-billing.tickets.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i>
            Buat Tiket
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <div class="card">
        <div class="table-responsive">
          <table class="table table-vcenter card-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Kode</th>
                <th>Subjek</th>
                <th>Pelanggan</th>
                <th>Prioritas</th>
                <th>Status</th>
                <th>Dibuat</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse ($tickets as $ticket)
                <tr>
                  <td>{{ $loop->iteration + $tickets->firstItem() - 1 }}</td>
                  <td class="fw-medium">{{ $ticket->code }}</td>
                  <td>{{ $ticket->subject }}</td>
                  <td>{{ $ticket->customer->name }}</td>
                  <td><x-common.badge :color="$ticket->priority->color()" :label="$ticket->priority->label()" /></td>
                  <td><x-common.badge :color="$ticket->status->color()" :label="$ticket->status->label()" /></td>
                  <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                  <td class="text-end">
                    <a href="{{ route('e-billing.tickets.show', $ticket) }}" class="btn btn-sm btn-primary">
                      Detail
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center text-muted">Belum ada tiket.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="card-footer">
          {{ $tickets->links() }}
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
