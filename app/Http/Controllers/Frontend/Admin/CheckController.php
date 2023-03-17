<?php

namespace App\Http\Controllers\Frontend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Http\Controllers\Backend\Admin\CheckController as CheckBackend;

class CheckController extends Controller {

    public function listVehiclesToCheck(): View {
        $devicesToCheck = CheckBackend::getDevicesToCheck();

        return view('admin.check-list', [
            'devices' => $devicesToCheck,
        ]);
    }
}
