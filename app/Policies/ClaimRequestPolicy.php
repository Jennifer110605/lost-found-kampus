<?php

namespace App\Policies;

use App\Models\ClaimRequest;
use App\Models\User;

class ClaimRequestPolicy
{
    public function update(User $user, ClaimRequest $claim): bool
    {
        return $user->id === $claim->user_id;
    }
}
