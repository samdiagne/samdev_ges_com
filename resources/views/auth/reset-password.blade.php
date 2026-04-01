@extends('layouts.auth')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-dark mb-1.5">Nouveau mot de passe</h1>
        <p class="text-sm text-gray-400">Choisissez un mot de passe sécurisé pour votre compte.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        {{-- Token --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                       value="{{ old('email', $request->email) }}"
                       required
                       autofocus
                       autocomplete="username"
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

        {{-- Nouveau mot de passe --}}
        <div x-data="{ showPassword: false }">
            <label class="block text-sm font-medium text-dark mb-1.5">
                Nouveau mot de passe
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0
                                 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input :type="showPassword ? 'text' : 'password'"
                       name="password"
                       required
                       autocomplete="new-password"
                       placeholder="••••••••"
                       class="w-full pl-10 pr-10 py-2.5 border rounded-xl text-sm
                              transition-colors duration-200 outline-none
                              {{ $errors->has('password')
                                 ? 'border-red-400 bg-red-50 focus:border-red-500'
                                 : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                <button type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <svg x-show="!showPassword" class="w-4 h-4" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943
                                 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477
                                 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="showPassword" class="w-4 h-4" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478
                                 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3
                                 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88
                                 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59
                                 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943
                                 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password')
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

        {{-- Confirmation --}}
        <div x-data="{ showConfirm: false }">
            <label class="block text-sm font-medium text-dark mb-1.5">
                Confirmer le mot de passe
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955
                                 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824
                                 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <input :type="showConfirm ? 'text' : 'password'"
                       name="password_confirmation"
                       required
                       autocomplete="new-password"
                       placeholder="••••••••"
                       class="w-full pl-10 pr-10 py-2.5 border rounded-xl text-sm
                              transition-colors duration-200 outline-none
                              {{ $errors->has('password_confirmation')
                                 ? 'border-red-400 bg-red-50 focus:border-red-500'
                                 : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                <button type="button"
                        @click="showConfirm = !showConfirm"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <svg x-show="!showConfirm" class="w-4 h-4" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943
                                 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477
                                 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="showConfirm" class="w-4 h-4" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478
                                 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3
                                 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88
                                 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59
                                 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943
                                 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password_confirmation')
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
                      d="M5 13l4 4L19 7"/>
            </svg>
            Réinitialiser le mot de passe
        </button>
    </form>
@endsection
