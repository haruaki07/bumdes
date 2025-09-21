<x-e-billing::layouts.panel>
  @push('css')
    <style>
      .form-check {
        margin-bottom: 0;
      }

      /* legacy */
      .perm-matrix th,
      .perm-matrix td {
        vertical-align: middle;
      }

      .perm-matrix th:first-child {
        width: 220px;
      }

      .perm-matrix .text-center .form-check {
        justify-content: center;
      }
    </style>
  @endpush

  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            Pengaturan
          </div>
          <h2 class="page-title">
            Tambah Role
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <a href="{{ route('e-billing.settings.roles.index') }}" class="btn btn-secondary">Kembali</a>
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
              <h3 class="card-title">Informasi Role</h3>
            </div>
            <form action="{{ route('e-billing.settings.roles.store') }}" method="POST">
              @csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label class="form-label required">Nama</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                      value="{{ old('name') }}" required>
                    @error('name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-8 mb-3">
                    <label class="form-label">Deskripsi</label>
                    <input type="text" name="description"
                      class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}">
                    @error('description')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <label class="form-label">Akses</label>
                <div class="form-text">Berikan akses yang sesuai untuk role ini.</div>
              </div>


              @php
                $grouped = $permissions
                    ->sortBy(function ($item) use ($groupOrder) {
                        return array_search($item->menu_group, $groupOrder);
                    })
                    ->groupBy('menu');
              @endphp

              <div class="table-responsive">
                <table class="table table-vcenter card-table perm-matrix">
                  <thead>
                    <tr>
                      <th>Menu</th>
                      @foreach ($actions as $action)
                        <th class="text-center">{{ Str::upper($action) }}</th>
                      @endforeach
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($grouped as $menu => $permissions)
                      <tr data-group="{{ $menu }}">
                        <td class="fw-bold">{{ $menu }}</td>

                        @foreach ($actions as $action)
                          @php
                            $permission = $permissions->first(fn($perm) => Str::startsWith($perm->name, $action));
                          @endphp
                          <td class="text-center">
                            @if ($permission)
                              <input type="checkbox" class="form-check-input cell-checkbox" name="permissions[]"
                                value="{{ $permission->name }}" data-group="{{ $menu }}"
                                @checked(in_array($permission->name, old('permissions', []))) data-bs-toggle="tooltip"
                                title="{{ $permission->description ?? '' }}" data-bs-placement="top"
                                data-bs-trigger="hover" />
                            @else
                              <span class="text-muted">-</span>
                            @endif
                          </td>
                        @endforeach
                        <td>
                          <div class="dropdown">
                            <button class="btn btn-action" type="button" data-bs-toggle="dropdown"
                              aria-expanded="false" tabindex="-1">
                              <i class="ti ti-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                              <li><button class="dropdown-item btn-toggle-row" type="button"
                                  data-group="{{ $menu }}" data-toggle="true">Pilih Semua</button></li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('js')
    <script type="module">
      const cellSelector = 'input.cell-checkbox';
      const btnToggleRowSelector = '.btn-toggle-row';

      function updateButtonToggleState(btn, toggle) {
        if (toggle) {
          btn.dataset.toggle = 'false';
          btn.textContent = 'Hapus semua';
        } else {
          btn.dataset.toggle = 'true';
          btn.textContent = 'Pilih semua';
        }
      }

      document.querySelectorAll(cellSelector).forEach(cell => {
        cell.addEventListener('change', (e) => {
          const menu = cell.dataset.group;
          const allChecked = Array.from(document.querySelectorAll(`${cellSelector}[data-group="${menu}"]`))
            .every(c => c.checked);
          const btn = document.querySelector(`${btnToggleRowSelector}[data-group="${menu}"]`);

          updateButtonToggleState(btn, allChecked);
        });
      });

      document.querySelectorAll(btnToggleRowSelector).forEach(btn => {
        btn.addEventListener('click', (e) => {
          const menu = btn.dataset.group;
          const toggleCheck = btn.dataset.toggle === 'true';

          document.querySelectorAll(`${cellSelector}[data-group="${menu}"]`)
            .forEach(cell => {
              cell.checked = toggleCheck;
            });

          updateButtonToggleState(btn, toggleCheck);
        });
      });
    </script>
  @endpush
</x-e-billing::layouts.panel>
