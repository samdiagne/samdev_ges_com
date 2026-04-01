@extends('layouts.shop')
@section('title', 'Mon profil')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-dark">Mon espace client</h1>
        <p class="text-sm text-gray-500 mt-1">
            Bonjour <span class="font-semibold text-dark">{{ auth()->user()->name }}</span>
        </p>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 bg-gray-100 p-1 rounded-xl mb-8 w-fit">
        <a href="{{ route('compte.commandes') }}"
           class="px-4 py-2 rounded-lg text-sm font-medium text-gray-500 hover:text-dark transition-colors">
            Mes commandes
        </a>
        <a href="{{ route('compte.profil') }}"
           class="px-4 py-2 rounded-lg text-sm font-medium bg-white text-primary shadow-sm">
            Mon profil
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Informations personnelles --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-bold text-dark mb-5 flex items-center gap-2">
                <span class="w-6 h-6 bg-primary/10 rounded-lg flex items-center justify-center text-primary text-xs font-bold">1</span>
                Informations personnelles
            </h2>

            @if(session('success'))
                <div class="bg-green-50 border border-green-100 rounded-xl px-4 py-3 mb-5 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('compte.profil.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-dark mb-1.5">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none transition-colors
                                  {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 focus:border-primary' }}"/>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark mb-1.5">Adresse email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none transition-colors
                                  {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 focus:border-primary' }}"/>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark mb-1.5">
                        Téléphone
                        <span class="text-gray-400 font-normal">(optionnel)</span>
                    </label>
                    <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                           placeholder="+221 XX XXX XX XX"
                           class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 rounded-xl text-sm
                                  focus:outline-none focus:border-primary transition-colors"/>
                </div>

                <button type="submit"
                        class="w-full bg-primary hover:bg-primary/90 text-white font-semibold py-2.5
                               rounded-xl transition-all text-sm shadow-sm shadow-primary/25">
                    Mettre à jour
                </button>
            </form>
        </div>

        {{-- Changer le mot de passe --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-bold text-dark mb-5 flex items-center gap-2">
                <span class="w-6 h-6 bg-primary/10 rounded-lg flex items-center justify-center text-primary text-xs font-bold">2</span>
                Changer le mot de passe
            </h2>

            <form action="{{ route('compte.profil.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-dark mb-1.5">Mot de passe actuel</label>
                    <input type="password" name="current_password" placeholder="••••••••"
                           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none transition-colors
                                  {{ $errors->has('current_password') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 focus:border-primary' }}"/>
                    @error('current_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark mb-1.5">Nouveau mot de passe</label>
                    <input type="password" name="password" placeholder="Minimum 8 caractères"
                           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none transition-colors
                                  {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 focus:border-primary' }}"/>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark mb-1.5">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" placeholder="••••••••"
                           class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 rounded-xl text-sm
                                  focus:outline-none focus:border-primary transition-colors"/>
                </div>

                <button type="submit"
                        class="w-full bg-dark hover:bg-dark/90 text-white font-semibold py-2.5
                               rounded-xl transition-all text-sm">
                    Changer le mot de passe
                </button>
            </form>
        </div>

    </div>

    {{-- Zone danger --}}
    <div id="danger" class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-bold text-dark mb-4">Zone de danger</h2>

        <div class="flex flex-col sm:flex-row gap-3">

            {{-- Déconnexion --}}
            <form method="POST" action="{{ route('compte.logout') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200
                               text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Se déconnecter
                </button>
            </form>

            {{-- Supprimer le compte --}}
            <div x-data="{ open: {{ $errors->has('delete_password') ? 'true' : 'false' }} }">
                <button type="button" @click="open = true"
                        class="inline-flex items-center gap-2 px-5 py-2.5 border border-red-200
                               text-red-600 rounded-xl text-sm font-medium hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5
                                 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer mon compte
                </button>

                {{-- Modal confirmation --}}
                <div x-show="open" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                     x-transition:enter="transition duration-200 ease-out"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition duration-150 ease-in"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">

                    <div @click.outside="open = false"
                         class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6"
                         x-transition:enter="transition duration-200 ease-out"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100">

                        {{-- Icône --}}
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center
                                    justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                                         1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
                                         0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>

                        <h3 class="text-lg font-bold text-dark text-center mb-1">
                            Supprimer mon compte
                        </h3>
                        <p class="text-sm text-gray-500 text-center mb-5">
                            Cette action est <strong>irréversible</strong>. Toutes vos données
                            seront définitivement supprimées. Saisissez votre mot de passe pour confirmer.
                        </p>

                        <form method="POST" action="{{ route('compte.delete') }}">
                            @csrf
                            @method('DELETE')

                            <div class="mb-4">
                                <input type="password" name="password"
                                       placeholder="Votre mot de passe"
                                       autofocus
                                       class="w-full px-4 py-2.5 border rounded-xl text-sm
                                              focus:outline-none transition-colors
                                              {{ $errors->has('delete_password')
                                                 ? 'border-red-400 bg-red-50'
                                                 : 'border-gray-200 bg-gray-50 focus:border-red-400' }}"/>
                                @error('delete_password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex gap-3">
                                <button type="button" @click="open = false"
                                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600
                                               rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors">
                                    Annuler
                                </button>
                                <button type="submit"
                                        class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700
                                               text-white rounded-xl text-sm font-semibold
                                               transition-colors">
                                    Supprimer définitivement
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-xs text-gray-400 mt-4">
            La suppression de votre compte entraîne la perte définitive de vos données et de votre historique de commandes.
        </p>
    </div>

</div>
@endsection
