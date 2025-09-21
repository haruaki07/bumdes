@props(['color' => null, 'label', 'randomize' => false, 'light' => false])

@php
  $colors = ['blue', 'azure', 'indigo', 'purple', 'pink', 'red', 'orange', 'yellow', 'lime', 'green', 'teal', 'cyan'];

  if ($randomize) {
      $color = $colors[crc32($label) % count($colors)];
  } elseif (!$color) {
      $color = 'primary'; // fallback
  }

  $bgClass = $light ? "bg-{$color}-lt text-{$color}-lt-fg" : "bg-{$color} text-{$color}-fg";
@endphp

<span class="badge {{ $bgClass }}">
  {{ $label }}
</span>
