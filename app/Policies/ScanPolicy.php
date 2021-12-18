<?php

namespace App\Policies;

use App\Scan;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ScanPolicy {

    use HandlesAuthorization;

    public function view(User $user, Scan $scan): bool {
        return $user->id === 1 || ($scan->device !== null && $scan->scanDevice->user_id === $user->id);
    }

    public function create(User $user): bool {
        return true;
    }

    public function update(User $user, Scan $scan): bool {
        return $user->id === 1 || ($scan->device !== null && $scan->scanDevice->user_id === $user->id);
    }

    public function delete(User $user, Scan $scan): bool {
        return false;
    }
}
