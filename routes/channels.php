<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function (User $user, mixed $id) {
    return (int) $user->id === (int) $id;
});
