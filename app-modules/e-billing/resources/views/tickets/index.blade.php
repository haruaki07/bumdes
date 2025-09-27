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
            <i class="ti ti-plus icon"></i>
            Buat Tiket
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')
      <div class="row row-deck row-cards">
        <div class="col-12">
          <x-datatable tableId="ticketsTable" title="Daftar Tiket" :data="$tickets">
            <x-slot:thead>
              <tr>
                <th>#</th>
                <x-sortable-header field="code" label="Kode" />
                <x-sortable-header field="subject" label="Subjek" />
                <x-sortable-header field="status" label="Pelanggan" />
                <x-sortable-header field="priority" label="Prioritas" />
                <x-sortable-header field="customer.name" label="Status" />
                <th>Tanggal dibuat</th>
                <th>Aksi</th>
              </tr>
            </x-slot>

            <x-slot:tbody>
              @forelse ($tickets as $ticket)
                <tr>
                  <td>{{ $loop->iteration + $tickets->firstItem() - 1 }}</td>
                  <td class="fw-medium">{{ $ticket->code }}</td>
                  <td>{{ $ticket->subject }}</td>
                  <td>{{ $ticket->customer->name }}</td>
                  <td><x-common.badge :color="$ticket->priority->color()" :label="$ticket->priority->label()" /></td>
                  <td><x-common.badge :color="$ticket->status->color()" :label="$ticket->status->label()" /></td>
                  <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                  <td>
                    <a href="{{ route('e-billing.tickets.show', $ticket) }}" class="btn btn-icon btn-primary"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat detail">
                      <i class="ti ti-eye"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center">Belum ada tiket.</td>
                </tr>
              @endforelse
            </x-slot>
          </x-datatable>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
