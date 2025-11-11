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
            Edit Transaksi
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-secondary">
              Kembali
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
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Informasi Transaksi</h3>
            </div>
            <div class="card-body">
              <form action="{{ route('transactions.update', $transaction) }}" method="POST" enctype="multipart/form-data"
                id="formEdit">
                @csrf
                @method('PUT')

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Tipe Transaksi</label>
                    <input type="text" name="type" id="typeSelect"
                      class="form-select @error('type') is-invalid @enderror"
                      value="{{ old('type', $transaction->type) }}" required />
                    @error('type')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Kategori</label>
                    <input type="text" name="transaction_category_id" id="categorySelect"
                      class="form-select @error('transaction_category_id') is-invalid @enderror"
                      value="{{ old('transaction_category_id', $transaction->transaction_category_id) }}" required />
                    @error('transaction_category_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Tanggal Transaksi</label>
                    <input type="date" name="transaction_date"
                      class="form-control @error('transaction_date') is-invalid @enderror"
                      value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" required>
                    @error('transaction_date')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Jumlah</label>
                    <div class="input-group">
                      <span class="input-group-text">Rp</span>
                      <input type="text" name="amount" id="amount"
                        class="form-control @error('amount') is-invalid @enderror"
                        value="{{ old('amount', number_format($transaction->amount, 0, ',', '.')) }}" required
                        data-mask-currency>
                    </div>
                    <small class="form-hint">Jumlah pemasukan atau pengeluaran</small>
                    @error('amount')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">No. Referensi</label>
                    <input type="text" name="reference_number"
                      class="form-control @error('reference_number') is-invalid @enderror"
                      value="{{ old('reference_number', $transaction->reference_number) }}"
                      placeholder="Kosongkan untuk auto-generate">
                    <small class="form-hint">Format otomatis: INC-202511-0001 atau EXP-202511-0001</small>
                    @error('reference_number')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label required">Deskripsi</label>
                  <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                    placeholder="Deskripsi transaksi" required>{{ old('description', $transaction->description) }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label">Catatan</label>
                  <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror"
                    placeholder="Catatan tambahan (opsional)">{{ old('notes', $transaction->notes) }}</textarea>
                  @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label">Dokumen Pendukung</label>

                  @if ($transaction->document_path)
                    <div class="mb-2" id="existingDocument">
                      @php
                        $extension = strtolower(pathinfo($transaction->document_path, PATHINFO_EXTENSION));
                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                      @endphp

                      @if ($isImage)
                        <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal">
                          <img src="{{ Storage::url($transaction->document_path) }}"
                            alt="{{ basename($transaction->document_path) }}" class="img-fluid rounded"
                            style="max-height: 200px;" />
                        </a>

                        <div class="modal modal-blur fade" id="imageModal" tabindex="-1" role="dialog"
                          aria-hidden="true">
                          <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Dokumen Pendukung</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                  aria-label="Close"></button>
                              </div>
                              <div class="modal-body text-center">
                                <img src="{{ Storage::url($transaction->document_path) }}" alt="Document"
                                  class="img-fluid">
                              </div>
                              <div class="modal-footer">
                                <a href="{{ Storage::url($transaction->document_path) }}" class="btn btn-primary"
                                  target="_blank">
                                  <i class="icon ti ti-download"></i>
                                  Download
                                </a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      @else
                        <div class="alert alert-info mb-0">
                          <div class="d-flex align-items-center gap-2">
                            <a href="{{ Storage::url($transaction->document_path) }}" class="btn btn-action"
                              target="_blank">
                              <i class="ti ti-download"></i>
                            </a>
                            <div>
                              <strong>{{ basename($transaction->document_path) }}</strong>
                              <div class="text-muted small">Dokumen saat ini</div>
                            </div>
                          </div>
                        </div>
                      @endif
                    </div>
                  @endif

                  <input type="file" name="document" id="documentInput"
                    class="form-control form-dropzone @error('document') is-invalid @enderror"
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                  <small class="form-hint">
                    Format: PDF, DOC, DOCX, JPG, PNG. Max: 5MB
                    @if ($transaction->document_path)
                      . Upload file baru akan mengganti dokumen yang ada.
                    @endif
                  </small>
                  @error('document')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <div id="documentPreview" class="mt-2"></div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Terkait Permintaan Pendanaan</label>
                  <select name="funding_request_id"
                    class="form-select @error('funding_request_id') is-invalid @enderror">
                    <option value="">Tidak terkait</option>
                    @foreach ($fundingRequests as $fr)
                      <option value="{{ $fr->id }}"
                        {{ old('funding_request_id', $transaction->funding_request_id) == $fr->id ? 'selected' : '' }}>
                        {{ $fr->business->name }} - Rp {{ number_format($fr->amount_approved, 0, ',', '.') }}
                        ({{ ucfirst($fr->status->label()) }})
                      </option>
                    @endforeach
                  </select>
                  <small class="form-hint">Pilih jika transaksi ini terkait dengan pendanaan tertentu</small>
                  @error('funding_request_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </form>
            </div>

            <div class="card-footer">
              <div class="btn-list justify-content-end">
                <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-ghost-secondary">
                  Batal
                </a>
                <button type="submit" class="btn btn-primary" form="formEdit">
                  Perbarui
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js')
  <script type="module">
    const typeOptions = [{
        value: 'income',
        label: 'Pendapatan'
      },
      {
        value: 'expense',
        label: 'Beban'
      }
    ];

    const allCategories = @js(collect($categories)->flatten()->map(fn($item) => ['id' => $item->id, 'name' => $item->name, 'type' => $item->type, 'parent' => $item->parent ? true : false]));

    const typeSelect = new TomSelect('#typeSelect', {
      valueField: 'value',
      labelField: 'label',
      searchField: 'label',
      placeholder: 'Pilih Tipe',
      options: typeOptions,
      maxItems: 1,
      plugins: {
        clear_button: {
          html: function(data) {
            return `<div class="${data.className}" title="${data.title}" style="line-height:1;">
              <i class="icon ti ti-sm ti-x" style="color:#9ca3af;"></i>
            </div>`;
          }
        },
      },
    });

    const categorySelect = new TomSelect('#categorySelect', {
      valueField: 'id',
      labelField: 'name',
      searchField: 'name',
      placeholder: 'Pilih Kategori',
      options: allCategories,
      maxItems: 1,
      plugins: {
        clear_button: {
          html: function(data) {
            return `<div class="${data.className}" title="${data.title}" style="line-height:1;">
              <i class="icon ti ti-sm ti-x" style="color:#9ca3af;"></i>
            </div>`;
          }
        },
      },
      render: {
        option: function(data, escape) {
          const badgeColor = data.type === 'income' ? 'success' : 'danger';
          const badgeText = data.type === 'income' ? 'Pendapatan' : 'Beban';
          const prefix = data.parent ? '&emsp;' : '&nbsp;';
          return `<div>
            <span class="badge bg-${badgeColor}-lt text-${badgeColor}-fg-lt">${badgeText}</span>
            <span class="${!data.parent ? 'fw-bold' : ''}">${prefix}${escape(data.name)}</span>
          </div>`;
        },
      },
    });

    // Set initial values
    const initialType = '{{ old('type', $transaction->type) }}';
    const initialCategory = {{ old('transaction_category_id', $transaction->transaction_category_id) }};

    if (initialType) {
      typeSelect.setValue(initialType);
    }

    if (initialCategory) {
      categorySelect.setValue(initialCategory);
    }

    typeSelect.on('change', () => {
      syncCategoryOptions();
    });

    categorySelect.on('change', () => {
      syncType();
    });

    syncCategoryOptions();

    function syncType() {
      const categoryValue = Number(categorySelect.getValue()) || null;
      const categoryValueType = allCategories.find(cat => cat.id === categoryValue)?.type;

      if (categoryValue && (!typeSelect.value || typeSelect.value !== categoryValueType)) {
        typeSelect.input.value = categoryValueType;
        typeSelect.input.dispatchEvent(new Event('input'));
      }
    }

    function syncCategoryOptions() {
      const type = typeSelect.getValue();
      const categoryValue = Number(categorySelect.getValue()) || null;
      const categoryValueType = allCategories.find(cat => cat.id === categoryValue)?.type;

      if (categoryValue && categoryValueType !== type) {
        categorySelect.clear();
      }

      categorySelect.clearOptions();
      const filteredCategories = type ? allCategories.filter(cat => cat.type === type) : allCategories;
      categorySelect.addOptions(filteredCategories);

      // Restore category value if it matches the type
      if (categoryValue && categoryValueType === type) {
        categorySelect.setValue(categoryValue);
      }
    }

    new Dropzone(".form-dropzone", {
      maxFileSize: 5 * 1024 * 1024,
    });
  </script>
@endsection
