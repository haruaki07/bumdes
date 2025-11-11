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
            Edit Kategori Transaksi
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <a href="{{ route('transaction-categories.index') }}" class="btn btn-secondary">
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
              <h3 class="card-title">Informasi Kategori</h3>
            </div>
            <div class="card-body">
              <form action="{{ route('transaction-categories.update', $category) }}" method="POST" id="formEdit">
                @csrf
                @method('PUT')
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Nama Kategori</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                      value="{{ old('name', $category->name) }}" placeholder="Contoh: Pendapatan Jasa" required>
                    @error('name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Tipe</label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                      <option value="">Pilih Tipe</option>
                      <option value="income" {{ old('type', $category->type) === 'income' ? 'selected' : '' }}>Pendapatan
                      </option>
                      <option value="expense" {{ old('type', $category->type) === 'expense' ? 'selected' : '' }}>Beban
                      </option>
                    </select>
                    @error('type')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label required">Kode</label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                      value="{{ old('code', $category->code) }}" placeholder="Contoh: INC-JSA" required>
                    <small class="form-hint">Kode unik untuk kategori (huruf besar, tanpa spasi)</small>
                    @error('code')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori Induk</label>
                    <input type="text" name="parent_id" class="form-select @error('parent_id') is-invalid @enderror"
                      id="parentIdSelect" value="{{ old('parent_id', $category->parent_id) }}" />
                    <small class="form-hint">Kosongkan jika ini adalah kategori utama</small>
                    @error('parent_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Deskripsi</label>
                  <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                    placeholder="Deskripsi kategori (opsional)">{{ old('description', $category->description) }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="order" class="form-control @error('order') is-invalid @enderror"
                      value="{{ old('order', $category->order) }}" min="0">
                    <small class="form-hint">Urutan tampilan (semakin kecil, semakin atas)</small>
                    @error('order')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <div class="form-check form-switch mt-3">
                      <input type="hidden" name="is_active" value="0">
                      <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1"
                        {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                      <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                  </div>
                </div>
              </form>
            </div>

            <div class="card-footer">
              <div class="btn-list justify-content-end">
                <button type="reset" class="btn btn-ghost-secondary" form="formEdit">
                  Reset
                </button>
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
    const nameInput = document.querySelector('input[name="name"]');
    const typeSelect = document.querySelector('select[name="type"]');
    const codeInput = document.querySelector('input[name="code"]');

    nameInput.addEventListener('input', () => {
      const name = nameInput.value.trim();
      const type = typeSelect.value;

      if (!codeInput.value && name && type) {
        const prefix = type === 'income' ? 'INC' : 'EXP';
        const words = name.split(/\s+/);
        const acronym = words.map(w => w.charAt(0)).join('').toUpperCase().slice(0, 3);
        codeInput.value = `${prefix}-${acronym}`;
      }
    });

    typeSelect.addEventListener('change', () => {
      codeInput.value = '';
      nameInput.dispatchEvent(new Event('input'));
    });

    const currentCategoryId = {{ $category->id }};
    const parentCategories = @js($parentCategories->map(fn($item) => $item->only(['id', 'name', 'type'])))
      .filter(cat => cat.id !== currentCategoryId);

    const parentIdSelect = document.getElementById('parentIdSelect');
    const parentSelect = new TomSelect(parentIdSelect, {
      valueField: 'id',
      labelField: 'name',
      searchField: 'name',
      placeholder: 'Pilih Kategori Induk',
      options: parentCategories,
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
          return `<div>
            <span class="badge bg-${badgeColor}-lt text-${badgeColor}-fg-lt">${badgeText}</span>
            <span>${escape(data.name)}</span>
          </div>`;
        },
      },
    });

    document.getElementById('formEdit').addEventListener("reset", (event) => {
      parentSelect.setValue(@js($category->parent_id));
    });

    parentSelect.on('change', () => {
      syncParent();
    });

    typeSelect.addEventListener('change', (event) => {
      syncParentOptions();
    });

    syncParent();
    syncParentOptions();

    function syncParent() {
      const parentValue = Number(parentSelect.getValue()) || null;
      const parentValueType = parentCategories.find(cat => cat.id === parentValue)?.type;

      if (parentValue && (!typeSelect.value || typeSelect.value !== parentValueType)) {
        typeSelect.value = parentValueType;
        typeSelect.dispatchEvent(new Event('change'));
      }
    }

    function syncParentOptions() {
      const type = typeSelect.value;
      const parentValue = Number(parentSelect.getValue()) || null;
      const parentValueType = parentCategories.find(cat => cat.id === parentValue)?.type;

      if (parentValue && parentValueType !== type) {
        parentSelect.clear();
      }

      parentSelect.clearOptions();
      const filteredCategories = type ? parentCategories.filter(cat => cat.type === type) : parentCategories;
      parentSelect.addOptions(filteredCategories);
    }
  </script>
@endsection
