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
            Daftar Transaksi
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <a href="{{ route('transactions.create') }}" class="btn btn-primary">
              <i class="icon ti ti-plus"></i>
              Tambah Transaksi
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      @include('tablar::common.alert')

      <div class="card mb-3">
        <div class="card-header">
          <h3 class="card-title">Filter Transaksi</h3>
        </div>
        <div class="card-body">
          <form method="GET" action="{{ route('transactions.index') }}">
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Tipe</label>
                <select name="type" class="form-select">
                  <option value="">Semua Tipe</option>
                  <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pendapatan</option>
                  <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Beban</option>
                </select>
              </div>

              <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select">
                  <option value="">Semua Kategori</option>
                  @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                      {{ $category->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-2">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
              </div>

              <div class="col-md-2">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
              </div>

              <div class="col-md-2">
                <label class="form-label">Status Verifikasi</label>
                <select name="verified" class="form-select">
                  <option value="">Semua Status</option>
                  <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Terverifikasi</option>
                  <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Belum Terverifikasi</option>
                </select>
              </div>
            </div>

            <div class="mt-3 btn-list justify-content-end">
              <button type="submit" class="btn btn-primary">
                <i class="icon ti ti-filter"></i>
                Filter
              </button>
              <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                <i class="icon ti ti-refresh"></i>
                Reset
              </a>
            </div>
          </form>
        </div>
      </div>

      @if ($transactions->count() > 0)
        <x-datatable :data="$transactions" tableId="transactionsTable">
          <table class="table table-hover table-striped">
            <x-slot:thead>
              <tr>
                <x-sortable-header field="transaction_date" label="Tanggal" />
                <x-sortable-header field="reference_number" label="No. Referensi" />
                <th>Kategori</th>
                <th>Deskripsi</th>
                <x-sortable-header field="amount" label="Jumlah" class="text-end" />
                <x-sortable-header field="type" label="Tipe" class="text-center" />
                <th class="text-center">Status</th>
                <th>Aksi</th>
              </tr>
            </x-slot:thead>
            <x-slot:tbody>
              @foreach ($transactions as $transaction)
                <tr>
                  <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                  <td>
                    <x-common.badge color="primary" label="{{ $transaction->reference_number }}" light />
                  </td>
                  <td>
                    <div>{{ $transaction->category->name }}</div>
                    @if ($transaction->category->parent)
                      <small class="text-muted">{{ $transaction->category->parent->name }}</small>
                    @endif
                  </td>
                  <td>
                    <div>{{ Str::limit($transaction->description, 50) }}</div>
                    @if ($transaction->fundingRequest)
                      <a href="{{ route('funding-requests.show', $transaction->fundingRequest) }}"
                        class="text-muted text-reset">
                        <i class="ti ti-xs ti-link"></i>
                        {{ $transaction->fundingRequest->business->name }}
                      </a>
                    @endif
                  </td>
                  <td class="text-end {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                  </td>
                  <td class="text-center">
                    @if ($transaction->type === 'income')
                      <x-common.badge color="success" label="Pendapatan" light />
                    @else
                      <x-common.badge color="danger" label="Beban" light />
                    @endif
                  </td>
                  <td class="text-center">
                    @if ($transaction->isVerified())
                      <x-common.badge color="success" label="Terverifikasi" light />
                    @else
                      <x-common.badge color="warning" label="Belum Verifikasi" light />
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-icon btn-primary"
                      title="Detail" data-bs-toggle="tooltip" data-bs-placement="top">
                      <i class="ti ti-eye"></i>
                    </a>

                    <div class="dropdown d-inline-block">
                      <button type="button" class="btn btn-icon" data-bs-toggle="dropdown">
                        <i class="icon ti ti-dots"></i>
                      </button>
                      <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        @if (!$transaction->isVerified())
                          <a class="dropdown-item" href="{{ route('transactions.edit', $transaction) }}">
                            <i class="dropdown-item-icon icon ti ti-edit"></i>
                            Edit
                          </a>
                          <a class="dropdown-item" href="#" onclick="verifyTransaction({{ $transaction->id }})">
                            <i class="dropdown-item-icon icon ti ti-check"></i>
                            Verifikasi
                          </a>
                          <a class="dropdown-item text-danger" href="#"
                            onclick="deleteTransaction({{ $transaction->id }})">
                            <i class="dropdown-item-icon text-danger icon ti ti-trash"></i>
                            Hapus Transaksi
                          </a>
                        @else
                          <a class="dropdown-item" href="#"
                            onclick="unverifyTransaction({{ $transaction->id }})">
                            <i class="dropdown-item-icon icon ti ti-x"></i>
                            Batalkan Verifikasi
                          </a>
                        @endif
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
            </x-slot:tbody>
          </table>
        </x-datatable>
      @else
        <div class="card">
          <div class="card-body">
            <div class="empty">
              <div class="empty-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                  fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                  stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-invoice">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                  <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                  <path d="M9 7l1 0" />
                  <path d="M9 13l6 0" />
                  <path d="M13 17l2 0" />
                </svg>
              </div>
              <p class="empty-title">Tidak ada transaksi</p>
              <p class="empty-subtitle text-muted">
                Belum ada transaksi yang tercatat. Klik tombol "Tambah Transaksi" untuk mulai mencatat transaksi.
              </p>
              <div class="empty-action">
                <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                  <i class="icon ti ti-plus"></i>
                  Tambah Transaksi
                </a>
              </div>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
@endsection

@push('js')
  <script type="module">
    const selectOptions = {
      maxItems: 1,
      plugins: {
        clear_button: {
          html: (data) => `<div class="${data.className}" title="${data.title}" style="line-height:1;">
            <i class="icon ti ti-sm ti-x" style="color:#9ca3af;"></i>
          </div>`
        },
      }
    }

    new TomSelect('select[name="type"]', {
      placeholder: 'Semua Tipe',
      ...selectOptions
    });

    new TomSelect('select[name="category_id"]', {
      placeholder: 'Semua Kategori',
      ...selectOptions
    });

    new TomSelect('select[name="verified"]', {
      placeholder: 'Semua Status',
      ...selectOptions
    });
  </script>
  <script>
    function verifyTransaction(transactionId) {
      deleteConfirm("{{ route('transactions.verify', ['transaction' => '_']) }}/".replace('_', transactionId), "PATCH", {
        title: 'Verifikasi Transaksi?',
        message: 'Apakah Anda yakin ingin memverifikasi transaksi ini?',
        buttons: {
          cancel: {
            label: "Batal"
          },
          confirm: {
            label: "Ya",
            className: "btn-primary"
          },
        },
      });
    }

    function unverifyTransaction(transactionId) {
      deleteConfirm("{{ route('transactions.unverify', ['transaction' => '_']) }}/".replace('_', transactionId),
        "PATCH", {
          title: 'Batalkan Verifikasi Transaksi?',
          message: 'Apakah Anda yakin ingin membatalkan verifikasi transaksi ini?',
          buttons: {
            cancel: {
              label: "Batal"
            },
            confirm: {
              label: "Ya, Batalkan",
              className: "btn-warning"
            },
          },
        });
    }

    function deleteTransaction(transactionId) {
      deleteConfirm("{{ route('transactions.destroy', ['transaction' => '_']) }}/".replace('_', transactionId),
        "DELETE", {
          title: 'Hapus Transaksi?',
          message: 'Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.',
          buttons: {
            cancel: {
              label: "Batal"
            },
            confirm: {
              label: "Ya, Hapus",
              className: "btn-danger"
            },
          },
        });
    }
  </script>
@endpush
