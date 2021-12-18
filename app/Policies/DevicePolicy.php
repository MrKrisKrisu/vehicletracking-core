<?php

namespace App\Policies;

use App\Device;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Scan;

class DevicePolicy {

    use HandlesAuthorization;

    public function view(User $user, Device $device): bool {
        return true;
    }

    public function create(User $user): bool {
        return $user->id === 1;
    }

    public function update(User $user, Device $device): bool {
        return $user->id === 1;
    }

    public function delete(User $user, Scan $device): bool {
        return false;
    }
}
