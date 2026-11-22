@extends('layouts.app')

@section('title', 'Facture ' . $vente->facture?->numero_facture)
@section('page-title', 'Facture ' . $vente->facture?->numero_facture)
@section('page-subtitle', $vente->vendu_le->format('d/m/Y à H:i'))

@section('content')
<div class="max-w-4xl mx-auto space-y-4">

    {{-- Flash --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="flex items-center gap-3 bg-green-50 border border-green-200
                    text-green-800 px-4 py-3 rounded-xl text-sm font-medium">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200
                    text-red-800 px-4 py-3 rounded-xl text-sm font-medium">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('factures.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium
                  text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
            ← Retour
        </a>

        {{-- Télécharger PDF --}}
        <a href="{{ route('factures.download', $vente->id) }}"
           class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90
                  text-white font-medium px-4 py-2.5 rounded-xl transition-all
                  shadow-lg shadow-primary/25 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2
                         0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0
                         01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Télécharger PDF
        </a>

        {{-- Basculer Statut --}}
        @if($vente->facture)
            <form action="{{ route('factures.toggle-statut', $vente->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm transition-all {{ $vente->facture->estPayee() ? 'bg-amber-100 hover:bg-amber-200 text-amber-800' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                    @if($vente->facture->estPayee())
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"/>
                        </svg>
                        Passer en « À régler »
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Marquer comme « Payée »
                    @endif
                </button>
            </form>
        @endif

        {{-- Imprimer --}}
        <button onclick="window.print()"
                class="inline-flex items-center gap-2 bg-dark hover:bg-dark/90
                       text-white font-medium px-4 py-2.5 rounded-xl transition-all
                       text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2
                         2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0
                         00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0
                         00-2 2v4h10z"/>
            </svg>
            Imprimer
        </button>
    </div>

    {{-- Aperçu facture --}}
    @if($vente->facture)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-dark flex items-center gap-2">
                    <span>Aperçu — {{ $vente->facture->numero_facture }}</span>
                </h3>
                <span @class([
                    'px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1.5 shadow-xs',
                    'bg-emerald-100 text-emerald-700' => $vente->facture->estPayee(),
                    'bg-amber-100 text-amber-700' => !$vente->facture->estPayee(),
                ])>
                    <span class="w-1.5 h-1.5 rounded-full {{ $vente->facture->estPayee() ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                    <span>{{ $vente->facture->estPayee() ? 'Payée (Acquittée)' : 'À régler' }}</span>
                </span>
            </div>

            {{-- Infos rapides --}}
            <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-y md:divide-y-0
                        divide-gray-100">
                <div class="p-4">
                    <p class="text-xs text-gray-400">Client</p>
                    <p class="text-sm font-semibold text-dark mt-0.5">
                        {{ $vente->client?->nom ?? 'Client anonyme' }}
                    </p>
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-400">Date d'émission</p>
                    <p class="text-sm font-semibold text-dark mt-0.5">
                        {{ $vente->facture->emise_le->format('d/m/Y') }}
                    </p>
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-400">Règlement</p>
                    <p class="text-sm font-semibold text-dark mt-0.5">
                        {{ $vente->facture->estPayee() ? $vente->mode_paiement_libelle : 'En attente' }}
                    </p>
                    @if($vente->facture->estPayee() && $vente->mode_paiement === 'multiple')
                        <div class="text-[11px] text-gray-500 mt-1 space-y-0.5">
                            @if($vente->montant_cash > 0) <div>💵 Espèces : {{ number_format($vente->montant_cash, 0, ',', ' ') }} F</div> @endif
                            @if($vente->montant_wave > 0) <div>📱 Wave : {{ number_format($vente->montant_wave, 0, ',', ' ') }} F</div> @endif
                            @if($vente->montant_om > 0) <div>🍊 OM : {{ number_format($vente->montant_om, 0, ',', ' ') }} F</div> @endif
                            @if($vente->montant_carte > 0) <div>💳 Carte : {{ number_format($vente->montant_carte, 0, ',', ' ') }} F</div> @endif
                        </div>
                    @endif
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-400">Total</p>
                    <p class="text-sm font-bold text-primary mt-0.5">
                        {{ number_format($vente->facture->total, 0, ',', ' ') }} FCFA
                    </p>
                </div>
            </div>

            {{-- Articles --}}
            <div class="p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">
                    Articles
                </p>
                <div class="space-y-2">
                    @foreach($vente->lignes as $item)
                        <div class="flex items-center justify-between p-3
                                    bg-gray-50 rounded-xl">
                            <div>
                                <p class="text-sm font-medium text-dark">
                                    {{ $item->nom_produit }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ number_format($item->prix_unitaire, 0, ',', ' ') }}
                                    FCFA × {{ $item->quantite }}
                                </p>
                            </div>
                            <p class="text-sm font-bold text-dark">
                                {{ number_format($item->total, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Totaux --}}
            <div class="p-4 border-t border-gray-100">
                <div class="max-w-xs ml-auto space-y-2">
                    @if($vente->discount > 0)
                        <div class="flex justify-between text-sm text-secondary">
                            <span>Remise</span>
                            <span>
                                -{{ number_format($vente->discount, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                    @endif
                    <div class="flex justify-between text-base font-bold text-dark
                                border-t border-gray-100 pt-2">
                        <span>Total</span>
                        <span class="text-primary">
                            {{ number_format($vente->facture->total, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 text-center">
            <p class="text-sm text-yellow-700">
                Aucune facture associée à cette vente.
            </p>
        </div>
    @endif
</div>
@endsection