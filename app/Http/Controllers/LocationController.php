<?php

namespace App\Http\Controllers;

use App\Scan;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use phpGPX\phpGPX;
use Illuminate\Validation\Rule;

class LocationController extends Controller {

    public function renderOverview(): Renderable {
        return view('location.overview');
    }

    public function importLocations(Request $request): RedirectResponse {
        $validated = $request->validate([
                                            'device_id' => ['required', 'exists:scan_devices,id', Rule::in(auth()->user()->scanDevices->pluck('id'))],
                                            'file'      => ['required', 'file']
                                        ]);


        $gpx  = new phpGPX();
        $file = $gpx->parse($validated['file']->get());

        $count = 0;

        foreach($file->tracks as $track) {
            foreach($track->segments as $segment) {
                foreach($segment->points as $point) {
                    $time  = Carbon::createFromTimestamp($point->time->getTimestamp());
                    $count += Scan::where('scanDeviceId', $validated['device_id'])
                                  ->where('latitude', null)
                                  ->where('longitude', null)
                                  ->where('created_at', '>=', $time->clone()->subSeconds(5))
                                  ->where('created_at', '<=', $time->clone()->addSeconds(5))
                                  ->update([
                                               'latitude'  => $point->latitude,
                                               'longitude' => $point->longitude,
                                           ]);
                }
            }
        }

        return back()->with('alert-success', 'Es wurden ' . $count . ' Scans aktualisiert.');
    }
}
