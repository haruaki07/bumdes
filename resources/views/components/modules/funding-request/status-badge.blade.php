@props(['status'])

@php
  use App\Enums\FundingRequestStatus;
  $statusEnum = is_string($status) ? FundingRequestStatus::fromString($status) : $status;
@endphp

<x-common.badge :color="$statusEnum->color()" :label="$statusEnum->label()" />
