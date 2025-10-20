<?php

namespace App\Policies;

use App\Models\Log;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LogPolicy
{
    public function modify(User $user, Log $log): Response
    {
        return $user->id === $log->user_id
            ? Response::allow()
            : Response::deny('You do not own this log');
    }
}
