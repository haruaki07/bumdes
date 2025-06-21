@props([
    'color',
    'label'
])

<span class="badge bg-{{ $color }} text-{{ $color }}-fg">
    {{ $label }}
</span> 