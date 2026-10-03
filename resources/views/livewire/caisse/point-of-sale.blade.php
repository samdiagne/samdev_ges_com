<div class="h-full w-full flex flex-col lg:flex-row gap-3 lg:gap-4 overflow-hidden min-h-0 flex-1">

    {{-- Erreur flash globale flottante --}}
    @if(session('pos_error'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 3500)"
             class="fixed top-20 right-6 z-50 flex items-center gap-3 bg-red-600 text-white px-5 py-3 rounded-2xl shadow-xl text-sm font-medium animate-bounce">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0"/>
            </svg>
            {{ session('pos_error') }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         COLONNE GAUCHE / CENTRE — CATALOGUE PRODUITS PLEINE HAUTEUR (70%)
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="flex-1 min-w-0 flex flex-col h-full bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- ── En-tête : Recherche & Filtres Catégories ── --}}
        <div class="p-3 sm:p-4 border-b border-gray-100 bg-white space-y-2.5 flex-shrink-0">
            <div class="flex items-center gap-3">
                {{-- Barre de recherche --}}
                <div class="relative flex-1">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input wire:model.live.debounce.250ms="rechercherPlat"
                           type="text"
                           placeholder="Rechercher un produit, plat, boisson..."
                           class="w-full pl-10 pr-9 py-2 sm:py-2.5 border border-gray-200 rounded-xl
                                  text-sm focus:outline-none focus:border-primary bg-gray-50 focus:bg-white transition-all"/>
                    @if($rechercherPlat)
                        <button wire:click="$set('rechercherPlat', '')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>

                {{-- Compteur produits --}}
                <div class="hidden sm:flex items-center gap-1.5 px-3 py-2 bg-gray-50 rounded-xl border border-gray-100 text-xs font-semibold text-gray-500 flex-shrink-0">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span>{{ count($produits) }} produit{{ count($produits) > 1 ? 's' : '' }}</span>
                </div>
            </div>

            {{-- Filtres Catégories horizontaux --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-hide">
                <button wire:click="$set('filtreCategorie', '')"
                        @class([
                            'flex-shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5',
                            'bg-primary text-white shadow-md shadow-primary/25'
                                => $filtreCategorie === '',
                            'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                => $filtreCategorie !== '',
                        ])>
                    <span>Tous</span>
                </button>
                @foreach($categories as $cat)
                    <button wire:click="$set('filtreCategorie', '{{ $cat->id }}')"
                            @class([
                                'flex-shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5',
                                'bg-primary text-white shadow-md shadow-primary/25'
                                    => $filtreCategorie == $cat->id,
                                'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                    => $filtreCategorie != $cat->id,
                            ])>
                        <span>{{ $cat->nom }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ── Grille Produits PLEINE HAUTEUR DÉFILANTE ── --}}
        <div class="flex-1 min-h-0 overflow-y-auto p-3 sm:p-4 bg-gray-50/40">
            @if(count($produits) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-2.5 sm:gap-3.5">
                    @foreach($produits as $produit)
                        @php
                            $cartItem = collect($panier)->firstWhere('produit_id', $produit['id']);
                            $qtyInCart = $cartItem['quantite'] ?? 0;
                            $isIndispo = ! $produit['sur_commande'] && $produit['stock'] <= 0;
                        @endphp

                        <button wire:click="addToCart({{ $produit['id'] }})"
                                @class([
                                    'group relative rounded-2xl border text-left transition-all duration-150 overflow-hidden flex flex-col',
                                    'cursor-not-allowed opacity-45 bg-gray-100 border-gray-200'
                                        => $isIndispo,
                                    'bg-white border-primary ring-2 ring-primary/20 shadow-md -translate-y-0.5'
                                        => $qtyInCart > 0 && !$isIndispo,
                                    'bg-white border-gray-100 hover:border-primary/40 hover:shadow-lg hover:-translate-y-1'
                                        => $qtyInCart === 0 && !$isIndispo,
                                ])
                                @disabled($isIndispo)>

                            {{-- Vignette Image --}}
                            <div class="aspect-square w-full bg-gray-50 relative overflow-hidden flex items-center justify-center p-2">
                                @if($produit['image'])
                                    <img src="{{ Storage::url($produit['image']) }}"
                                         alt="{{ $produit['nom'] }}"
                                         class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-300"/>
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif

                                {{-- Badge Quantité Panier --}}
                                @if($qtyInCart > 0)
                                    <div class="absolute top-2 left-2 px-2 py-0.5 bg-primary text-white text-xs font-extrabold rounded-lg shadow flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>{{ $qtyInCart }}</span>
                                    </div>
                                @endif

                                {{-- Badges Stock / Commande --}}
                                @if($produit['sur_commande'])
                                    <div class="absolute top-2 right-2 bg-blue-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md shadow-sm">
                                        Sur Cde
                                    </div>
                                @elseif($produit['stock'] > 0 && $produit['stock'] <= 3)
                                    <div class="absolute top-2 right-2 bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md shadow-sm">
                                        {{ $produit['stock'] }} rest.
                                    </div>
                                @elseif($isIndispo)
                                    <div class="absolute top-2 right-2 bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md shadow-sm">
                                        Épuisé
                                    </div>
                                @endif
                            </div>

                            {{-- Détails texte --}}
                            <div class="p-2.5 sm:p-3 flex flex-col justify-between flex-1 bg-white">
                                <div>
                                    <p class="text-[10px] font-medium uppercase tracking-wider text-gray-400 truncate">
                                        {{ $produit['categorie'] ?? 'Plat' }}
                                    </p>
                                    <h4 class="text-xs sm:text-sm font-bold text-dark line-clamp-2 leading-snug group-hover:text-primary transition-colors">
                                        {{ $produit['nom'] }}
                                    </h4>
                                </div>

                                <div class="mt-2 pt-1 border-t border-gray-50 flex items-baseline justify-between">
                                    <p class="text-xs sm:text-sm font-extrabold text-primary">
                                        {{ number_format($produit['prix_vente'], 0, ',', ' ') }}
                                        <span class="text-[10px] font-normal text-gray-400">FCFA</span>
                                    </p>
                                    @if($produit['quantite_palier'] && $produit['prix_palier'])
                                        <span class="text-[9px] text-emerald-600 bg-emerald-50 px-1 py-0.5 rounded font-medium" title="Dès {{ $produit['quantite_palier'] }} pièces">
                                            dès {{ $produit['quantite_palier'] }}p
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-600">Aucun produit trouvé</p>
                    <p class="text-xs text-gray-400 mt-1">Essayez un autre mot-clé ou filtrez par catégorie</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         COLONNE DROITE — PANIER & ENCAISSEMENT UNIFIÉ (330px)
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="w-full lg:w-80 xl:w-[330px] flex flex-col h-full bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex-shrink-0">

        {{-- ── En-tête : Client ── --}}
        <div class="p-3 border-b border-gray-100 bg-gray-50/60 flex-shrink-0">
            @if($clientSelectionneId)
                {{-- Client sélectionné --}}
                <div class="flex items-center justify-between bg-white rounded-xl border border-primary/20 px-3 py-2 shadow-sm">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 bg-primary text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($clientSelectionneNom, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-400 font-medium">Client sélectionné</p>
                            <p class="text-xs sm:text-sm font-bold text-dark truncate">{{ $clientSelectionneNom }}</p>
                        </div>
                    </div>
                    <button wire:click="clearClient"
                            class="text-gray-400 hover:text-red-500 p-1 rounded-lg hover:bg-red-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

            @elseif($utiliserClientRapide)
                {{-- Saisie rapide --}}
                <div class="bg-white rounded-xl border border-secondary/30 p-2.5 shadow-sm space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-secondary flex items-center gap-1">
                            ✏️ Client rapide
                        </span>
                        <button wire:click="clearClient" class="text-xs text-gray-400 hover:text-red-500">
                            Annuler
                        </button>
                    </div>
                    <input wire:model="clientRapideNom"
                           type="text"
                           placeholder="Nom du client *"
                           class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs bg-gray-50 focus:outline-none focus:border-primary"/>
                    <input wire:model.live.debounce.300ms="clientRapideTel"
                           type="text"
                           placeholder="Téléphone / WhatsApp"
                           class="w-full px-3 py-1.5 border rounded-lg text-xs focus:outline-none {{ $doublonClient ? 'border-orange-300 bg-orange-50' : 'border-gray-200 bg-gray-50 focus:border-primary' }}"/>
                    @if($doublonClient)
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-2 text-xs">
                            <p class="text-[11px] text-orange-700 font-semibold mb-1">Numéro existant :</p>
                            <button wire:click="selectClient({{ $doublonClient['id'] }}, '{{ addslashes($doublonClient['nom']) }}')"
                                    class="w-full text-left font-bold text-primary hover:underline truncate">
                                {{ $doublonClient['nom'] }} ({{ $doublonClient['telephone'] }})
                            </button>
                        </div>
                    @endif
                </div>

            @else
                {{-- Recherche / Bouton rapide --}}
                <div class="space-y-1.5">
                    <div class="relative">
                        <input wire:model.live.debounce.250ms="rechercherClient"
                               type="text"
                               placeholder="Rechercher un client (nom / tél)..."
                               class="w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-xl text-xs bg-white focus:outline-none focus:border-primary"/>
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>

                        @if(count($resultatsClients) > 0)
                            <div class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-gray-100 z-30 overflow-hidden max-h-48 overflow-y-auto">
                                @foreach($resultatsClients as $client)
                                    <button wire:click="selectClient({{ $client['id'] }}, '{{ addslashes($client['nom']) }}')"
                                            class="w-full flex items-center justify-between px-3 py-2 hover:bg-primary/5 transition-colors text-left border-b border-gray-50 last:border-0">
                                        <div class="truncate">
                                            <p class="text-xs font-bold text-dark truncate">{{ $client['nom'] }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $client['telephone'] }}</p>
                                        </div>
                                        <span class="text-[10px] text-primary font-semibold flex-shrink-0">Choisir</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-between px-1">
                        <button wire:click="$set('utiliserClientRapide', true)"
                                class="text-[11px] font-semibold text-secondary hover:underline flex items-center gap-1">
                            <span>+ Saisie rapide client</span>
                        </button>
                        <span class="text-[10px] text-gray-400">Vente anonyme par défaut</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- ── En-tête Panier : Titre & Vider ── --}}
        <div class="px-4 py-2.5 border-b border-gray-100 bg-white flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="font-bold text-xs sm:text-sm text-dark">
                    Ticket en cours
                    <span class="text-gray-400 font-normal">({{ count($panier) }})</span>
                </h3>
            </div>
            @if(count($panier) > 0)
                <button wire:click="$set('afficherConfirmationVider', true)"
                        class="text-xs text-red-500 hover:text-red-700 hover:bg-red-50 px-2 py-1 rounded-lg transition-colors font-medium">
                    Vider
                </button>
            @endif
        </div>

        {{-- ── Corps du Panier Défilant ── --}}
        <div class="flex-1 min-h-0 overflow-y-auto divide-y divide-gray-50 px-2 sm:px-3">
            @forelse($panier as $index => $item)
                <div wire:key="cart-item-{{ $item['produit_id'] }}-{{ $index }}"
                     class="py-2.5 px-2 flex items-center gap-2.5 hover:bg-gray-50/70 rounded-xl transition-colors">
                    {{-- Titre & Prix unitaire --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-bold text-dark truncate">
                            {{ $item['nom'] }}
                        </p>
                        <p class="text-[11px] text-gray-400">
                            {{ number_format($item['prix_unitaire'], 0, ',', ' ') }} FCFA
                            @if(($item['remise'] ?? 0) > 0)
                                <span class="text-amber-600 font-medium ml-1">(-{{ number_format($item['remise'], 0, ',', ' ') }})</span>
                            @endif
                        </p>
                    </div>

                    {{-- Boutons & Saisie Quantité --}}
                    <div class="flex items-center gap-1 flex-shrink-0 bg-gray-100 rounded-lg p-0.5">
                        <button wire:click="updateQuantity({{ $index }}, {{ $item['quantite'] - 1 }})"
                                type="button"
                                class="w-6 h-6 flex items-center justify-center rounded-md bg-white hover:bg-gray-200 text-gray-700 font-extrabold text-xs shadow-xs transition-colors"
                                title="Diminuer">
                            −
                        </button>
                        <input type="number"
                               inputmode="numeric"
                               min="1"
                               @if(($item['stock_max'] ?? null) !== null) max="{{ $item['stock_max'] }}" @endif
                               value="{{ $item['quantite'] }}"
                               x-on:focus="$event.target.select()"
                               x-on:change="let val = parseInt($event.target.value); $wire.updateQuantity({{ $index }}, isNaN(val) ? 1 : val)"
                               x-on:keydown.enter.prevent="$event.target.blur()"
                               class="w-10 h-6 text-center text-xs font-bold text-dark bg-white border border-gray-200 hover:border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary rounded-md p-0 shadow-xs cursor-text [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none transition-all"
                               title="Cliquer pour saisir le nombre" />
                        <button wire:click="updateQuantity({{ $index }}, {{ $item['quantite'] + 1 }})"
                                type="button"
                                class="w-6 h-6 flex items-center justify-center rounded-md bg-white hover:bg-gray-200 text-gray-700 font-extrabold text-xs shadow-xs transition-colors"
                                title="Augmenter">
                            +
                        </button>
                    </div>

                    {{-- Total ligne --}}
                    <div class="text-right flex-shrink-0 w-20">
                        <p class="text-xs sm:text-sm font-bold text-dark">
                            {{ number_format($item['total'], 0, ',', ' ') }}
                        </p>
                        <span class="text-[9px] text-gray-400">FCFA</span>
                    </div>

                    {{-- Supprimer ligne --}}
                    <button wire:click="removeFromCart({{ $index }})"
                            class="text-gray-300 hover:text-red-500 p-1 transition-colors flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center h-full py-12 text-center text-gray-400">
                    <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs sm:text-sm font-semibold text-gray-500">Panier vide</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Touchez un plat à gauche pour commencer</p>
                </div>
            @endforelse
        </div>

        {{-- ── Pied : Récapitulatif & Bouton Encaisser (Fixé) ── --}}
        <div class="p-3.5 bg-gray-50/90 border-t border-gray-100 flex-shrink-0 space-y-3">
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between text-gray-500">
                    <span>Sous-total</span>
                    <span class="font-medium text-dark">{{ number_format($sousTotal, 0, ',', ' ') }} FCFA</span>
                </div>

                {{-- Remise globale --}}
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Remise</span>
                    <div class="flex items-center gap-1">
                        <span class="text-gray-400 text-xs">−</span>
                        <input wire:model.live="remise"
                               type="number" min="0" step="100"
                               placeholder="0"
                               class="w-20 text-right px-2 py-0.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-primary bg-white"/>
                        <span class="text-[10px] text-gray-400">FCFA</span>
                    </div>
                </div>

                {{-- Total TTC en grand --}}
                <div class="border-t border-gray-200/80 pt-2 flex items-baseline justify-between">
                    <span class="text-xs sm:text-sm font-bold text-dark">TOTAL À PAYER</span>
                    <div class="text-right">
                        <span class="text-lg sm:text-xl font-extrabold text-primary">
                            {{ number_format($total, 0, ',', ' ') }}
                        </span>
                        <span class="text-xs font-semibold text-primary ml-0.5">FCFA</span>
                    </div>
                </div>
            </div>

            {{-- Bouton Encaisser --}}
            <button wire:click="openPaymentModal"
                    @class([
                        'w-full py-3.5 rounded-xl font-bold text-sm sm:text-base transition-all flex items-center justify-center gap-2 shadow-lg',
                        'bg-primary hover:bg-primary/95 text-white shadow-primary/30 hover:shadow-primary/40 active:scale-[0.99]'
                            => count($panier) > 0,
                        'bg-gray-200 text-gray-400 cursor-not-allowed shadow-none'
                            => count($panier) === 0,
                    ])
                    @disabled(count($panier) === 0)>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Encaisser &nbsp;{{ number_format($total, 0, ',', ' ') }} FCFA</span>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL CONFIRMATION VIDER PANIER
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($afficherConfirmationVider)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="$wire.set('afficherConfirmationVider', false)">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm cursor-pointer"
                 wire:click="$set('afficherConfirmationVider', false)"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center" @click.stop>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-dark mb-1">Vider le panier ?</h3>
                <p class="text-sm text-gray-400 mb-6">
                    {{ count($panier) }} article{{ count($panier) > 1 ? 's' : '' }} seront supprimés du ticket.
                </p>
                <div class="flex gap-3">
                    <button wire:click="$set('afficherConfirmationVider', false)"
                            class="flex-1 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-xl transition-colors border border-gray-200">
                        Annuler
                    </button>
                    <button wire:click="clearCart"
                            class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-colors">
                        Vider
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL PAIEMENT & ENCAISSEMENT
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($afficherModalPaiement)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="$wire.set('afficherModalPaiement', false)">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm cursor-pointer"
                 wire:click="$set('afficherModalPaiement', false)"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto"
                 @click.stop
                 x-data="{
                     montant: {{ (float) $montantRecu }},
                     total: {{ (float) $total }},
                     get monnaie() {
                         let m = parseFloat(this.montant) || 0;
                         return m > this.total ? Math.round(m - this.total) : 0;
                     },
                     formatMoney(val) {
                         return new Intl.NumberFormat('fr-FR').format(Math.round(val));
                     },
                     setMontant(val) {
                         this.montant = val;
                         $wire.set('montantRecu', val);
                     },
                     valider() {
                         let m = parseFloat(this.montant) || 0;
                         $wire.processPayment(m);
                     }
                 }">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-dark">Encaissement</h3>
                    <button wire:click="$set('afficherModalPaiement', false)"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    {{-- Erreur éventuelle --}}
                    @if($erreurPaiement)
                        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-medium">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0"/>
                            </svg>
                            {{ $erreurPaiement }}
                        </div>
                    @endif

                    {{-- Résumé montant --}}
                    <div class="bg-primary/5 rounded-2xl p-4 text-center border border-primary/10">
                        <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Montant à encaisser</p>
                        <p class="text-3xl font-extrabold text-primary mt-1">
                            {{ number_format($total, 0, ',', ' ') }}
                            <span class="text-lg">FCFA</span>
                        </p>
                    </div>

                    {{-- Date de la vente --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Date de la vente</label>
                        <input wire:model="dateVente" type="date"
                               max="{{ now()->toDateString() }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:outline-none focus:border-primary transition-colors"/>
                    </div>

                    {{-- Modes de règlement --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Mode de paiement</label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach([
                                ['value' => 'cash',     'label' => 'Cash'],
                                ['value' => 'wave',     'label' => 'Wave'],
                                ['value' => 'om',       'label' => 'Orange Money'],
                                ['value' => 'mix',      'label' => 'Mixx by Yass'],
                                ['value' => 'card',     'label' => 'Carte bancaire'],
                                ['value' => 'a_regler', 'label' => 'À régler plus tard'],
                            ] as $method)
                                <button type="button"
                                        wire:click="$set('modePaiement', '{{ $method['value'] }}')"
                                        @class([
                                            'py-2 px-2.5 rounded-xl text-xs font-bold transition-all border-2 text-center',
                                            'border-primary bg-primary text-white shadow-md shadow-primary/25'
                                                => $modePaiement === $method['value'],
                                            'border-amber-400 bg-amber-500 text-white shadow-md shadow-amber-500/25'
                                                => $modePaiement === 'a_regler' && $method['value'] === 'a_regler',
                                            'border-gray-200 bg-gray-50 text-gray-600 hover:border-primary/40'
                                                => $modePaiement !== $method['value'],
                                        ])>
                                    {{ $method['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @if($modePaiement === 'a_regler')
                        {{-- Information vente à régler --}}
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800">
                            <p class="font-bold flex items-center gap-1.5 mb-1">
                                <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"/>
                                </svg>
                                Facture en attente de règlement
                            </p>
                            <p class="text-amber-700 text-[11px] leading-relaxed">
                                La vente sera enregistrée en attente. La facture générée affichera la mention officielle <strong>« À RÉGLER »</strong>. Vous pourrez la marquer comme payée plus tard en un clic.
                            </p>
                        </div>
                    @else
                        {{-- Montant reçu --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-xs font-semibold text-gray-700">Montant reçu du client</label>
                                {{-- Bouton d'appoint rapide --}}
                                <button type="button"
                                        @click="setMontant(total)"
                                        class="text-[11px] text-primary hover:underline font-semibold">
                                    Montant exact (<span x-text="formatMoney(total)"></span> FCFA)
                                </button>
                            </div>
                            <div class="relative">
                                <input type="number"
                                       inputmode="numeric"
                                       x-model.number="montant"
                                       x-on:focus="$event.target.select()"
                                       x-on:keydown.enter.prevent="valider()"
                                       x-on:input.debounce.300ms="$wire.set('montantRecu', parseFloat(montant) || 0)"
                                       x-on:blur="$wire.set('montantRecu', parseFloat(montant) || 0)"
                                       placeholder="Saisir le montant..."
                                       class="w-full px-4 py-2.5 border-2 rounded-xl text-lg font-extrabold text-center focus:outline-none transition-colors border-gray-200 focus:border-primary bg-white {{ $errors->has('montantRecu') ? '!border-red-400 !bg-red-50' : '' }}"/>
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-bold">FCFA</span>
                            </div>
                            @error('montantRecu')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror

                            {{-- Coupures rapides FCFA --}}
                            @php
                                $billetsStandards = [1000, 2000, 5000, 10000];
                                $arrondiSup = ceil($total / 1000) * 1000;
                                if ($arrondiSup > $total && !in_array($arrondiSup, $billetsStandards)) {
                                    $billetsStandards[] = (int) $arrondiSup;
                                    sort($billetsStandards);
                                }
                                $billetsAAfficher = array_values(array_filter($billetsStandards, fn($b) => $b >= $total || $b >= 1000));
                                $billetsAAfficher = array_slice($billetsAAfficher, 0, 5);
                            @endphp
                            @if(count($billetsAAfficher) > 0)
                                <div class="flex items-center gap-1.5 mt-2 overflow-x-auto pb-0.5">
                                    <span class="text-[10px] text-gray-400 font-semibold mr-0.5 flex-shrink-0">Billets :</span>
                                    @foreach($billetsAAfficher as $billet)
                                        <button type="button"
                                                @click="setMontant({{ $billet }})"
                                                class="px-2.5 py-1 bg-gray-100 hover:bg-primary hover:text-white text-gray-700 text-xs font-bold rounded-lg transition-all flex-shrink-0 active:scale-95">
                                            {{ number_format($billet, 0, ',', ' ') }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Monnaie à rendre instantanée --}}
                        <div x-show="monnaie > 0"
                             x-transition
                             class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-center">
                            <p class="text-xs font-medium text-emerald-700">Monnaie à rendre au client</p>
                            <p class="text-2xl font-extrabold text-emerald-700 mt-0.5">
                                <span x-text="formatMoney(monnaie)"></span> FCFA
                            </p>
                        </div>
                    @endif

                    {{-- Notes --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Notes (optionnel)</label>
                        <input wire:model="notes" type="text"
                               placeholder="Ex: Table 4, emporter, etc."
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs bg-gray-50 focus:outline-none focus:border-primary transition-colors"/>
                    </div>
                </div>

                <div class="px-6 pb-6 pt-2 flex gap-3">
                    <button wire:click="$set('afficherModalPaiement', false)"
                            class="flex-1 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors border border-gray-200">
                        Annuler
                    </button>
                    <button @click="valider()"
                            type="button"
                            class="flex-1 py-3 bg-primary hover:bg-primary/95 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary/25 flex items-center justify-center gap-2">
                        <svg wire:loading wire:target="processPayment"
                             class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span>Valider la vente</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL SUCCÈS & RÉIMPRESSION TICKET (Fermeture par clic partout)
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($afficherModalSucces)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="$wire.set('afficherModalSucces', false)">
            {{-- Fond semi-transparent cliquable pour fermer --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm cursor-pointer"
                 wire:click="$set('afficherModalSucces', false)"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-7 text-center" @click.stop>
                {{-- Bouton croix en haut à droite --}}
                <button wire:click="$set('afficherModalSucces', false)"
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-dark mb-0.5">Vente validée !</h3>
                <p class="text-xs text-gray-400 mb-3">{{ $derniereReference }}</p>

                @if($monnaieRendue > 0)
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3 mb-4">
                        <p class="text-xs text-emerald-600 font-semibold">Monnaie rendue</p>
                        <p class="text-2xl font-extrabold text-emerald-700">
                            {{ number_format($monnaieRendue, 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                @endif

                <div class="space-y-2 mt-4">
                    @if($derniereVenteId)
                        <button type="button"
                                onclick="imprimerTicketDirect('{{ route('caisse.ticket', $derniereVenteId) }}?auto=1')"
                                class="w-full py-2.5 bg-primary hover:bg-primary/95 text-white text-xs sm:text-sm font-bold rounded-xl transition-all shadow-md shadow-primary/25 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            🖨️ Réimprimer le ticket
                        </button>
                    @endif

                    <div class="flex gap-2">
                        @if($derniereVenteId)
                            <a href="{{ route('factures.show', $derniereVenteId) }}" target="_blank"
                               class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition-colors flex items-center justify-center">
                                🧾 Voir facture
                            </a>
                        @endif
                        <button wire:click="$set('afficherModalSucces', false)"
                                class="flex-1 py-2.5 bg-secondary hover:bg-secondary/90 text-white text-xs font-bold rounded-xl transition-colors">
                            Nouvelle vente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Iframe invisible pour impression automatique immédiate --}}
    <iframe id="pos-ticket-frame" style="position: absolute; width: 0; height: 0; border: 0; visibility: hidden;"></iframe>

    @push('scripts')
    <script>
        window.imprimerTicketDirect = function(url) {
            const frame = document.getElementById('pos-ticket-frame');
            if (frame) {
                frame.src = url + (url.includes('?') ? '&' : '?') + 't=' + Date.now();
            } else {
                window.open(url, '_blank', 'width=380,height=600');
            }
        };

        function setupPosTicketListener() {
            if (window.Livewire) {
                Livewire.on('imprimer-ticket', (data) => {
                    const url = data.url || (Array.isArray(data) ? data[0]?.url : null) || (typeof data === 'string' ? data : null);
                    if (url) {
                        imprimerTicketDirect(url);
                    }
                });
            }
        }

        if (window.Livewire) {
            setupPosTicketListener();
        } else {
            document.addEventListener('livewire:init', setupPosTicketListener);
        }
    </script>
    @endpush
</div>