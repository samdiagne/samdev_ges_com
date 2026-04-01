<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthClientController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            return redirect()->route('shop.home');
        }
        return redirect()->route('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'L\'email est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        if (!Auth::attempt([
            'email'    => $request->email,
            'password' => $request->password,
        ], $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email ou mot de passe incorrect.',
            ])->withInput($request->only('email'));
        }

        $user = Auth::user();

        // Bloquer les admins/gestionnaires — ils ont leur propre login
        if ($user->hasAnyRole(['admin', 'gestionnaire'])) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Utilisez le portail administrateur.',
            ]);
        }

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Votre compte est désactivé.',
            ]);
        }

        $request->session()->regenerate();

        // Ne pas utiliser intended() : évite la redirection vers des routes admin
        $intended = session()->pull('url.intended', '');
        $safePath = parse_url($intended, PHP_URL_PATH) ?? '';
        $destination = str_starts_with($safePath, '/compte/')
            ? $intended
            : route('compte.commandes');

        return redirect($destination)
            ->with('success', 'Bienvenue ' . $user->name . ' !');
    }

    public function registerForm()
    {
        if (Auth::check()) {
            return redirect()->route('shop.home');
        }
        return view('shop.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|min:2|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|min:8|max:20',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.required'      => 'Votre nom est obligatoire.',
            'email.required'     => 'L\'email est obligatoire.',
            'email.unique'       => 'Cet email est déjà utilisé.',
            'phone.required'     => 'Votre téléphone est obligatoire.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.min'       => 'Le mot de passe doit faire au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password),
            'is_active' => true,
        ]);

        $user->assignRole('client');

        // Créer la fiche client dans le back-office
        Customer::create([
            'user_id' => $user->id,
            'name'    => $user->name,
            'email'   => $user->email,
            'phone'   => $user->phone,
            'type'    => 'individual',
        ]);

        Auth::login($user);

        return redirect()->route('compte.commandes')
            ->with('success', 'Compte créé avec succès ! Bienvenue ' . $user->name . ' !');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('shop.home')
            ->with('success', 'Déconnexion réussie.');
    }
}