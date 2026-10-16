@extends('layouts.shop')
@section('title', 'Commande enregistrée — ' . $commande->reference)

@section('content')
@php
    $nomBoutique = $parametres->nom ?? $settings->nom ?? 'MAF\'FOOD';
    $waNumber = preg_replace('/[^0-9]/', '', $parametres->whatsapp ?? $parametres->telephone ?? $settings->whatsapp ?? $settings->telephone ?? '');
    
    $msg  = "Bonjour *{$nomBoutique}* 👋\n\n";
    $msg .= "Je viens de valider ma commande sur le site !\n\n";
    $msg .= "📋 *Référence :* {$commande->reference}\n";
    $msg .= "👤 *Client :* {$commande->nom_client}\n";
    $msg .= "📞 *Téléphone :* {$commande->telephone_client}\n";
    $msg .= "📍 *Adresse de livraison :* {$commande->adresse_livraison} ({$commande->ville})\n";
    if ($commande->notes) {
        $msg .= "📝 *Note :* {$commande->notes}\n";
    }
    $msg .= "\n🛒 *Articles commandés :*\n";
    foreach($commande->lignes as $ligne) {
        $msg .= "• {$ligne->nom_produit} ×{$ligne->quantite} — " . number_format($ligne->total, 0, ',', ' ') . " FCFA\n";
    }
    $msg .= "\n💰 *Total : " . number_format($commande->total, 0, ',', ' ') . " FCFA*\n\n";
    $msg .= "Merci de me confirmer la prise en charge et le délai de livraison !";
    $waUrl = $waNumber ? "https://wa.me/{$waNumber}?text=" . urlencode($msg) : null;
@endphp

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 text-center">

    {{-- Icône succès --}}
    <div class="w-18 h-18 sm:w-24 sm:h-24 bg-green-100 rounded-full flex items-center
                justify-center mx-auto mb-5 sm:mb-6" style="width: 72px; height: 72px;">
        <svg class="w-9 h-9 sm:w-12 sm:h-12 text-green-500" fill="none"
             stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="text-2xl sm:text-3xl font-bold text-dark mb-2 sm:mb-3">
        Commande enregistrée ! 🎉
    </h1>
    <p class="text-gray-500 mb-2">
        Référence : <span class="font-bold text-primary font-mono">
            {{ $commande->reference }}
        </span>
    </p>

    @if($waUrl)
        <div class="my-6 p-5 bg-green-50 border-2 border-green-200 rounded-2xl text-left">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 bg-green-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-green-950 mb-1">
                        Dernière étape : Transmettez votre commande sur WhatsApp
                    </h3>
                    <p class="text-xs text-green-800 leading-relaxed">
                        Cliquez sur le bouton ci-dessous pour envoyer le récapitulatif pré-rempli à notre équipe. Nous confirmerons immédiatement votre commande et votre livraison.
                    </p>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ $waUrl }}"
                   target="_blank"
                   id="btn-whatsapp-auto"
                   class="w-full flex items-center justify-center gap-2.5 py-3.5 bg-green-500 hover:bg-green-600 text-white font-bold text-sm sm:text-base rounded-xl shadow-lg shadow-green-500/25 transition-all hover:scale-[1.01]">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    <span>Finaliser ma commande sur WhatsApp</span>
                </a>
            </div>
        </div>
    @else
        <p class="text-gray-500 mb-8">
            Nous vous contacterons au
            <span class="font-semibold">{{ $commande->telephone_client }}</span>
            pour organiser la livraison.
        </p>
    @endif

    {{-- Détail commande --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6
                text-left mb-8">
        <h2 class="text-sm font-bold text-dark mb-4">
            Détail de votre commande
        </h2>

        <div class="space-y-3 mb-4">
            @foreach($commande->lignes as $ligne)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">
                        {{ $ligne->nom_produit }}
                        <span class="text-gray-400">×{{ $ligne->quantite }}</span>
                    </span>
                    <span class="font-semibold">
                        {{ number_format($ligne->total, 0, ',', ' ') }} FCFA
                    </span>
                </div>
            @endforeach
        </div>

        <div class="border-t border-gray-100 pt-3 flex justify-between
                    font-bold text-dark">
            <span>Total</span>
            <span class="text-primary">
                {{ number_format($commande->total, 0, ',', ' ') }} FCFA
            </span>
        </div>

        <div class="mt-3 bg-orange-50 border border-orange-100 rounded-xl p-3 text-xs text-orange-700">
            📦 Les frais de livraison vous seront confirmés par notre service client sur WhatsApp.
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 space-y-2 text-sm">
            <div class="flex justify-between text-gray-500">
                <span>Mode de commande</span>
                <span class="font-medium text-green-600 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    WhatsApp
                </span>
            </div>
            <div class="flex justify-between text-gray-500">
                <span>Livraison à</span>
                <span class="font-medium text-right max-w-48">
                    {{ $commande->ville }}
                </span>
            </div>
            <div class="flex justify-between text-gray-500">
                <span>Statut</span>
                <span class="inline-flex items-center gap-1.5 text-orange-600
                             font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                    {{ $commande->statut_label }}
                </span>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('shop.home') }}"
           class="px-6 py-3 border-2 border-gray-200 rounded-2xl text-sm
                  font-semibold text-gray-600 hover:border-primary
                  hover:text-primary transition-all">
            Retour à l'accueil
        </a>
        @if($waUrl)
            <a href="{{ $waUrl }}"
               target="_blank"
               class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white
                      rounded-2xl text-sm font-semibold transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Ouvrir WhatsApp
            </a>
        @endif
        @auth
            <a href="{{ route('compte.commandes') }}"
               class="px-6 py-3 bg-primary hover:bg-primary/90 text-white
                      rounded-2xl text-sm font-semibold transition-all">
                Mes commandes
            </a>
        @endauth
    </div>
</div>
@endsection