<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
}
