<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller {

    public function renderDashboard(): View {
        return view('user.dashboard');
    }
}
