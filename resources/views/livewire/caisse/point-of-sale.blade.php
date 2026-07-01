<div class="lg:h-full flex flex-col lg:flex-row gap-4">

    {{-- ════════════════════════════════════════
         COLONNE GAUCHE — Recherche & Produits
    ════════════════════════════════════════ --}}
    <div class="flex flex-col gap-4 min-w-0 lg:flex-1">

        {{-- Erreur flash --}}
        @if(session('pos_error'))
            <div x-data="{ show: true }" x-show="show"
                 x-init="setTimeout(() => show = false, 3000)"
                 class="flex items-center gap-3 bg-red-50 border border-red-200
                        text-red-700 px-4 py-3 rounded-xl text-sm font-medium">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0"/>
                </svg>
                {{ session('pos_error') }}
            </div>
        @endif

        {{-- ── Partie gauche : Grille produits ── --}}
        <div class="flex flex-col overflow-hidden lg:flex-1">

            {{-- Barre recherche + filtres catégories --}}
            <div class="p-4 bg-white border-b border-gray-100 space-y-3">

                {{-- Recherche --}}
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="rechercherPlat"
                        type="text" placeholder="Rechercher un Produit..."
                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl
                                text-sm focus:outline-none focus:border-primary bg-gray-50"/>
                </div>

                {{-- Filtres catégories --}}
                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
                    <button wire:click="$set('filtreCategorie', '')"
                            @class([
                                'flex-shrink-0 px-4 py-1.5 rounded-xl text-xs font-semibold
                                transition-all',
                                'bg-primary text-white shadow-lg shadow-primary/25'
                                    => $filtreCategorie === '',
                                'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                    => $filtreCategorie !== '',
                            ])>
                        Tous
                    </button>
                    @foreach($categories as $cat)
                        <button wire:click="$set('filtreCategorie', '{{ $cat->id }}')"
                                @class([
                                    'flex-shrink-0 px-4 py-1.5 rounded-xl text-xs font-semibold
                                    transition-all',
                                    'bg-primary text-white shadow-lg shadow-primary/25'
                                        => $filtreCategorie == $cat->id,
                                    'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                        => $filtreCategorie != $cat->id,
                                ])>
                            {{ $cat->nom }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Grille produits --}}
            <div class="overflow-y-auto p-4 max-h-72 lg:max-h-none lg:flex-1">
                @if(count($produits) > 0)
                    @php
                        $panierproduitIds = array_column($panier, 'produit_id');
                    @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                        @foreach($produits as $produit)
                            @php $isSelected = in_array($produit['id'], $panierproduitIds); @endphp
                            <button wire:click="toggleProduit({{ $produit['id'] }})"
                                    @class([
                                        'rounded-2xl border text-left transition-all
                                        duration-150 overflow-hidden group
                                        hover:-translate-y-0.5',
                                        'opacity-50 cursor-not-allowed'
                                            => ! $produit['sur_commande'] && $produit['stock'] <= 0,
                                        'bg-green-50 border-green-400 ring-2 ring-green-300/40 shadow-md shadow-green-100'
                                            => $isSelected,
                                        'bg-white border-gray-100 hover:shadow-lg hover:border-primary/30'
                                            => !$isSelected && ($produit['sur_commande'] || $produit['stock'] > 0),
                                    ])
                                    {{ ! $produit['sur_commande'] && $produit['stock'] <= 0 ? 'disabled' : '' }}>

                                {{-- Image --}}
                                <div class="aspect-square flex items-center
                                            justify-center p-3 relative overflow-hidden
                                            {{ $isSelected ? 'bg-green-100/60' : 'bg-gray-50' }}">
                                    @if($produit['image'])
                                        <img src="{{ Storage::url($produit['image']) }}"
                                            class="w-full h-full object-contain
                                                    group-hover:scale-105 transition-transform
                                                    duration-300"/>
                                    @else
                                        <svg class="w-10 h-10 text-gray-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0
                                                    00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    @endif

                                    {{-- Checkmark sélectionné --}}
                                    @if($isSelected)
                                        <div class="absolute top-1.5 left-1.5 w-6 h-6
                                                    bg-green-500 rounded-full flex items-center
                                                    justify-center shadow">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    @endif

                                    {{-- Badge stock bas / sur commande --}}
                                    @if($produit['sur_commande'])
                                        <div class="absolute top-1.5 right-1.5 bg-blue-500
                                                    text-white text-xs font-bold px-1.5 py-0.5
                                                    rounded-lg">
                                            Cde
                                        </div>
                                    @elseif($produit['stock'] > 0 && $produit['stock'] <= 3)
                                        <div class="absolute top-1.5 right-1.5 bg-orange-500
                                                    text-white text-xs font-bold px-1.5 py-0.5
                                                    rounded-lg">
                                            {{ $produit['stock'] }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Infos --}}
                                <div class="p-3">
                                    <p class="text-xs truncate mb-0.5
                                              {{ $isSelected ? 'text-green-600' : 'text-gray-400' }}">
                                        {{ $produit['categorie'] ?? '' }}
                                    </p>
                                    <p class="text-sm font-semibold truncate transition-colors
                                              {{ $isSelected ? 'text-green-700' : 'text-dark group-hover:text-primary' }}">
                                        {{ $produit['nom'] }}
                                    </p>
                                    <p class="text-sm font-bold mt-1
                                              {{ $isSelected ? 'text-green-600' : 'text-primary' }}">
                                        {{ number_format($produit['prix_vente'], 0, ',', ' ') }}
                                        <span class="text-xs font-normal text-gray-400">FCFA</span>
                                    </p>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                        <p class="text-sm">Aucun Produit trouvé</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Panier --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col
                    overflow-hidden lg:flex-1">

            {{-- Header panier --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293
                                 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100
                                 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="font-bold text-dark text-sm">
                        Panier
                        <span class="text-gray-400 font-normal">
                            ({{ count($panier) }} article{{ count($panier) > 1 ? 's' : '' }})
                        </span>
                    </h3>
                </div>
                @if(count($panier) > 0)
                    <button wire:click="$set('afficherConfirmationVider', true)"
                            class="text-xs text-red-500 hover:text-red-700
                                   hover:bg-red-50 px-2 py-1 rounded-lg transition-colors">
                        Vider
                    </button>
                @endif
            </div>

            {{-- Items panier --}}
            <div class="overflow-y-auto divide-y divide-gray-50 lg:flex-1">
                @forelse($panier as $index => $item)
                    <div class="px-4 py-3 flex items-center gap-3 hover:bg-gray-50/50
                                transition-colors">
                        {{-- Nom --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-dark truncate">
                                {{ $item['nom'] }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ number_format($item['prix_unitaire'], 0, ',', ' ') }} FCFA/u
                            </p>
                        </div>

                        {{-- Quantité --}}
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <button wire:click="updateQuantity({{ $index }}, {{ $item['quantite'] - 1 }})"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg
                                           bg-gray-100 hover:bg-gray-200 text-gray-600
                                           transition-colors text-sm font-bold">
                                −
                            </button>
                            <input type="number"
                                   wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                   value="{{ $item['quantite'] }}"
                                   min="1"
                                   @if($item['stock_max'] !== null) max="{{ $item['stock_max'] }}" @endif
                                   class="w-12 text-center text-sm font-bold border border-gray-200
                                          rounded-lg py-1 focus:outline-none focus:border-primary"/>
                            <button wire:click="updateQuantity({{ $index }}, {{ $item['quantite'] + 1 }})"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg
                                           bg-gray-100 hover:bg-gray-200 text-gray-600
                                           transition-colors text-sm font-bold">
                                +
                            </button>
                        </div>

                        {{-- Total --}}
                        <div class="text-right flex-shrink-0 w-24">
                            <p class="text-sm font-bold text-dark">
                                {{ number_format($item['total'], 0, ',', ' ') }}
                                <span class="text-xs font-normal text-gray-400">FCFA</span>
                            </p>
                        </div>

                        {{-- Supprimer --}}
                        <button wire:click="removeFromCart({{ $index }})"
                                class="w-7 h-7 flex items-center justify-center rounded-lg
                                       text-gray-300 hover:text-red-500 hover:bg-red-50
                                       transition-colors flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-8 lg:py-16 text-center px-4">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center
                                    justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293
                                         2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100
                                         4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-400">Panier vide</p>
                        <p class="text-xs text-gray-300 mt-1">
                            Recherchez un Produit pour commencer
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════
         COLONNE DROITE — Récap & Paiement
    ════════════════════════════════════════ --}}
    <div class="w-full lg:w-80 flex flex-col gap-4 flex-shrink-0">

        {{-- Client --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <h3 class="text-sm font-bold text-dark mb-3">Client</h3>

            @if($clientSelectionneId)
                {{-- Client sélectionné depuis la base --}}
                <div class="flex items-center justify-between bg-primary/5 rounded-xl px-3 py-2.5">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-primary rounded-full flex items-center
                                    justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr($clientSelectionneNom, 0, 2)) }}
                        </div>
                        <p class="text-sm font-semibold text-dark">{{ $clientSelectionneNom }}</p>
                    </div>
                    <button wire:click="clearClient"
                            class="text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

            @elseif($utiliserClientRapide)
                {{-- Saisie client rapide --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-xs font-medium text-secondary">✏️ Client rapide</p>
                        <button wire:click="clearClient"
                                class="text-xs text-gray-400 hover:text-red-500 transition-colors">
                            Annuler
                        </button>
                    </div>
                    <input wire:model="clientRapideNom"
                        type="text"
                        placeholder="Nom du client *"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm
                                bg-gray-50 focus:outline-none focus:border-primary transition-colors"/>
                    <input wire:model.live.debounce.400ms="clientRapideTel"
                        type="text"
                        placeholder="Téléphone / WhatsApp"
                        class="w-full px-3 py-2 border rounded-xl text-sm
                                focus:outline-none transition-colors
                                {{ $doublonClient ? 'border-orange-300 bg-orange-50' : 'border-gray-200 bg-gray-50 focus:border-primary' }}"/>

                    @if($doublonClient)
                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-3">
                            <p class="text-xs font-medium text-orange-700 mb-2">
                                ⚠️ Ce numéro existe déjà :
                            </p>
                            <button wire:click="selectClient({{ $doublonClient['id'] }}, '{{ addslashes($doublonClient['nom']) }}')"
                                    class="w-full flex items-center gap-2 bg-white border border-orange-200
                                           rounded-lg px-3 py-2 hover:bg-orange-100 transition-colors text-left">
                                <div class="w-7 h-7 bg-orange-100 rounded-full flex items-center
                                            justify-center text-xs font-bold text-orange-600 flex-shrink-0">
                                    {{ strtoupper(substr($doublonClient['nom'], 0, 2)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-dark truncate">
                                        {{ $doublonClient['nom'] }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $doublonClient['telephone'] }}</p>
                                </div>
                                <span class="text-xs text-primary font-medium flex-shrink-0">Sélectionner</span>
                            </button>
                        </div>
                    @else
                        <p class="text-xs text-gray-400">
                            💡 Une fiche client sera créée automatiquement
                        </p>
                    @endif
                </div>

            @else
                {{-- Recherche ou création rapide --}}
                <div class="relative">
                    <input wire:model.live.debounce.300ms="rechercherClient"
                        type="text"
                        placeholder="Rechercher un client..."
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm
                                bg-gray-50 focus:outline-none focus:border-primary transition-colors"/>

                    @if(count($resultatsClients) > 0)
                        <div class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl
                                    shadow-xl border border-gray-100 z-30 overflow-hidden">
                            @foreach($resultatsClients as $client)
                                <button wire:click="selectClient({{ $client['id'] }}, '{{ addslashes($client['nom']) }}')"
                                        class="w-full flex items-center gap-2 px-3 py-2.5
                                            hover:bg-primary/5 transition-colors text-left
                                            border-b border-gray-50 last:border-0">
                                    <div class="w-7 h-7 bg-gray-200 rounded-full flex items-center
                                                justify-center text-xs font-bold text-gray-600">
                                        {{ strtoupper(substr($client['nom'], 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-dark">
                                            {{ $client['nom'] }}
                                        </p>
                                        <p class="text-xs text-gray-400">{{ $client['telephone'] }}</p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 mt-2">
                    <button wire:click="$set('utiliserClientRapide', true)"
                            class="flex-1 py-1.5 text-xs font-medium text-secondary
                                border border-secondary/30 rounded-xl hover:bg-secondary/5
                                transition-colors">
                        ✏️ Saisir un client
                    </button>
                    <p class="text-xs text-gray-300">ou</p>
                    <p class="text-xs text-gray-400">vente anonyme</p>
                </div>
            @endif
        </div>

        {{-- Récapitulatif --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <h3 class="text-sm font-bold text-dark mb-3">Récapitulatif</h3>

            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-gray-500">
                    <span>Sous-total HT</span>
                    <span>{{ number_format($sousTotal, 0, ',', ' ') }} FCFA</span>
                </div>

                {{-- Remise --}}
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Remise</span>
                    <div class="flex items-center gap-1">
                        <span class="text-gray-400 text-xs">−</span>
                        <input wire:model.live="remise"
                               type="number" min="0" step="100"
                               placeholder="0"
                               class="w-24 text-right px-2 py-1 border border-gray-200
                                      rounded-lg text-sm focus:outline-none focus:border-primary"/>
                        <span class="text-xs text-gray-400">FCFA</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-2 mt-2">
                    <div class="flex justify-between font-bold text-dark text-base">
                        <span>Total</span>
                        <span class="text-primary">
                            {{ number_format($total, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bouton encaisser --}}
        <button wire:click="openPaymentModal"
                @class([
                    'w-full py-4 rounded-2xl font-bold text-base transition-all flex items-center justify-center gap-3',
                    'bg-primary hover:bg-primary/90 text-white shadow-xl shadow-primary/30' => count($panier) > 0,
                    'bg-gray-100 text-gray-400 cursor-not-allowed' => count($panier) === 0,
                ])
                @disabled(count($panier) === 0)>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2
                         2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2
                         2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Encaisser — {{ number_format($total, 0, ',', ' ') }} FCFA
        </button>
    </div>

    {{-- ════════════════════════════════════════
         MODAL CONFIRMATION VIDER PANIER
    ════════════════════════════════════════ --}}
    @if($afficherConfirmationVider)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="$set('afficherConfirmationVider', false)"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5
                                 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-dark mb-1">Vider le panier ?</h3>
                <p class="text-sm text-gray-400 mb-6">
                    {{ count($panier) }} article{{ count($panier) > 1 ? 's' : '' }} seront supprimés.
                </p>
                <div class="flex gap-3">
                    <button wire:click="$set('afficherConfirmationVider', false)"
                            class="flex-1 py-2.5 text-sm font-medium text-gray-600
                                   hover:bg-gray-100 rounded-xl transition-colors border border-gray-200">
                        Annuler
                    </button>
                    <button wire:click="clearCart"
                            class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white
                                   text-sm font-bold rounded-xl transition-colors">
                        Vider
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════
         MODAL PAIEMENT
    ════════════════════════════════════════ --}}
    @if($afficherModalPaiement)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="$set('afficherModalPaiement', false)"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-dark">Encaissement</h3>
                    <button wire:click="$set('afficherModalPaiement', false)"
                            class="w-8 h-8 flex items-center justify-center rounded-lg
                                   text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Erreur paiement --}}
                    @if($erreurPaiement)
                        <div class="flex items-center gap-3 bg-red-50 border border-red-200
                                    text-red-700 px-4 py-3 rounded-xl text-sm font-medium">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0"/>
                            </svg>
                            {{ $erreurPaiement }}
                        </div>
                    @endif

                    {{-- Résumé --}}
                    <div class="bg-primary/5 rounded-xl p-4 text-center">
                        <p class="text-sm text-gray-500">Montant à encaisser</p>
                        <p class="text-3xl font-bold text-primary mt-1">
                            {{ number_format($total, 0, ',', ' ') }}
                            <span class="text-lg">FCFA</span>
                        </p>
                    </div>

                    {{-- Date de la vente --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Date de la vente
                        </label>
                        <input wire:model="dateVente" type="date"
                               max="{{ now()->toDateString() }}"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                      text-sm bg-gray-50 focus:outline-none
                                      focus:border-primary transition-colors"/>
                    </div>

                    {{-- Mode de paiement --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">
                            Mode de paiement
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach([
                                ['value' => 'cash',  'label' => 'Cash'],
                                ['value' => 'wave',  'label' => 'Wave'],
                                ['value' => 'om',    'label' => 'Orange Money'],
                                ['value' => 'mix',   'label' => 'Mixx by Yass'],
                                ['value' => 'card',  'label' => 'Carte bancaire'],
                            ] as $method)
                                <button type="button"
                                        wire:click="$set('modePaiement', '{{ $method['value'] }}')"
                                        @class([
                                            'py-2.5 px-3 rounded-xl text-xs font-semibold transition-all border-2',
                                            'border-primary bg-primary text-white shadow-lg shadow-primary/25'
                                                => $modePaiement === $method['value'],
                                            'border-gray-200 bg-gray-50 text-gray-600 hover:border-primary/50'
                                                => $modePaiement !== $method['value'],
                                        ])>
                                    {{ $method['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Montant reçu --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Montant reçu
                        </label>
                        <div class="relative">
                            <input wire:model.live="montantRecu"
                                   type="number" min="{{ $total }}" step="100"
                                   class="w-full px-4 py-3 border-2 rounded-xl text-lg
                                          font-bold text-center focus:outline-none
                                          transition-colors
                                          {{ $errors->has('montantRecu')
                                             ? 'border-red-400 bg-red-50'
                                             : 'border-gray-200 focus:border-primary' }}"/>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2
                                         text-sm text-gray-400 font-medium">FCFA</span>
                        </div>
                        @error('montantRecu')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Monnaie à rendre --}}
                    @php $change = max(0, $montantRecu - $total); @endphp
                    @if($change > 0)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                            <p class="text-sm text-green-600">Monnaie à rendre</p>
                            <p class="text-2xl font-bold text-green-700 mt-1">
                                {{ number_format($change, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                    @endif

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Notes (optionnel)
                        </label>
                        <input wire:model="notes" type="text"
                               placeholder="Référence, commentaire..."
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                      text-sm bg-gray-50 focus:outline-none
                                      focus:border-primary transition-colors"/>
                    </div>
                </div>

                <div class="px-6 pb-6 flex gap-3">
                    <button wire:click="$set('afficherModalPaiement', false)"
                            class="flex-1 py-3 text-sm font-medium text-gray-600
                                   hover:bg-gray-100 rounded-xl transition-colors">
                        Annuler
                    </button>
                    <button wire:click="processPayment"
                            class="flex-1 py-3 bg-primary hover:bg-primary/90 text-white
                                   font-bold rounded-xl transition-all shadow-lg
                                   shadow-primary/25 flex items-center justify-center gap-2">
                        <svg wire:loading wire:target="processPayment"
                             class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        ✅ Valider la vente
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════
         MODAL SUCCÈS
    ════════════════════════════════════════ --}}
    @if($afficherModalSucces)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 text-center">

                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center
                            justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-dark mb-1">Vente enregistrée !</h3>
                <p class="text-sm text-gray-400 mb-4">{{ $derniereReference }}</p>

                @if($monnaieRendue > 0)
                    <div class="bg-green-50 rounded-xl p-3 mb-4">
                        <p class="text-sm text-green-600">Monnaie à rendre</p>
                        <p class="text-2xl font-bold text-green-700">
                            {{ number_format($monnaieRendue, 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                @endif

                <div class="flex gap-3">
                    <a href="{{ route('factures.show', $derniereVenteId) }}"
                       class="flex-1 py-2.5 bg-secondary hover:bg-secondary/90 text-white
                              text-sm font-medium rounded-xl transition-colors">
                        🧾 Voir facture
                    </a>
                    <button wire:click="$set('afficherModalSucces', false)"
                            class="flex-1 py-2.5 bg-primary hover:bg-primary/90 text-white
                                   text-sm font-medium rounded-xl transition-colors">
                        Nouvelle vente
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>