<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Data Master
          </div>
          <h2 class="page-title">
            Perangkat
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            @haspermission('create-devices', 'ebil')
              <a href="{{ route('e-billing.master-data.devices.create') }}" class="btn btn-primary">
                <i class="icon ti ti-plus"></i>
                Tambah Perangkat
              </a>
            @endhaspermission
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')
      <div class="row row-deck row-cards">
        <div class="col-12">
          <x-datatable tableId="devicesTable" title="Daftar Perangkat" :data="$devices">
            <x-slot:thead>
              <tr>
                <th>#</th>
                <x-sortable-header field="code" label="Kode" />
                <x-sortable-header field="brand" label="Merek" />
                <x-sortable-header field="model" label="Model" />
                <x-sortable-header field="description" label="Deskripsi" />
                <th>Aksi</th>
              </tr>
            </x-slot>

            <x-slot:tbody>
              @forelse ($devices as $device)
                <tr>
                  <td>{{ $loop->iteration + $devices->firstItem() - 1 }}</td>
                  <td><x-common.badge color="primary" :label="$device->code" light /></td>
                  <td class="fw-medium">{{ $device->brand }}</td>
                  <td>{{ $device->model }}</td>
                  <td title="{{ $device->description }}">{{ Str::limit($device->description, 30) ?? '-' }}</td>
                  <td>
                    <a href="{{ route('e-billing.master-data.devices.show', $device) }}"
                      class="btn btn-icon btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                      title="Lihat detail">
                      <i class="ti ti-eye"></i>
                    </a>
                    @haspermission('update-devices', 'ebil')
                      <a href="{{ route('e-billing.master-data.devices.edit', $device) }}"
                        class="btn btn-icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                        <i class="ti ti-edit"></i>
                      </a>
                    @endhaspermission
                    @haspermission('delete-devices', 'ebil')
                      <button class="btn btn-icon btn-danger"
                        onclick="deleteConfirm('{{ route('e-billing.master-data.devices.destroy', $device) }}')"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                        <i class="ti ti-trash"></i>
                      </button>
                    @endhaspermission
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center">Tidak ada perangkat yang terdaftar.</td>
                </tr>
              @endforelse
            </x-slot>
          </x-datatable>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
