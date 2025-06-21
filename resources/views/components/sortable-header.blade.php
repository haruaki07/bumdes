@props([
    'field' => '',
    'label' => '',
    'currentSort' => null,
])

@aware(['tableId'])

@php
    $formId = $tableId;
    if (!$formId) {
        throw new \Exception('<x-sortable-header> requires a tableId prop (passed from <x-datatable>)');
    }

    $sort = $currentSort ?? request()->sort;
    $direction = 'asc';
    $nextDirection = 'asc';

    if ($sort && str_contains($sort, ':')) {
        [$sortField, $direction] = explode(':', $sort, 2);
        $nextDirection = $direction == 'asc' ? 'desc' : null;
    }

    $isCurrentSort = $sort && str_starts_with($sort, $field . ':');
    $sortClass = $isCurrentSort ? ($direction === 'asc' ? ' asc' : ' desc') : '';

    $tooltip = $nextDirection
        ? 'Click to sort ' . ($nextDirection === 'asc' ? 'ascending' : 'descending')
        : 'Click to clear sorting';
@endphp

<th>
    <button class="table-sort{{ $sortClass }} d-flex justify-content-between"
        data-next-direction="{{ $nextDirection }}"
        onclick="datatableFormSubmit('{{ $formId }}', {sort: '{{ $nextDirection ? $field . ':' . $nextDirection : '' }}'})"
        title="{{ $tooltip }}" data-bs-toggle="tooltip" data-bs-placement="top">
        {{ $label }}
    </button>
</th>
