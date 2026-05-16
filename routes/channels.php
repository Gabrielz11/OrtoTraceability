<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal de auditoria — admin e auditores
Broadcast::channel('audit', function (User $user) {
    return in_array($user->role, ['admin', 'auditor']);
});

// Canal de operações — admin, instrumentadores e auditores
Broadcast::channel('operations', function (User $user) {
    return in_array($user->role, ['admin', 'instrumentator', 'auditor']);
});
