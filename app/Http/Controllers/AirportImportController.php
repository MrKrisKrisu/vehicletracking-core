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
        $validated = $request->validate([
                                            'date' => ['required', 'date'],
                                            'file' => ['required', 'file']
                                        ]);

        $scanDevice = self::getScanDevice();

        $content = $validated['file']->get();
        $csv     = Reader::createFromString($content);
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(',');
        $ok = 0;
        foreach($csv->getRecords() as $record) {
            if(!isset($record[' BSS']) || !isset($record[' Time'])) {
                continue;
            }
            $device = Device::updateOrCreate([
                                                 'bssid' => $record[' BSS'],
                                             ], [
                                                 'ssid'     => $record['SSID'] ?? null,
                                                 'lastSeen' => Carbon::parse($validated['date'] . ' ' . $record[' Time'])->toIso8601String(),
                                             ]);

            IgnoredNetworkController::checkIfDeviceShouldBeHidden($device);
            
            Scan::create([
                             'bssid'        => $record[' BSS'],
                             'ssid'         => $record['SSID'] ?? null,
                             'signal'       => $record[' RSSI'] ?? null,
                             'channel'      => $record[' Channel'] ?? null,
                             'scanDeviceId' => $scanDevice->id,
                             'created_at'   => Carbon::parse($validated['date'] . ' ' . $record[' Time'])->toIso8601String(),
                         ]);
            $ok++;
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
