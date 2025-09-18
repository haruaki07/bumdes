@props(['status'])
<x-common.badge :color="$status->color()" :label="$status->label()" />
