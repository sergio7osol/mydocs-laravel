<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function create(Request $request): View {
        // If a specific return URL is provided, save it as the intended URL
        if ($request->filled('return')) {
            $return = (string) $request->query('return');
            // Allow only same-origin redirects to prevent open-redirects
            if (Str::startsWith($return, url('/'))) {
                $request->session()->put('url.intended', $return);
            }
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('documents.index'))->with('message', 'Welcome back!');
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function destroy(Request $request): RedirectResponse {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')->with('message', 'You have been logged out.');
    }
}
 