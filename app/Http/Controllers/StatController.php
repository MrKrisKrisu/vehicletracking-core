<?php

namespace App\Http\Controllers;

use App\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatController extends Controller
{
    public static function renderStatpage()
    {

        $d = Device::where('firstSeen', '>', Carbon::now()->addDays(-1))
            ->groupBy(DB::raw("FROM_UNIXTIME( CEILING(UNIX_TIMESTAMP(`firstSeen`)/900)*900)"))
            ->select(DB::raw("FROM_UNIXTIME( CEILING(UNIX_TIMESTAMP(`firstSeen`)/900)*900) AS timestamp"), DB::raw('COUNT(*) as cnt'))
            ->orderBy(DB::raw("timestamp"))
            ->get();

        return view('stats', ['data' => $d]);
    }
}
