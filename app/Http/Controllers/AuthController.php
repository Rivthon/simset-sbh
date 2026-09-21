<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View
    {
        return view('auth.login', [
            'redirect' => $this->safeRedirect($request->query('redirect')),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'redirect' => ['nullable', 'string'],
        ]);

        if ($redirect = $this->safeRedirect($credentials['redirect'] ?? null)) {
            $request->session()->put('url.intended', url($redirect));
        }

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([
            $field => $credentials['login'],
            'password' => $credentials['password'],
            'status' => 'active',
        ], false)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['login' => 'Username/email atau password tidak sesuai.'])
            ->withInput($request->only('login', 'redirect'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function safeRedirect(?string $redirect): ?string
    {
        if (! is_string($redirect) || $redirect === '') {
            return null;
        }

        return str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//')
            ? $redirect
            : null;
    }
}
