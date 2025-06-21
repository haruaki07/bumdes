@props(['status'])

@php
use App\Enums\BusinessStatus;
$statusEnum = BusinessStatus::fromString($status);
@endphp

<x-common.badge :color="$statusEnum->color()" :label="$statusEnum->label()" /> 