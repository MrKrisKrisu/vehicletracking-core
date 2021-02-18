<?php

namespace App\Http\Controllers\API\v1;

use App\Device;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Scan;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class MapController extends Controller {

    public function getNetworksByBbox(Request $request): Response {

        $validated = $request->validate([
                                            'minLat' => ['required', 'numeric'],
                                            'minLon' => ['required', 'numeric'],
                                            'maxLat' => ['required', 'numeric'],
                                            'maxLon' => ['required', 'numeric'],
                                        ]);

        $networks = Scan::join('devices', 'devices.bssid', '=', 'scans.bssid')
                        ->groupBy(['devices.id', 'devices.ssid'])
                        ->having(DB::raw('AVG(scans.latitude)'), '<', $validated['maxLat'])
                        ->having(DB::raw('AVG(scans.latitude)'), '>', $validated['minLat'])
                        ->having(DB::raw('AVG(scans.longitude)'), '<', $validated['maxLon'])
                        ->having(DB::raw('AVG(scans.longitude)'), '>', $validated['minLon'])
                        ->whereNotNull('scans.latitude')
                        ->whereNotNull('scans.longitude')
                        ->select([
                                     'devices.id',
                                     DB::raw('CONCAT(SUBSTRING(devices.ssid, 1, 4), "******") AS name'),
                                     DB::raw('AVG(scans.latitude) AS lat'),
                                     DB::raw('AVG(scans.longitude) AS lon')
                                 ])
                        ->limit(5001)
                        ->get();

        if($networks->count() > 5000)
            return response(['error' => 'too many networks in the bbox. Please choose a smaller bbox.'], 406);

        return response($networks);

    }
}
