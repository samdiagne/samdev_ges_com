<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte a été désactivé.']);
        }

        if ($user->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('success', 'Bienvenue ' . $user->name . ' !');
        }

        if ($user->isGestionnaire()) {
            return redirect()->route('caisse.pos')
                ->with('success', 'Bienvenue ' . $user->name . ' !');
        }

        $intended    = session()->pull('url.intended', '');
        $safePath    = parse_url($intended, PHP_URL_PATH) ?? '';
        $destination = str_starts_with($safePath, '/compte/')
            ? $intended
            : route('compte.commandes');

        return redirect($destination)
            ->with('success', 'Bienvenue ' . $user->name . ' !');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
