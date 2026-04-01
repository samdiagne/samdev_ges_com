@extends('layouts.auth')

@section('title', 'Mot de passe oublié')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-dark mb-1.5">Mot de passe oublié ?</h1>
        <p class="text-sm text-gray-400">Saisissez votre email, nous vous enverrons un lien de réinitialisation.</p>
    </div>

    {{-- Message de succès --}}
    @if (session('status'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl
                    flex items-center gap-2 text-green-700 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9
                         10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-dark mb-1.5">
                Adresse email
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005
                                 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </div>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autofocus
                       placeholder="votre@email.com"
                       class="w-full pl-10 pr-4 py-2.5 border rounded-xl text-sm
                              transition-colors duration-200 outline-none
                              {{ $errors->has('email')
                                 ? 'border-red-400 bg-red-50 focus:border-red-500'
                                 : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
            </div>
            @error('email')
                <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1
                                 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                              clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full bg-primary hover:bg-primary/90 text-white font-semibold
                       py-2.5 px-4 rounded-xl transition-all duration-200
                       flex items-center justify-center gap-2 shadow-lg
                       shadow-primary/25 hover:shadow-primary/40 mt-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0
                         00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Envoyer le lien
        </button>
    </form>

    {{-- Retour connexion --}}
    <p class="text-center text-sm text-gray-500 mt-6">
        <a href="{{ route('login') }}"
           class="text-primary hover:text-primary/80 font-medium transition-colors
                  inline-flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 19l-7-7 7-7"/>
            </svg>
            Retour à la connexion
        </a>
    </p>
@endsection
