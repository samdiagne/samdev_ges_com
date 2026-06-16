@extends('layouts.shop')
@section('title', 'Commande confirmée — ' . $commande->reference)

@section('content')
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
        Commande confirmée ! 🎉
    </h1>
    <p class="text-gray-500 mb-2">
        Référence : <span class="font-bold text-primary">
            {{ $commande->reference }}
        </span>
    </p>
    <p class="text-gray-500 mb-8">
        Nous vous contacterons au
        <span class="font-semibold">{{ $commande->telephone_client }}</span>
        pour organiser la livraison.
    </p>

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
                        {{ $ligne->nom_plat }}
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
            <span>Total HT</span>
            <span class="text-primary">
                {{ number_format($commande->total, 0, ',', ' ') }} FCFA
            </span>
        </div>

        @if($commande->mode_paiement === 'carte')
            <div class="mt-3 bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-800 text-left">
                <p class="font-semibold mb-2">💳 Instructions de paiement par carte bancaire</p>
                <p class="text-xs text-blue-700 mb-2">
                    Un agent vous contactera pour traiter votre paiement par carte bancaire.
                    Munissez-vous de votre carte et de la référence commande <strong>{{ $commande->reference }}</strong>.
                </p>
                @if($settings->whatsapp || $settings->telephone)
                    <p class="text-xs text-blue-700">
                        Vous serez contacté par
                        @if($settings->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->whatsapp) }}"
                               target="_blank" class="underline font-medium">WhatsApp</a>
                        @endif
                        après confirmation de votre commande.
                    </p>
                @endif
            </div>
        @else
            <div class="mt-3 bg-orange-50 border border-orange-100 rounded-xl p-3 text-xs text-orange-700">
                📦 Les frais de livraison vous seront communiqués par notre service client après validation de votre commande.
            </div>
        @endif

        <div class="mt-4 pt-4 border-t border-gray-100 space-y-2 text-sm">
            <div class="flex justify-between text-gray-500">
                <span>Mode de paiement</span>
                <span class="font-medium capitalize">
                    {{ match($commande->mode_paiement) {
                        'cash'     => 'Espèces',
                        'wave'     => 'Wave',
                        'om'       => 'Orange Money',
                        'carte'    => 'Carte bancaire',
                        'paydunya' => 'Paiement en ligne (PayDunya)',
                        default    => ucfirst($commande->mode_paiement ?? '—'),
                    } }}
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
        @if($settings->whatsapp)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->whatsapp) }}?text={{ urlencode('Bonjour, j\'ai passé la commande ' . $commande->reference . ' et j\'aimerais avoir des informations sur la livraison.') }}"
               target="_blank"
               class="px-6 py-3 bg-green-500 hover:bg-green-400 text-white
                      rounded-2xl text-sm font-semibold transition-all">
                Suivre sur WhatsApp
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