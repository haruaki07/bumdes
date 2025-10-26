@props([
    'tableId' => null,
    'url' => null,
    'search' => true,
    'limit' => true,
    'sort' => true,
    'pagination' => true,
    'searchPlaceholder' => 'kata kunci',
    'limitOptions' => [10, 25, 50, 100],
    'currentLimit' => null,
    'currentSearch' => null,
    'currentSort' => null,
    'currentPage' => null,
    'data' => null,
    'title' => null,
])

@php
  if (!$tableId) {
      throw new \Exception('<x-datatable> requires a tableId prop');
  }
@endphp

<div class="card">
  <div class="card-header">
    <h3 class="card-title">{{ $title ?? 'Data Table' }}</h3>
  </div>

  <div class="card-body border-bottom py-3">
    {{ $cardHeader ?? '' }}

    <div class="d-flex align-items-center justify-content-sm-between justify-content-center flex-wrap gap-3">
      @if ($limit)
        <div class="text-muted">
          Tampilkan
          <div class="mx-2 d-inline-block">
            <select class="form-select form-select-sm" style="width: 54px;" aria-label="Jumlah data per halaman"
              data-datatable-limit="{{ $tableId }}">
              @if (request()->has('limit') && !in_array(request()->limit, $limitOptions))
                <option value="{{ request()->limit }}" selected hidden>{{ request()->limit }}</option>
              @endif
              @foreach ($limitOptions as $option)
                <option value="{{ $option }}"
                  {{ ($currentLimit ?? (request()->limit ?? ($data ? $data->perPage() : 10))) == $option ? 'selected' : '' }}>
                  {{ $option }}
                </option>
              @endforeach
            </select>
          </div>
          entri
        </div>
      @endif

      @if ($search)
        <div class="d-flex align-items-center justify-content-center flex-wrap gap-3">
          {{ $headerRight ?? '' }}
          <div class="text-muted">
            Cari:
            <div class="ms-2 d-inline-block">
              <input type="text" class="form-control form-control-sm"
                value="{{ $currentSearch ?? (request()->search ?? '') }}" aria-label="{{ $searchPlaceholder }}"
                placeholder="{{ $searchPlaceholder }}" data-datatable-search="{{ $tableId }}">
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>

  <div class="table-responsive">
    <table class="table card-table table-vcenter text-nowrap datatable" data-datatable-id="{{ $tableId }}"
      data-datatable-url="{{ $url ?? request()->url() }}"
      data-datatable-request="{{ json_encode(request()->only(['sort', 'search', 'limit', 'page']), JSON_FORCE_OBJECT) }}">
      <thead>
        {{ $thead }}
      </thead>
      <tbody>
        {{ $tbody }}
      </tbody>
    </table>
  </div>

  @if ($pagination && $data)
    <div class="card-footer d-flex align-items-center">
      {!! $data->links('tablar::pagination') !!}
    </div>
  @endif
</div>


@pushOnce('js')
  <script type="module">
    Datatable.init("[data-datatable-id]");
  </script>
@endpushOnce
