<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Data Master
          </div>
          <h2 class="page-title">
            Paket
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            @haspermission('create-packages', 'ebil')
              <a href="{{ route('e-billing.master-data.packages.create') }}" class="btn btn-primary">
                <i class="icon ti ti-plus"></i>
                Tambah Paket
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
          <x-datatable tableId="packagesTable" title="Daftar Paket" :data="$packages">
            <x-slot:thead>
              <tr>
                <th>#</th>
                <x-sortable-header field="code" label="Kode" />
                <x-sortable-header field="name" label="Nama Paket" />
                <x-sortable-header field="description" label="Deskripsi" />
                <x-sortable-header field="bandwidth" label="Bandwidth" />
                <x-sortable-header field="price" label="Harga" />
                <x-sortable-header field="due" label="Jatuh Tempo" />
                <th>Aksi</th>
              </tr>
            </x-slot>

            <x-slot:tbody>
              @forelse ($packages as $package)
                <tr>
                  <td>{{ $loop->iteration + $packages->firstItem() - 1 }}</td>
                  <td><x-common.badge color="primary" :label="$package->code" light /></td>
                  <td class="fw-medium">{{ $package->name }}</td>
                  <td title="{{ $package->description }}">{{ Str::limit($package->description, 30) ?? '-' }}</td>
                  <td>{{ $package->bandwidth }} Mbps</td>
                  <td>Rp{{ number_format($package->price, 0, ',', '.') }}</td>
                  <td>Setiap tanggal {{ $package->due }}</td>
                  <td>
                    <a href="{{ route('e-billing.master-data.packages.show', $package) }}"
                      class="btn btn-icon btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                      title="Lihat detail">
                      <i class="ti ti-eye"></i>
                    </a>
                    @haspermission('update-packages', 'ebil')
                      <a href="{{ route('e-billing.master-data.packages.edit', $package) }}"
                        class="btn btn-icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                        <i class="ti ti-edit"></i>
                      </a>
                    @endhaspermission
                    @haspermission('delete-packages', 'ebil')
                      <button class="btn btn-icon btn-danger"
                        onclick="deleteConfirm('{{ route('e-billing.master-data.packages.destroy', $package) }}')"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                        <i class="ti ti-trash"></i>
                      </button>
                    @endhaspermission
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center">Tidak ada paket yang terdaftar.</td>
                </tr>
              @endforelse
            </x-slot>
          </x-datatable>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
