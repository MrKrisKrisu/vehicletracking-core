<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller {

    public function render(): View {
        return view('search.search');
    }

    public function search(Request $request): View {
        $validated = $request->validate([
                                            'query'    => ['required', 'min:3', 'max:32'],
                                            'operator' => ['required', Rule::in(['=', '%.', '.%', '%%'])],
                                        ]);

        Log::info(strtr('Searched for ":query" with :operator', [
            ':query'    => $validated['query'],
            ':operator' => $validated['operator'],
        ]));

        $query = DB::table('devices');
        if($validated['operator'] == '=') {
            $query->where('devices.ssid', $validated['query']);
        } elseif($validated['operator'] == '%.') {
            $query->where('devices.ssid', 'LIKE', '%' . $validated['query']);
        } elseif($validated['operator'] == '.%') {
            $query->where('devices.ssid', 'LIKE', $validated['query'] . '%');
        } elseif($validated['operator'] == '%%') {
            $query->where('devices.ssid', 'LIKE', '%' . $validated['query'] . '%');
        } else {
            abort(403);
        }

        $query->join('scans', 'scans.bssid', '=', 'devices.bssid')
              ->whereNotNull('scans.latitude')
              ->whereNotNull('scans.longitude')
              ->groupBy('devices.bssid')
              ->limit(5000)
              ->select([
                           'devices.id',
                           'devices.ssid',
                           DB::raw('AVG(scans.latitude) AS latitudeAvg'),
                           DB::raw('AVG(scans.longitude) AS longitudeAvg'),
                           DB::raw('MIN(scans.latitude) AS latitudeMin'),
                           DB::raw('MIN(scans.longitude) AS longitudeMin'),
                           DB::raw('MAX(scans.latitude) AS latitudeMax'),
                           DB::raw('MAX(scans.longitude) AS longitudeMax'),
                       ]);

        $data = $query->get()->map(function($row) {
            $row->radiusMeter = self::calculateDistanceBetweenCoordinates(
                    latitudeA: $row->latitudeMin,
                    longitudeA: $row->longitudeMin,
                    latitudeB: $row->latitudeMax,
                    longitudeB: $row->longitudeMax
                ) * 1000 / 2;
            return $row;
        });

        return view('search.search', [
            'data' => $data
        ]);
    }

    public static function calculateDistanceBetweenCoordinates(
        float $latitudeA,
        float $longitudeA,
        float $latitudeB,
        float $longitudeB,
        int $decimals = 3
    ): float {
        if($longitudeA === $longitudeB && $latitudeA === $latitudeB) {
            return 0.0;
        }

        $equatorialRadiusInKilometers = 6378.137;

        $pi       = pi();
        $latA     = $latitudeA / 180 * $pi;
        $lonA     = $longitudeA / 180 * $pi;
        $latB     = $latitudeB / 180 * $pi;
        $lonB     = $longitudeB / 180 * $pi;
        $distance = acos(sin($latA) * sin($latB) + cos($latA) * cos($latB) * cos($lonB - $lonA))
                    * $equatorialRadiusInKilometers;

        return round($distance, $decimals);
    }
}
