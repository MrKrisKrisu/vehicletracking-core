<?php

namespace App\Policies;

use App\Device;
use App\Scan;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DevicePolicy {

    use HandlesAuthorization;

    public function view(User $user, Device $device): bool {
        return true;
    }

    public function create(User $user): bool {
        return $user->id === 1;//TODO
    }

    public function update(User $user, Device $device): bool {
        return $user->id === 1; //TODO
    }

    public function delete(User $user, Scan $device): bool {
        return false;
    }
}
