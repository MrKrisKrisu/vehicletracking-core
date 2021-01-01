<?php

namespace App\Http\Controllers;

use App\Device;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class MapController extends Controller {
    public function renderMap() {
        $positions = self::getVehiclePositions();
        return view('map.main', [
            'positions' => $positions
        ]);
    }

    public static function getVehiclePositions() {
        return collect(DB::select("SELECT vehicles.id AS vehicle_id,vehicles.company_id,vehicles.vehicle_name,scans.latitude,scans.longitude,MAX(scans.created_at) AS timestamp FROM `scans` 
                                        INNER JOIN (SELECT bssid,MAX(created_at) AS created_at FROM `scans` WHERE latitude IS NOT NULL AND longitude IS NOT NULL GROUP BY bssid) n ON scans.bssid = n.bssid AND scans.created_at = n.created_at
                                        INNER JOIN devices ON devices.bssid = scans.bssid
                                        INNER JOIN vehicles ON devices.vehicle_id = vehicles.id
                                        WHERE vehicles.company_id != 4 AND scans.created_at > (NOW() - INTERVAL 14 DAY)
                                        GROUP BY vehicles.company_id,vehicles.vehicle_name,scans.latitude,scans.longitude,scans.created_at")
        )->groupBy('vehicle_id')
         ->map(function($scans) {
             return $scans->sortByDesc('timestamp')->first();
         });
    }

    public function renderSitemap(): Response|Application|ResponseFactory {
        $sitemap = SitemapGenerator::create(config('app.url'))
                                   ->getSitemap();

        $vehicles = Device::where('vehicle_id', '<>', null)->groupBy('vehicle_id')->select('vehicle_id')->pluck('vehicle_id');

        foreach($vehicles as $vehicle) {
            $sitemap->add(Url::create('/vehicle/' . $vehicle)
                             ->setLastModificationDate(Carbon::yesterday())
                             ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                             ->setPriority(0.5));
        }

        return response($sitemap->render(), 200, [
            'Content-Type' => 'application/xml'
        ]);
    }
}
