@extends('tablar::page')

@section('content')
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Manajemen Keuangan
          </div>
          <h2 class="page-title">
            Kategori Transaksi
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <a href="{{ route('transaction-categories.create') }}" class="btn btn-primary">
              <i class="icon ti ti-plus"></i>
              Tambah Kategori
            </a>
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
          <x-datatable :data="$categories" tableId="transaction-categories-table">
            <x-slot:thead>
              <tr>
                <th>#</th>
                <x-sortable-header field="code" label="Kode" />
                <x-sortable-header field="name" label="Nama" />
                <x-sortable-header field="description" label="Deskripsi" />
                <x-sortable-header field="type" label="Tipe" class="text-center" />
                <th>Kategori Induk</th>
                <th>Status</th>
                <th class="w-1">Aksi</th>
              </tr>
            </x-slot:thead>

            <x-slot:tbody>
              @forelse ($categories as $category)
                <tr>
                  <td>{{ $loop->iteration + $categories->firstItem() - 1 }}</td>
                  <td><x-common.badge color="primary" :label="$category->code" light /></td>
                  <td class="fw-medium">{{ $category->name }}</td>
                  <td data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $category->description }}">
                    {{ Str::limit($category->description, 30) ?? '-' }}</td>
                  <td class="text-center">
                    @if ($category->type === 'income')
                      <x-common.badge color="success" label="Pendapatan" light />
                    @else
                      <x-common.badge color="danger" label="Beban" light />
                    @endif
                  </td>
                  <td class="text-muted">
                    @if ($category->parent)
                      <span>{{ $category->parent->name }}</span>
                    @else
                      <span>-</span>
                    @endif
                  </td>
                  <td>
                    <form method="POST" action="{{ route('transaction-categories.toggle-active', $category) }}"
                      id="statusForm{{ $category->id }}">
                      @csrf
                      <label class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="is_active" @checked($category->is_active) />
                        <span class="form-check-label">{{ $category->is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
                      </label>
                    </form>
                  </td>
                  <td>
                    <a href="{{ route('transaction-categories.edit', $category) }}" class="btn btn-icon btn-primary"
                      title="Edit" data-bs-toggle="tooltip" data-bs-placement="top">
                      <i class="icon ti ti-edit"></i>
                    </a>
                    <button type="button" class="btn btn-icon btn-danger"
                      onclick="deleteConfirm('{{ route('transaction-categories.destroy', $category) }}')" title="Hapus"
                      data-bs-toggle="tooltip" data-bs-placement="top">
                      <i class="icon ti ti-trash"></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted">Tidak ada data</td>
                </tr>
              @endforelse
            </x-slot:tbody>
          </x-datatable>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script>
    let submitting = false;
    document.querySelectorAll('[id^="statusForm"]').forEach(form => {
      const checkbox = form.querySelector('input[name="is_active"]');
      checkbox.addEventListener('change', () => {
        if (submitting) return;
        submitting = true;
        form.submit();
        checkbox.disabled = true;
      });
    });
  </script>
@endpush
