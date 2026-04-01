@extends('layouts.auth')

@section('content')
<div class="text-center mb-6">
    <div class="w-16 h-16 bg-secondary/10 rounded-2xl flex items-center
                justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0
                     00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
    </div>
    <h2 class="text-xl font-bold text-dark">Confirmer le mot de passe</h2>
    <p class="text-sm text-gray-500 mt-2">
        Zone sécurisée. Confirmez votre mot de passe pour continuer.
    </p>
</div>

<form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
    @csrf
    <div x-data="{ show: false }">
        <label class="block text-sm font-medium text-dark mb-1.5">
            Mot de passe
        </label>
        <div class="relative">
            <input :type="show ? 'text' : 'password'"
                   name="password" required autofocus
                   placeholder="••••••••"
                   class="w-full px-4 py-3 pr-12 border rounded-2xl text-sm
                          focus:outline-none transition-colors
                          {{ $errors->has('password')
                             ? 'border-red-400 bg-red-50'
                             : 'border-gray-200 bg-gray-50
                                focus:border-primary focus:bg-white' }}"/>
            <button type="button" @click="show = !show"
                    class="absolute right-4 top-1/2 -translate-y-1/2
                           text-gray-400 hover:text-gray-600">
                <svg x-show="!show" class="w-4 h-4" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0
                             8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542
                             7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
        @error('password')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit"
            class="w-full py-3 bg-primary hover:bg-primary/90 text-white
                   font-semibold rounded-2xl transition-all text-sm shadow-lg
                   shadow-primary/25">
        Confirmer
    </button>
</form>
@endsection