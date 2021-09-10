<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Rules\MatchOldPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SettingsController extends Controller {

    public function renderSettings(): View {
        return view('user.settings');
    }

    public function changePassword(Request $request): RedirectResponse {
        $validated = $request->validate([
                                            'current_password'     => ['required', new MatchOldPassword()],
                                            'new_password'         => ['required',],
                                            'new_confirm_password' => ['same:new_password'],
                                        ]);

        Auth::user()->update(['password' => Hash::make($validated['new_password'])]);

        return back()->with('alert-success', 'Das Passwort wurde geändert.');
    }

    public function saveToSession(Request $request): RedirectResponse {
        $validated = $request->validate([
                                            'show-verified' => ['nullable', 'gte:0', 'lte:1'],
                                            'show-hidden'   => ['nullable', 'gte:0', 'lte:1'],
                                            'show-ignored'   => ['nullable', 'gte:0', 'lte:1'],
                                        ]);

        if(isset($validated['show-verified'])) {
            session()->put('show-verified', $validated['show-verified']);
        }
        if(isset($validated['show-hidden'])) {
            session()->put('show-hidden', $validated['show-hidden']);
        }
        if(isset($validated['show-ignored'])) {
            session()->put('show-ignored', $validated['show-ignored']);
        }

        return back();
    }
}
