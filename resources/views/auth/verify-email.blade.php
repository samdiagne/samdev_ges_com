@extends('layouts.auth')

@section('content')
<div class="text-center mb-6">
    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center
                justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0
                     002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>
    <h2 class="text-xl font-bold text-dark">Vérifiez votre email</h2>
    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
        Un lien de vérification a été envoyé à votre adresse email.
        Cliquez sur le lien pour activer votre compte.
    </p>
</div>

@if(session('status') == 'verification-link-sent')
    <div class="bg-green-50 border border-green-200 rounded-2xl px-4 py-3
                mb-4 text-sm text-green-700 text-center">
        ✅ Un nouveau lien a été envoyé !
    </div>
@endif

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit"
            class="w-full py-3 bg-primary hover:bg-primary/90 text-white
                   font-semibold rounded-2xl transition-all text-sm shadow-lg
                   shadow-primary/25">
        Renvoyer l'email de vérification
    </button>
</form>

<form method="POST" action="{{ route('logout') }}" class="mt-3">
    @csrf
    <button type="submit"
            class="w-full py-3 text-sm font-medium text-gray-500
                   hover:text-red-500 transition-colors">
        Déconnexion
    </button>
</form>
@endsection