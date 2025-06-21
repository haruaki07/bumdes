@props([
    'tableId' => null,
    'search' => true,
    'limit' => true,
    'sort' => true,
    'pagination' => true,
    'searchPlaceholder' => 'Search...',
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
        <div class="d-flex">
            @if ($limit)
                <div class="text-muted">
                    Show
                    <div class="mx-2 d-inline-block">
                        <select class="form-select form-select-sm" style="width: 54px;"
                            onchange="datatableFormSubmit('{{ $tableId }}', {limit: this.value})"
                            aria-label="Jumlah data per halaman">
                            @foreach ($limitOptions as $option)
                                <option value="{{ $option }}"
                                    {{ ($currentLimit ?? (request()->limit ?? ($data ? $data->perPage() : 10))) == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    entries
                </div>
            @endif

            @if ($search)
                <div class="ms-auto text-muted">
                    Search:
                    <div class="ms-2 d-inline-block">
                        <input type="text" class="form-control form-control-sm"
                            value="{{ $currentSearch ?? (request()->search ?? '') }}"
                            aria-label="{{ $searchPlaceholder }}" placeholder="{{ $searchPlaceholder }}"
                            onkeydown="event.key === 'Enter' && datatableFormSubmit('{{ $tableId }}', {search: this.value})">
                        <input type="submit" hidden />
                    </div>
                </div>
            @endif
        </div>
    </div>

    <form action="" name="{{ $tableId }}" method="GET">
        @if (request()->has('sort') && $sort)
            <input type="hidden" name="sort" value="{{ $currentSort ?? request()->sort }}">
        @endif
        @if (request()->has('search') && $search)
            <input type="hidden" name="search" value="{{ $currentSearch ?? request()->search }}">
        @endif
        @if (request()->has('limit') && $limit)
            <input type="hidden" name="limit" value="{{ $currentLimit ?? request()->limit }}">
        @endif
        @if (request()->has('page') && $pagination)
            <input type="hidden" name="page" value="{{ $currentPage ?? request()->page }}">
        @endif
    </form>

    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable">
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

@once
    @section('js')
        <script>
            function datatableFormSubmit(formId, values) {
                const form = document.querySelector(`[name="${formId}"]`);
                if (!form) {
                    console.error(`Form with name "${formId}" not found`);
                    return;
                }

                for (const key in values) {
                    let input = form.querySelector(`[name="${key}"]`);
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        form.appendChild(input);
                    }
                    input.value = values[key];
                }
                form.submit();
            }
        </script>
    @stop
@endonce
