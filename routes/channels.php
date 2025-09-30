<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel(
    'Modules.EBilling.Models.User.{id}',
    fn ($user, $id) => (int) $user->id === (int) $id,
    ['guards' => ['ebil']]
);
