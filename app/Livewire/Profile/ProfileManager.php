<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileManager extends Component
{
    public string $name     = '';
    public string $email    = '';
    public string $phone    = '';
    public string $whatsapp = '';

    public string $current_password      = '';
    public string $password              = '';
    public string $password_confirmation = '';

    public string $ongletActif = 'infos';
    public bool   $sauvegarde  = false;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name     = $user->name;
        $this->email    = $user->email;
        $this->phone    = $user->phone ?? '';
        $this->whatsapp = $user->whatsapp ?? '';
    }

    public function saveInfos(): void
    {
        $this->validate([
            'name'     => 'required|string|min:2|max:255',
            'email'    => 'required|email|unique:users,email,' . auth()->id(),
            'phone'    => 'nullable|string|max:100',
            'whatsapp' => 'nullable|string|max:100',
        ]);

        auth()->user()->update([
            'name'     => $this->name,
            'email'    => $this->email,
            'phone'    => $this->phone ?: null,
            'whatsapp' => $this->whatsapp ?: null,
        ]);

        $this->showSaved();
    }

    public function savePassword(): void
    {
        $this->validate([
            'current_password'      => 'required|current_password',
            'password'              => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.confirmed'                => 'Les mots de passe ne correspondent pas.',
            'password.min'                      => 'Le nouveau mot de passe doit faire au moins 8 caractères.',
        ]);

        auth()->user()->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->showSaved();
    }

    private function showSaved(): void
    {
        $this->sauvegarde = true;
        $this->dispatch('flash-saved');
    }

    public function render()
    {
        return view('livewire.profile.profile-manager');
    }
}
