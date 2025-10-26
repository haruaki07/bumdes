@props([
    'field' => '',
    'label' => '',
    'currentSort' => null,
    'class' => '',
])

@aware(['tableId'])

@php
  if (!$tableId) {
      throw new \Exception('<x-sortable-header> requires a tableId prop (passed from <x-datatable>)');
  }

  $sort = $currentSort ?? request()->sort;
  $direction = 'asc';
  $nextDirection = 'asc';

  if ($sort && str_contains($sort, ':')) {
      [$sortField, $direction] = explode(':', $sort, 2);
      if ($sortField === $field) {
          $nextDirection = $direction == 'asc' ? 'desc' : null;
      }
  }

  $isCurrentSort = $sort && str_starts_with($sort, $field . ':');
  $sortClass = $isCurrentSort ? ($direction === 'asc' ? ' asc' : ' desc') : '';

  $tooltip = $nextDirection ? 'Urutkan ' . ($nextDirection === 'asc' ? 'menaik' : 'menurun') : 'Hapus pengurutan';
@endphp

<th @if ($class) class="{{ $class }}" @endif>
  <button class="table-sort{{ $sortClass }} gap-2" type="button"title="{{ $tooltip }}" data-bs-toggle="tooltip"
    data-bs-placement="top" data-datatable-sort="{{ $tableId }}" data-datatable-sort-field="{{ $field }}"
    data-datatable-sort-direction="{{ $direction }}" data-datatable-sort-next-direction="{{ $nextDirection }}">
    {{ $label }}
  </button>
</th>
