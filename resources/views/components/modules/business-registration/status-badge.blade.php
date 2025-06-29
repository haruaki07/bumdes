@props(['status'])

@php
    use App\Enums\BusinessRegistrationStatus;
    $statusEnum = BusinessRegistrationStatus::fromString($status);
@endphp

<x-common.badge :color="$statusEnum->color()" :label="$statusEnum->label()" />
