@extends('layouts.shop')
@section('title', 'Commande ' . $commande->reference)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Retour --}}
    <a href="{{ route('compte.commandes') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-dark mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour à mes commandes
    </a>

    {{-- Header commande --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-xl font-bold text-dark mb-1">{{ $commande->reference }}</h1>
                <p class="text-sm text-gray-400">
                    Passée le {{ $commande->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-3 py-1.5 rounded-full text-sm font-medium {{ $commande->statut_color }}">
                    {{ $commande->statut_label }}
                </span>
                @if($commande->statut_paiement === 'paye')
                    <span class="px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-700">
                        Paiement reçu
                    </span>
                @elseif($commande->statut_paiement === 'en_attente')
                    <span class="px-3 py-1.5 rounded-full text-sm font-medium bg-secondary/10 text-secondary">
                        Paiement en attente
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Suivi de livraison --}}
    @if($commande->statut !== 'annulee')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-sm font-bold text-dark mb-6">Suivi de votre commande</h2>
            @php
                $steps = [
                    ['key' => 'en_attente', 'label' => 'Commande reçue',    'desc' => 'Votre commande a été enregistrée'],
                    ['key' => 'confirmee',  'label' => 'Confirmée',          'desc' => 'Votre commande a été confirmée par notre équipe'],
                    ['key' => 'expediee',   'label' => 'En livraison',       'desc' => 'Votre commande est en route'],
                    ['key' => 'livree',     'label' => 'Livrée',             'desc' => 'Votre commande a été livrée'],
                ];
                $keys = array_column($steps, 'key');
                $currentStep = array_search($commande->statut, $keys);
            @endphp
            <div class="space-y-4">
                @foreach($steps as $i => $step)
                    @php
                        $done    = $currentStep !== false && $i <= $currentStep;
                        $current = $currentStep !== false && $i === $currentStep;
                    @endphp
                    <div class="flex items-start gap-4">
                        {{-- Indicateur --}}
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0
                                        {{ $done ? 'bg-primary' : 'bg-gray-100' }}">
                                @if($done && !$current)
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @elseif($current)
                                    <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                @else
                                    <div class="w-2.5 h-2.5 bg-gray-300 rounded-full"></div>
                                @endif
                            </div>
                            @if($i < count($steps) - 1)
                                <div class="w-0.5 h-6 {{ $done && !$current ? 'bg-primary' : 'bg-gray-200' }} mt-1"></div>
                            @endif
                        </div>
                        {{-- Texte --}}
                        <div class="pb-1">
                            <p class="text-sm font-semibold {{ $done ? 'text-dark' : 'text-gray-400' }}">
                                {{ $step['label'] }}
                            </p>
                            <p class="text-xs {{ $current ? 'text-primary' : 'text-gray-400' }} mt-0.5">
                                {{ $step['desc'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-red-50 border border-red-100 rounded-2xl p-4 mb-6 text-sm text-red-600 font-medium">
            Cette commande a été annulée.
        </div>
    @endif

    {{-- Produits commandés --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <h2 class="text-sm font-bold text-dark mb-4">Articles commandés</h2>
        <div class="space-y-4">
            @foreach($commande->lignes as $ligne)
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        {{-- Image Produit --}}
                        @if($ligne->Produit?->image_principale)
                            <img src="{{ Storage::url($ligne->Produit->image_principale) }}"
                                 alt="{{ $ligne->nom_produit }}"
                                 class="w-14 h-14 rounded-xl object-contain bg-gray-50 border border-gray-100 shrink-0 p-1"/>
                        @else
                            <div class="w-14 h-14 rounded-xl bg-gray-100 shrink-0 flex items-center
                                        justify-center text-gray-400 text-xs font-bold">
                                {{ $ligne->quantite }}×
                            </div>
                        @endif

                        <div class="min-w-0">
                            {{-- Nom avec lien si le Produit existe encore --}}
                            @if($ligne->Produit?->slug)
                                <a href="{{ route('shop.produit', $ligne->Produit->slug) }}"
                                   class="text-sm font-medium text-dark hover:text-primary transition-colors
                                          truncate block group">
                                    {{ $ligne->nom_produit }}
                                    <svg class="w-3 h-3 inline-block ml-1 opacity-0 group-hover:opacity-100
                                                transition-opacity -mt-0.5 text-primary"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            @else
                                <p class="text-sm font-medium text-dark truncate">{{ $ligne->nom_produit }}</p>
                            @endif

                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $ligne->quantite }} × {{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA
                            </p>

                            {{-- Lien laisser un avis --}}
                            @if($ligne->Produit?->slug && $commande->statut === 'livree')
                                <a href="{{ route('shop.produit', $ligne->Produit->slug) }}#avis"
                                   class="inline-flex items-center gap-1 text-[11px] text-yellow-600
                                          hover:text-yellow-700 font-medium mt-1 transition-colors">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    Laisser un avis
                                </a>
                            @endif
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-dark shrink-0">
                        {{ number_format($ligne->total, 0, ',', ' ') }} FCFA
                    </span>
                </div>
            @endforeach
        </div>

        <div class="border-t border-gray-100 mt-6 pt-4 space-y-2">
            <div class="flex justify-between text-sm text-gray-500">
                <span>Sous-total</span>
                <span>{{ number_format($commande->sous_total, 0, ',', ' ') }} FCFA</span>
            </div>
            @if($commande->frais_livraison > 0)
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Frais de livraison</span>
                    <span>{{ number_format($commande->frais_livraison, 0, ',', ' ') }} FCFA</span>
                </div>
            @endif
            <div class="flex justify-between text-base font-bold text-dark pt-2 border-t border-gray-100">
                <span>Total</span>
                <span>{{ number_format($commande->total, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>
    </div>

    {{-- Infos livraison et paiement --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Livraison</h3>
            <p class="text-sm font-semibold text-dark">{{ $commande->nom_client }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ $commande->telephone_client }}</p>
            @if($commande->adresse_livraison)
                <p class="text-sm text-gray-500 mt-1">{{ $commande->adresse_livraison }}</p>
            @endif
            @if($commande->ville)
                <p class="text-sm text-gray-500">{{ $commande->ville }}</p>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Paiement</h3>
            <p class="text-sm font-semibold text-dark">{{ $commande->paiement_label }}</p>
            <div class="flex items-center gap-2 mt-2">
                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-medium
                             {{ $commande->statut_paiement === 'paye'
                                ? 'bg-green-100 text-green-700'
                                : ($commande->statut_paiement === 'echoue'
                                   ? 'bg-red-100 text-red-700'
                                   : 'bg-secondary/10 text-secondary') }}">
                    {{ match($commande->statut_paiement) {
                        'paye' => 'Payé',
                        'echoue' => 'Échoué',
                        'en_attente' => 'En attente',
                        default => ucfirst($commande->statut_paiement ?? '—'),
                    } }}
                </span>
            </div>
        </div>
    </div>

    @if($commande->notes)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-4">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Note</h3>
            <p class="text-sm text-gray-600">{{ $commande->notes }}</p>
        </div>
    @endif

</div>
@endsection
