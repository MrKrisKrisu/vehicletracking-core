<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Csv\Reader;
use App\ScanDevice;
use Illuminate\Support\Facades\DB;
use App\Scan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use App\Device;

class AirportImportController extends Controller {

    public function import(Request $request): RedirectResponse {
        $this->authorize('create', Scan::class);

        $validated = $request->validate([
                                            'date'         => ['required', 'date'],
                                            'location'     => ['required', 'string'],
                                            'vehicle_name' => ['required', 'string'],
                                            'file'         => ['required', 'file'],
                                        ]);

        $scanDevice = self::getScanDevice();

        $vehicleName = $validated['location'] . ',' . $validated['vehicle_name'];

        $content = $validated['file']->get();
        $csv     = Reader::createFromString($content);
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(',');
        $ok = 0;

        $foundVehicles = collect();
        foreach($csv->getRecords() as $record) {
            if(!isset($record[' BSS']) || !isset($record[' Time'])) {
                continue;
            }
            $date = Carbon::parse($validated['date'] . ' ' . $record[' Time'])->toIso8601String();

            $device = Device::updateOrCreate([
                                                 'bssid' => $record[' BSS'],
                                             ], [
                                                 'ssid'     => $record['SSID'] ?? null,
                                                 'lastSeen' => Carbon::parse($validated['date'] . ' ' . $record[' Time'])->toIso8601String(),
                                             ]);

            if($device->blocked) {
                continue;
            }

            IgnoredNetworkController::checkIfDeviceShouldBeHidden($device);

            $scan = Scan::create([
                                     'bssid'        => $record[' BSS'],
                                     'vehicle_name' => $vehicleName,
                                     'ssid'         => $record['SSID'] ?? null,
                                     'signal'       => $record[' RSSI'] ?? null,
                                     'channel'      => $record[' Channel'] ?? null,
                                     'scanDeviceId' => $scanDevice->id,
                                     'created_at'   => $date,
                                     'updated_at'   => $date,
                                 ]);

            if(isset($scan->device->vehicle) && !$foundVehicles->contains($scan->device->vehicle)) {
                $foundVehicles->push($scan->device->vehicle);
            }

            $ok++;
        }
        if($foundVehicles->count() > 0) {
            $request->session()->flash('alert-info', 'Glückwunsch! Du hast ' . $foundVehicles->count() . ' verifizierte Fahrzeuge gefunden! -> ' . implode(', ', $foundVehicles->pluck('vehicle_name')->toArray()));
        }

        return back()->with('alert-success', 'Es wurden ' . $ok . ' Scans importiert.');
    }

    private static function getScanDevice(): ScanDevice {
        $device = ScanDevice::where('user_id', auth()->user()->id)->where('name', 'Airport Utility Import')->first();

        if($device != null) {
            return $device;
        }

        return ScanDevice::create([
                                      'user_id' => auth()->user()->id,
                                      'name'    => 'Airport Utility Import',
                                      'token'   => DB::raw('UUID()'),
                                  ]);
    }
}
