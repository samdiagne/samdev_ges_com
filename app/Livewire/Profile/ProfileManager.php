<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileManager extends Component
{
    // Infos
    public string $name     = '';
    public string $email    = '';
    public string $phone    = '';

    // Mot de passe
    public string $current_password  = '';
    public string $new_password      = '';
    public string $confirm_password  = '';

    // UI
    public bool $savedInfo     = false;
    public bool $savedPassword = false;

    public function mount(): void
    {
        $user        = Auth::user();
        $this->name  = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    public function saveInfo(): void
    {
        $this->validate([
            'name'  => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required'  => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique'   => 'Cet email est déjà utilisé.',
        ]);

        Auth::user()->update([
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
        ]);

        $this->savedInfo = true;
        $this->dispatch('info-saved');
    }

    public function savePassword(): void
    {
        $this->validate([
            'current_password' => 'required',
            'new_password'     => ['required', 'min:8', Password::defaults()],
            'confirm_password' => 'required|same:new_password',
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'new_password.required'     => 'Le nouveau mot de passe est obligatoire.',
            'new_password.min'          => 'Le mot de passe doit faire au moins 8 caractères.',
            'confirm_password.same'     => 'Les mots de passe ne correspondent pas.',
        ]);

        // Vérifier le mot de passe actuel
        if (!Hash::check($this->current_password, Auth::user()->password)) {
            $this->addError('current_password', 'Mot de passe actuel incorrect.');
            return;
        }

        Auth::user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->current_password = '';
        $this->new_password     = '';
        $this->confirm_password = '';
        $this->savedPassword    = true;
        $this->dispatch('password-saved');
    }

    public function render()
    {
        return view('livewire.profile.profile-manager');
    }
}