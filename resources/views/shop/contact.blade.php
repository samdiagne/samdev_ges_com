@extends('layouts.shop')
@section('title', 'Contactez-nous')

@section('content')
@php $settings = App\Models\Parametre::first(); @endphp

{{-- Hero --}}
<div class="relative bg-gradient-to-br from-primary via-primary to-dark overflow-hidden py-12 sm:py-20">
    {{-- Décoration --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-16 -right-16 w-48 sm:w-72 h-48 sm:h-72 bg-white/5 rounded-full"></div>
        <div class="absolute bottom-0 left-0 w-32 sm:w-48 h-32 sm:h-48 bg-secondary/10 rounded-full -translate-x-1/2 translate-y-1/2"></div>
    </div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-1.5 sm:gap-2 bg-white/15 backdrop-blur-sm border border-white/20
                    px-3 sm:px-4 py-1 sm:py-1.5 rounded-full text-xs sm:text-sm font-medium text-white mb-4 sm:mb-6">
            <span class="w-1.5 h-1.5 bg-secondary rounded-full"></span>
            Nous sommes là pour vous
        </div>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3 sm:mb-4">Contactez-nous</h1>
        <p class="text-white/70 text-sm sm:text-lg max-w-xl mx-auto">
            Une question, un devis ou un besoin spécifique ?
            Notre équipe vous répond rapidement.
        </p>
    </div>
</div>

<div class="bg-light">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-16">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 sm:gap-10">

        {{-- ── Formulaire ── --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <h2 class="text-xl font-bold text-dark mb-1">Envoyez-nous un message</h2>
                <p class="text-sm text-gray-500 mb-8">
                    Réponse garantie sous 24h en jours ouvrables.
                </p>

                @if(session('success'))
                    <div class="bg-primary/5 border border-primary/20 rounded-2xl p-5 mb-8 flex items-start gap-4">
                        <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-primary text-sm">Message envoyé !</p>
                            <p class="text-sm text-primary/70 mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-3 mb-6 text-sm text-red-600">
                        <ul class="space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('shop.contact.send') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Votre nom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}"
                                   placeholder="Prénom Nom"
                                   class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none transition-colors
                                          {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 focus:border-primary' }}"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Adresse email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}"
                                   placeholder="votre@email.com"
                                   class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none transition-colors
                                          {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 focus:border-primary' }}"/>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Téléphone <span class="text-gray-400 font-normal">(optionnel)</span>
                            </label>
                            <input type="tel" name="phone" value="{{ old('phone', auth()->user()?->phone) }}"
                                   placeholder="+221 XX XXX XX XX"
                                   class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 rounded-xl text-sm
                                          focus:outline-none focus:border-primary transition-colors"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Sujet <span class="text-red-500">*</span>
                            </label>
                            <select name="subject"
                                    class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none transition-colors
                                           {{ $errors->has('subject') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 focus:border-primary' }}">
                                <option value="">Choisir un sujet</option>
                                <option value="Demande de devis"          {{ old('subject') === 'Demande de devis' ? 'selected' : '' }}>Demande de devis</option>
                                <option value="Question sur un plat"   {{ old('subject') === 'Question sur un plat' ? 'selected' : '' }}>Question sur un plat</option>
                                <option value="Suivi de commande"         {{ old('subject') === 'Suivi de commande' ? 'selected' : '' }}>Suivi de commande</option>
                                <option value="Service après-vente"       {{ old('subject') === 'Service après-vente' ? 'selected' : '' }}>Service après-vente (SAV)</option>
                                <option value="Partenariat"               {{ old('subject') === 'Partenariat' ? 'selected' : '' }}>Partenariat</option>
                                <option value="Autre"                     {{ old('subject') === 'Autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message" rows="5"
                                  placeholder="Décrivez votre demande en détail..."
                                  class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none transition-colors resize-none
                                         {{ $errors->has('message') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 focus:border-primary' }}">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full bg-primary hover:bg-primary/90 text-white font-semibold py-3
                                   rounded-xl transition-all shadow-lg shadow-primary/25 text-sm
                                   flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Envoyer le message
                    </button>
                </form>
            </div>
        </div>

        {{-- ── Infos contact ── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Carte : Adresse --}}
            @if($settings->adresse)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 bg-primary/10 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-dark mb-1">Notre adresse</p>
                            <p class="text-sm text-gray-500 leading-relaxed">{{ $settings->adresse }}</p>
                            <p class="text-sm text-gray-400">Dakar, Sénégal</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Carte : Téléphone --}}
            @if($settings->telephone)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 bg-secondary/10 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-dark mb-1">Téléphone</p>
                            <a href="tel:{{ $settings->telephone }}"
                               class="text-sm text-primary font-semibold hover:underline">
                                {{ $settings->telephone }}
                            </a>
                            <p class="text-xs text-gray-400 mt-1">Lun–Sam · 9h00 – 18h00</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Carte : WhatsApp --}}
            @if($settings->whatsapp)
                @php
                    $wa = preg_replace('/[^0-9]/', '', $settings->whatsapp);
                    $sujets = [
                        ['label' => '🛒 Passer une commande',        'msg' => "Bonjour *{$settings->nom}* 👋\nJe souhaite passer une commande. Pouvez-vous m'aider ?"],
                        ['label' => '📦 Disponibilité d\'un plat',    'msg' => "Bonjour *{$settings->nom}* 👋\nJe voudrais savoir si un plat est disponible."],
                        ['label' => '💰 Demande de prix',             'msg' => "Bonjour *{$settings->nom}* 👋\nJe souhaite avoir des informations sur vos tarifs."],
                        ['label' => '🚚 Infos livraison',             'msg' => "Bonjour *{$settings->nom}* 👋\nJe voudrais des informations sur vos délais et frais de livraison."],
                        ['label' => '🔧 Réclamation',                 'msg' => "Bonjour *{$settings->nom}* 👋\nJ'ai besoin d'assistance concernant ma commande."],
                        ['label' => '❓ Autre question',               'msg' => "Bonjour *{$settings->nom}* 👋\nJ'ai une question à vous poser."],
                    ];
                @endphp

                <div x-data="{ open: false }"
                     class="bg-green-500 text-white rounded-2xl shadow-lg shadow-green-500/25 overflow-hidden">

                    {{-- Bouton principal --}}
                    <button @click="open = !open"
                            class="w-full flex items-center gap-4 p-5 hover:bg-green-600 transition-colors text-left">
                        <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm">Écrire sur WhatsApp</p>
                            <p class="text-white/80 text-xs mt-0.5">Réponse rapide garantie</p>
                        </div>
                        <svg :class="open ? 'rotate-180' : ''"
                             class="w-4 h-4 text-white/70 transition-transform duration-200 shrink-0"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Liste sujets --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="border-t border-white/20 px-3 pb-3 pt-2 space-y-1.5">

                        <p class="text-xs font-semibold text-white/70 px-1 mb-2">Choisissez un sujet :</p>

                        @foreach($sujets as $sujet)
                            <a href="https://wa.me/{{ $wa }}?text={{ urlencode($sujet['msg']) }}"
                               target="_blank"
                               class="flex items-center justify-between gap-2 w-full px-4 py-2.5
                                      bg-white/10 hover:bg-white/20 text-white text-sm font-medium
                                      rounded-xl transition-all group">
                                <span>{{ $sujet['label'] }}</span>
                                <svg class="w-3.5 h-3.5 text-white/50 group-hover:text-white
                                            group-hover:translate-x-1 transition-all shrink-0"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Carte : Email --}}
            @if($settings->email)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 bg-primary/10 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-dark mb-1">Email</p>
                            <a href="mailto:{{ $settings->email }}"
                               class="text-sm text-primary font-semibold hover:underline break-all">
                                {{ $settings->email }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Horaires --}}
            <div class="bg-gradient-to-br from-primary to-primary/80 rounded-2xl p-5 text-white">
                <h3 class="text-sm font-bold mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Horaires d'ouverture
                </h3>
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-white/70">Lundi – Vendredi</span>
                        <span class="font-semibold bg-white/15 px-2 py-0.5 rounded-lg">9h00 – 18h00</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-white/70">Samedi</span>
                        <span class="font-semibold bg-white/15 px-2 py-0.5 rounded-lg">10h00 – 16h00</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-white/70">Dimanche</span>
                        <span class="font-semibold bg-secondary/20 text-secondary px-2 py-0.5 rounded-lg">Fermé</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</div>
@endsection
