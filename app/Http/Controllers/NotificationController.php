<?php

namespace App\Http\Controllers;

use App\ScanDevice;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller {
    public function renderNotifications(): Renderable {
        return view('notifications', [
            'scanDevices' => ScanDevice::where('valid_until', null)
                                       ->orWhere('valid_until', '<=', Carbon::now())
                                       ->get()
        ]);
    }

    public function switchNotifications(Request $request): RedirectResponse {
        $validated = $request->validate([
                                            'id' => ['required', 'exists:scan_devices,id']
                                        ]);

        $scanDevice = ScanDevice::find($validated['id']);
        if($scanDevice->notify) {
            $scanDevice->update(['notify' => 0]);
            self::notifyRaw('Benachrichtigungen von Scanner <i>' . $scanDevice->name . '</i> deaktiviert.');
        } else {
            $scanDevice->update(['notify' => 1]);
            self::notifyRaw('Benachrichtigungen von Scanner <i>' . $scanDevice->name . '</i> aktiviert.');
        }

        return back();
    }

    public static function notifyRaw(string $html) {
        try {
            $client = new Client();
            $client->post('https://api.telegram.org/' . config('app.telegram.token') . '/sendMessage', [
                'json' => [
                    'chat_id'    => config('app.telegram.chat'),
                    'text'       => $html,
                    'parse_mode' => 'HTML'
                ]
            ]);
        } catch(\Exception | GuzzleException $exception) {
            report($exception);
            dump($html);
            dd($exception);
        }
    }
}
