<?php

namespace App\Http\Controllers;

use App\ScanDevice;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\View\View;

class NotificationController extends Controller {

    public function renderNotifications(): View {
        if(auth()->user()->id !== 1) {
            abort(403);
        }
        return view('notifications', [
            'scanDevices' => ScanDevice::where(function($query) {
                $query->where('valid_until', null)
                      ->orWhere('valid_until', '<=', Carbon::now());
            })
                                       ->where('user_id', auth()->user()->id)
                                       ->get()
        ]);
    }

    public function switchNotifications(Request $request): RedirectResponse {
        if(auth()->user()->id !== 1) {
            abort(403);
        }
        $validated = $request->validate([
                                            'id' => ['required', 'exists:scan_devices,id', Rule::in(auth()->user()->scanDevices->pluck('id'))]
                                        ]);

        $scanDevice = ScanDevice::find($validated['id']);
        if($scanDevice->notify) {
            $scanDevice->update(['notify' => 0]);
            $message = 'Benachrichtigungen von Scanner <i>' . $scanDevice->name . '</i> deaktiviert.';
        } else {
            $scanDevice->update(['notify' => 1]);
            $message = 'Benachrichtigungen von Scanner <i>' . $scanDevice->name . '</i> aktiviert.';
        }
        self::notifyRaw($message);
        return back()->with('alert-success', $message);
    }

    public static function notifyRaw(string $html): void {
        try {
            $client = new Client();
            $client->post('https://api.telegram.org/' . config('app.telegram.token') . '/sendMessage', [
                'json' => [
                    'chat_id'    => config('app.telegram.chat'),
                    'text'       => $html,
                    'parse_mode' => 'HTML'
                ]
            ]);
        } catch(Exception|GuzzleException $exception) {
            report($exception);
        }
    }
}
