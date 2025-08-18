@props(['color', 'label'])

<span class="badge @if ($color) bg-{{ $color }}-lt text-{{ $color }}-lt-fg @endif">
  {{ $label }}
</span>
