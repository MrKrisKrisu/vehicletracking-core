<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Vehicle;

class VehiclePolicy {
    use HandlesAuthorization;

    public function view(User $user, Vehicle $vehicle): bool {
        return true;
    }

    public function create(User $user): bool {
        return $user->id === 1;
    }

    public function update(User $user, Vehicle $vehicle): bool {
        return $user->id === 1;
    }

    public function delete(User $user, Vehicle $vehicle): bool {
        return false;
    }
}
