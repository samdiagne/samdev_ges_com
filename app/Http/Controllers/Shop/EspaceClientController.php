<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;

class EspaceClientController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('shop.compte.commandes', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        $order->load('items');

        return view('shop.compte.commande', compact('order'));
    }

    public function profil()
    {
        return view('shop.compte.profil');
    }

    public function updateProfil(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required'  => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique'   => 'Cette adresse email est déjà utilisée.',
        ]);

        $user->update($request->only('name', 'email', 'phone'));

        return back()->with('success', 'Votre profil a été mis à jour.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'password.required'         => 'Le nouveau mot de passe est obligatoire.',
            'password.confirmed'        => 'Les mots de passe ne correspondent pas.',
            'password.min'              => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.'])
                ->with('tab', 'password');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Mot de passe mis à jour avec succès.');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => ['required'],
        ], [
            'password.required' => 'Veuillez saisir votre mot de passe pour confirmer.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['delete_password' => 'Mot de passe incorrect.'])
                ->withFragment('danger');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect()->route('shop.home')
            ->with('success', 'Votre compte a été supprimé. À bientôt !');
    }
}
