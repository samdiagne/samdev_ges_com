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

    {{-- Succès flash global --}}
    @if(session('pos_success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 3500)"
             class="fixed top-20 right-6 z-50 flex items-center gap-3 bg-green-600 text-white px-5 py-3 rounded-2xl shadow-xl text-sm font-medium">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('pos_success') }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         COLONNE GAUCHE / CENTRE — CATALOGUE PRODUITS PLEINE HAUTEUR (70%)
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="flex-1 min-w-0 flex flex-col h-full bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- ── En-tête : Recherche & Filtres Catégories ── --}}
        <div class="p-3 sm:p-4 border-b border-gray-100 bg-white space-y-2.5 flex-shrink-0">

            {{-- Barre d'état de la session de caisse --}}
            <div class="flex flex-wrap items-center justify-between gap-2 px-3 py-2 rounded-xl {{ $sessionActive ? 'bg-orange-50/70 border border-orange-100 text-orange-950' : 'bg-red-50 border border-red-100 text-red-900' }}">
                <div class="flex items-center gap-2 min-w-0">
                    @if($sessionActive)
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse flex-shrink-0"></span>
                        <div class="text-xs truncate">
                            <span class="font-bold text-gray-900">{{ $sessionActive->reference }}</span>
                            <span class="text-gray-400 mx-1">•</span>
                            <span class="text-gray-600">Ouvert à {{ $sessionActive->ouvert_le->format('H:i') }} ({{ $sessionActive->duree() }})</span>
                            <span class="text-gray-400 mx-1">•</span>
                            <span class="text-gray-600 font-medium">Espèces : <strong class="text-primary">{{ number_format($sessionActive->fond_caisse_ouverture, 0, ',', ' ') }} F</strong></span>
                            <span class="text-gray-400 mx-1">•</span>
                            <span class="text-gray-600 font-medium">Wave : <strong class="text-sky-600">{{ number_format($sessionActive->fond_caisse_wave_ouverture ?? 0, 0, ',', ' ') }} F</strong></span>
                        </div>
                    @else
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500 flex-shrink-0"></span>
                        <span class="text-xs font-semibold text-red-700">Caisse fermée — Aucune session active</span>
                    @endif
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if($sessionActive)
                        <button wire:click="preparerCloture"
                                type="button"
                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Clôturer la caisse
                        </button>
                    @else
                        <button wire:click="$set('afficherModalOuverture', true)"
                                type="button"
                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary hover:bg-primary/90 text-white text-xs font-bold rounded-lg shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>
                            Ouvrir la caisse
                        </button>
                    @endif

                    <a href="{{ route('caisse.rapport-journalier') }}"
                       title="Voir le rapport quotidien"
                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg border border-gray-200 transition-colors">
                        📊 Rapport du jour
                    </a>
                </div>
            </div>

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

                                {{-- Badges Stock --}}
                                @if(! $produit['sur_commande'] && $produit['stock'] > 0 && $produit['stock'] <= 3)
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
                        @if(! empty($item['details']))
                            <p class="text-[10px] text-orange-600 font-semibold truncate" title="{{ $item['details'] }}">
                                {{ $item['details'] }}
                            </p>
                        @endif
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
         MODAL COMPOSITION MENU FAST-FOOD (BURGER / TACOS + JUS)
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($afficherModalMenu)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/60 backdrop-blur-sm overflow-y-auto"
             @keydown.escape.window="$wire.set('afficherModalMenu', false)">
            <div class="absolute inset-0 cursor-pointer" wire:click="$set('afficherModalMenu', false)"></div>

            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden border border-gray-100 my-auto animate-in fade-in zoom-in-95 duration-200"
                 @click.stop>
                {{-- En-tête --}}
                <div class="p-4 sm:p-5 bg-gradient-to-r from-orange-500 via-primary to-orange-600 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-xl flex-shrink-0 shadow-inner">
                            🍔
                        </span>
                        <div>
                            <h3 class="font-extrabold text-base sm:text-lg leading-tight">{{ $menuProduitNom ?? 'Menu' }}</h3>
                            <p class="text-xs text-white/90">Personnalisez votre formule étape par étape</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="$set('afficherModalMenu', false)"
                            class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                        ✕
                    </button>
                </div>

                {{-- Corps du modal --}}
                <div class="p-5 space-y-5 max-h-[72vh] overflow-y-auto">
                    @forelse($menuEtapes as $stepIndex => $etape)
                        <div class="space-y-2.5 p-3.5 bg-gray-50/70 border border-gray-100 rounded-2xl">
                            {{-- Titre de l'étape --}}
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-extrabold uppercase tracking-wider text-gray-800 flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-orange-500 text-white text-[10px] flex items-center justify-center font-black">
                                        {{ $stepIndex + 1 }}
                                    </span>
                                    <span>{{ $etape['titre'] }}</span>
                                </label>
                                @if(($etape['type_selection'] ?? '') === 'texte_fixe')
                                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">Inclus</span>
                                @elseif(! empty($menuSelections[$stepIndex]))
                                    <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full border border-orange-100">Choisi ✓</span>
                                @endif
                            </div>

                            @if(($etape['type_selection'] ?? '') === 'texte_fixe')
                                <div class="p-2.5 bg-white rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 flex items-center gap-2">
                                    <span>{{ $etape['nom_fixe'] }}</span>
                                </div>
                            @elseif(! empty($etape['options']))
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @foreach($etape['options'] as $opt)
                                        @php
                                            $estChoisi = ($menuSelections[$stepIndex] ?? null) === $opt['id'];
                                        @endphp
                                        <button type="button"
                                                wire:click="selectMenuOption({{ $stepIndex }}, {{ $opt['id'] }})"
                                                @class([
                                                    'p-2.5 rounded-xl border text-left transition-all flex flex-col justify-between text-xs',
                                                    'border-orange-500 bg-orange-50/80 shadow-xs ring-1 ring-orange-400' => $estChoisi,
                                                    'border-gray-200 bg-white hover:border-orange-300 hover:bg-orange-50/30' => ! $estChoisi,
                                                ])>
                                            <div class="flex items-start justify-between gap-1">
                                                <span class="font-bold {{ $estChoisi ? 'text-orange-900' : 'text-gray-800' }} leading-tight line-clamp-2">
                                                    {{ $opt['nom'] }}
                                                </span>
                                                @if($estChoisi)
                                                    <span class="w-4 h-4 rounded-full bg-orange-500 text-white flex items-center justify-center text-[9px] font-bold flex-shrink-0">✓</span>
                                                @endif
                                            </div>
                                            @if(($opt['stock'] ?? 0) <= 0 && ! ($opt['sur_commande'] ?? false))
                                                <span class="text-[10px] text-amber-600 mt-1">Stock faible</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-gray-400 italic">Aucun produit configuré dans cette catégorie.</p>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-400 text-xs">
                            Aucune étape configurée pour ce menu. Rendez-vous dans la fiche produit pour ajouter des étapes.
                        </div>
                    @endforelse
                </div>

                {{-- Pied de modale : Calculateur & Validation --}}
                <div class="p-4 sm:p-5 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div>
                        <p class="text-[11px] text-gray-500 font-medium">
                            Total de la formule personnalisée :
                        </p>
                        <p class="text-2xl font-black text-primary leading-tight">
                            {{ number_format($menuPrixTotal, 0, ',', ' ') }}
                            <span class="text-sm font-bold text-gray-500">FCFA</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="button"
                                wire:click="$set('afficherModalMenu', false)"
                                class="flex-1 sm:flex-none px-4 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-200 rounded-xl transition-colors">
                            Annuler
                        </button>
                        <button type="button"
                                wire:click="validerAjoutMenu"
                                class="flex-1 sm:flex-none px-6 py-2.5 bg-primary hover:bg-primary/95 text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-primary/25 transition-all flex items-center justify-center gap-2">
                            <span>Ajouter au ticket</span>
                            <span>•</span>
                            <span>{{ number_format($menuPrixTotal, 0, ',', ' ') }} F</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL SÉLECTION PARFUM BOISSON / JUS (250ml, 1L, Canette 33cl)
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($afficherModalBoisson)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/60 backdrop-blur-sm overflow-y-auto"
             @keydown.escape.window="$wire.set('afficherModalBoisson', false)">
            <div class="absolute inset-0 cursor-pointer" wire:click="$set('afficherModalBoisson', false)"></div>

            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden border border-gray-100 my-auto animate-in fade-in zoom-in-95 duration-200"
                 @click.stop>
                {{-- En-tête --}}
                <div class="p-4 sm:p-5 bg-gradient-to-r from-emerald-600 via-primary to-orange-500 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-xl flex-shrink-0 shadow-inner">
                            {{ $boissonIcone }}
                        </span>
                        <div>
                            <h3 class="font-extrabold text-base sm:text-lg leading-tight">{{ $boissonTitre }}</h3>
                            <p class="text-xs text-white/90">Cliquez sur un parfum pour l'ajouter directement au ticket</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="$set('afficherModalBoisson', false)"
                            class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                        ✕
                    </button>
                </div>

                {{-- Corps du modal : Grille des choix --}}
                <div class="p-5 max-h-[70vh] overflow-y-auto">
                    @if(! empty($listeOptionsBoisson))
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($listeOptionsBoisson as $opt)
                                @php
                                    $isDispo = $opt['sur_commande'] || $opt['stock'] > 0;
                                @endphp
                                <button type="button"
                                        wire:click="choisirOptionBoisson({{ $opt['id'] }})"
                                        @disabled(! $isDispo)
                                        @class([
                                            'p-3.5 rounded-2xl border text-left flex flex-col justify-between transition-all group relative overflow-hidden',
                                            'border-gray-200 hover:border-primary hover:bg-orange-50/40 hover:shadow-md cursor-pointer active:scale-95' => $isDispo,
                                            'opacity-40 border-gray-200 bg-gray-50 cursor-not-allowed' => ! $isDispo,
                                        ])>
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="w-10 h-10 rounded-xl bg-orange-100/70 text-orange-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:bg-primary group-hover:text-white transition-colors">
                                            @if($boissonType === 'gazeuse')
                                                🥤
                                            @elseif($boissonType === 'jus_1l')
                                                🍾
                                            @else
                                                🧃
                                            @endif
                                        </div>
                                        @if($opt['sur_commande'])
                                            <span class="text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full font-bold">
                                                Dispo
                                            </span>
                                        @elseif($opt['stock'] > 0)
                                            <span class="text-[10px] text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full font-bold">
                                                {{ $opt['stock'] }} rest.
                                            </span>
                                        @else
                                            <span class="text-[10px] text-red-600 bg-red-50 px-2 py-0.5 rounded-full font-bold">
                                                Épuisé
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-xs sm:text-sm font-black text-gray-900 group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                                            {{ $opt['nom'] }}
                                        </p>
                                        <p class="text-[11px] font-bold text-gray-400 mt-1">
                                            {{ number_format($opt['prix_vente'] > 0 ? $opt['prix_vente'] : $boissonPrix, 0, ',', ' ') }} FCFA
                                        </p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 text-center text-gray-400">
                            <p class="text-sm font-semibold">Aucune boisson disponible pour cette sélection.</p>
                        </div>
                    @endif
                </div>

                {{-- Pied du modal --}}
                <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-500 font-medium">
                        {{ count($listeOptionsBoisson) }} option{{ count($listeOptionsBoisson) > 1 ? 's' : '' }} disponible{{ count($listeOptionsBoisson) > 1 ? 's' : '' }}
                    </p>
                    <button type="button"
                            wire:click="$set('afficherModalBoisson', false)"
                            class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-200 rounded-xl transition-colors">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif

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
                     canalMonnaie: 'especes',
                     partWave: 0,
                     get monnaie() {
                         let m = parseFloat(this.montant) || 0;
                         return m > this.total ? Math.round(m - this.total) : 0;
                     },
                     get monnaieWave() {
                         if (this.monnaie <= 0) return 0;
                         if (this.canalMonnaie === 'wave') return this.monnaie;
                         if (this.canalMonnaie === 'especes') return 0;
                         return Math.min(this.monnaie, Math.max(0, parseFloat(this.partWave) || 0));
                     },
                     get monnaieEspeces() {
                         return Math.max(0, this.monnaie - this.monnaieWave);
                     },
                     formatMoney(val) {
                         return new Intl.NumberFormat('fr-FR').format(Math.round(val));
                     },
                     setMontant(val) {
                         this.montant = val;
                         $wire.set('montantRecu', val);
                     },
                     setCanal(c) {
                         this.canalMonnaie = c;
                         if (c === 'wave') {
                             this.partWave = this.monnaie;
                         } else if (c === 'especes') {
                             this.partWave = 0;
                         }
                     },
                     valider() {
                         let m = parseFloat(this.montant) || 0;
                         let mw = this.monnaieWave;
                         $wire.processPayment(m, mw);
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

                        {{-- Monnaie à rendre instantanée et choix du canal (Espèces vs Wave) --}}
                        <div x-show="monnaie > 0"
                             x-transition
                             class="bg-emerald-50/80 border border-emerald-200 rounded-2xl p-3.5 space-y-3">
                            <div class="text-center">
                                <p class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Monnaie totale à rendre</p>
                                <p class="text-2xl font-black text-emerald-800 mt-0.5">
                                    <span x-text="formatMoney(monnaie)"></span> FCFA
                                </p>
                            </div>

                            {{-- Sélecteur de canal de rendu de monnaie --}}
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1 text-center">
                                    Où faites-vous la monnaie ?
                                </label>
                                <div class="grid grid-cols-3 gap-1.5">
                                    <button type="button"
                                            @click="setCanal('especes')"
                                            :class="canalMonnaie === 'especes' ? 'bg-emerald-600 text-white shadow-md border-emerald-600' : 'bg-white text-gray-700 border-gray-200 hover:border-emerald-300'"
                                            class="py-1.5 px-1 rounded-xl text-xs font-bold border transition-all text-center flex flex-col items-center gap-0.5">
                                        <span>💵 Espèces</span>
                                        <span class="text-[9px] font-normal opacity-85">100% tiroir</span>
                                    </button>

                                    <button type="button"
                                            @click="setCanal('wave')"
                                            :class="canalMonnaie === 'wave' ? 'bg-sky-600 text-white shadow-md border-sky-600' : 'bg-white text-gray-700 border-gray-200 hover:border-sky-300'"
                                            class="py-1.5 px-1 rounded-xl text-xs font-bold border transition-all text-center flex flex-col items-center gap-0.5">
                                        <span>📱 Wave</span>
                                        <span class="text-[9px] font-normal opacity-85">100% transfert</span>
                                    </button>

                                    <button type="button"
                                            @click="setCanal('mixte')"
                                            :class="canalMonnaie === 'mixte' ? 'bg-purple-600 text-white shadow-md border-purple-600' : 'bg-white text-gray-700 border-gray-200 hover:border-purple-300'"
                                            class="py-1.5 px-1 rounded-xl text-xs font-bold border transition-all text-center flex flex-col items-center gap-0.5">
                                        <span>🔀 Mixte</span>
                                        <span class="text-[9px] font-normal opacity-85">Espèces + Wave</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Saisie de la part Wave si Mixte --}}
                            <div x-show="canalMonnaie === 'mixte'" x-transition class="bg-white p-2.5 rounded-xl border border-purple-200 space-y-1.5">
                                <label class="block text-xs font-bold text-purple-900 flex items-center justify-between">
                                    <span>Part rendue par transfert Wave :</span>
                                    <span class="text-[10px] text-gray-400 font-normal">Max: <span x-text="formatMoney(monnaie)"></span> F</span>
                                </label>
                                <div class="relative">
                                    <input type="number"
                                           x-model.number="partWave"
                                           :max="monnaie"
                                           min="0"
                                           placeholder="Ex: 500"
                                           class="w-full px-3 py-1.5 text-sm font-bold border border-purple-300 rounded-lg text-purple-900 focus:outline-none focus:ring-1 focus:ring-purple-500 bg-purple-50/20"/>
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-purple-400">FCFA</span>
                                </div>
                            </div>

                            {{-- Détail visuel de la répartition --}}
                            <div class="grid grid-cols-2 gap-2 text-xs font-bold pt-1 border-t border-emerald-200/60">
                                <div class="bg-white p-2 rounded-lg border border-emerald-100 flex items-center justify-between">
                                    <span class="text-gray-600 font-medium">💵 Espèces :</span>
                                    <span class="text-emerald-700"><span x-text="formatMoney(monnaieEspeces)"></span> F</span>
                                </div>
                                <div class="bg-sky-50 p-2 rounded-lg border border-sky-100 flex items-center justify-between">
                                    <span class="text-sky-800 font-medium">📱 Wave :</span>
                                    <span class="text-sky-700"><span x-text="formatMoney(monnaieWave)"></span> F</span>
                                </div>
                            </div>
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
                    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-3.5 mb-4 space-y-2">
                        <p class="text-xs text-emerald-600 font-bold uppercase tracking-wider">Monnaie rendue</p>
                        <p class="text-2xl font-black text-emerald-700">
                            {{ number_format($monnaieRendue, 0, ',', ' ') }} FCFA
                        </p>
                        @if($monnaieRendueWave > 0)
                            <div class="pt-2 border-t border-emerald-200/60 grid grid-cols-2 gap-2 text-xs text-left">
                                <div class="bg-white p-2 rounded-xl border border-emerald-100 font-semibold text-gray-700">
                                    <div class="text-[10px] text-gray-400 font-normal">En Espèces</div>
                                    <div class="text-emerald-700 font-bold text-sm">{{ number_format($monnaieRendueEspeces, 0, ',', ' ') }} F</div>
                                </div>
                                <div class="bg-sky-50 p-2 rounded-xl border border-sky-100 font-semibold text-sky-900">
                                    <div class="text-[10px] text-sky-500 font-normal">Via Wave</div>
                                    <div class="text-sky-700 font-bold text-sm">{{ number_format($monnaieRendueWave, 0, ',', ' ') }} F</div>
                                </div>
                            </div>
                            <div class="bg-sky-100/70 border border-sky-200 rounded-xl p-2 text-[11px] text-sky-800 font-medium text-left flex items-start gap-1.5">
                                <span>📱</span>
                                <span>Effectuez le transfert Wave de <strong>{{ number_format($monnaieRendueWave, 0, ',', ' ') }} FCFA</strong> vers le numéro du client.</span>
                            </div>
                        @endif
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

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL 1 : OUVERTURE DE CAISSE
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($afficherModalOuverture)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 animate-in fade-in zoom-in-95 duration-200">
                <div class="p-5 bg-gradient-to-r from-primary to-orange-600 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 rounded-xl bg-white/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="font-bold text-base leading-tight">Ouverture de Caisse</h3>
                            <p class="text-xs text-white/80">Saisie du fond de caisse initial</p>
                        </div>
                    </div>
                    @if($sessionCaisseId)
                        <button type="button" wire:click="$set('afficherModalOuverture', false)" class="text-white/80 hover:text-white p-1">
                            ✕
                        </button>
                    @endif
                </div>

                <form wire:submit.prevent="ouvrirSession" class="p-5 space-y-4">
                    <div class="bg-orange-50 border border-orange-100 rounded-2xl p-3.5 text-xs text-orange-900 flex items-start gap-2.5">
                        <span class="text-base flex-shrink-0">💡</span>
                        <span>Renseignez les fonds de départ pour démarrer les encaissements et assurer un inventaire rigoureux en fin de service.</span>
                    </div>

                    {{-- Fond espèces --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <span>💵 Fond de caisse Espèces (Tiroir)</span>
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" step="any" min="0"
                                   wire:model="fondCaisseOuverture"
                                   placeholder="Ex: 50 000"
                                   class="w-full px-4 py-2.5 text-base font-bold border border-gray-200 rounded-2xl focus:ring-primary focus:border-primary">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">FCFA</span>
                        </div>
                        @error('fondCaisseOuverture')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror

                        {{-- Raccourcis montants fréquents --}}
                        <div class="grid grid-cols-4 gap-1.5 mt-1.5">
                            @foreach([0, 25000, 50000, 100000] as $montant)
                                <button type="button"
                                        wire:click="$set('fondCaisseOuverture', '{{ $montant }}')"
                                        class="py-1 px-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition-colors">
                                    {{ number_format($montant, 0, ',', ' ') }} F
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Fond Wave --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span class="flex items-center gap-1.5 text-sky-700">
                                <span>📱 Solde initial Wave (Compte caissier)</span>
                                <span class="text-red-500">*</span>
                            </span>
                            <span class="text-[10px] text-gray-400 font-normal lowercase">(pour faire la monnaie Wave)</span>
                        </label>
                        <div class="relative">
                            <input type="number" step="any" min="0"
                                   wire:model="fondCaisseWaveOuverture"
                                   placeholder="Ex: 25 000 (ou 0)"
                                   class="w-full px-4 py-2.5 text-base font-bold border border-sky-200 bg-sky-50/30 rounded-2xl focus:ring-sky-500 focus:border-sky-500 text-sky-900">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-sky-400">FCFA</span>
                        </div>
                        @error('fondCaisseWaveOuverture')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror

                        {{-- Raccourcis montants fréquents Wave --}}
                        <div class="grid grid-cols-4 gap-1.5 mt-1.5">
                            @foreach([0, 10000, 25000, 50000] as $montant)
                                <button type="button"
                                        wire:click="$set('fondCaisseWaveOuverture', '{{ $montant }}')"
                                        class="py-1 px-2 bg-sky-50 hover:bg-sky-100 text-sky-800 text-xs font-semibold rounded-xl transition-colors border border-sky-100">
                                    {{ number_format($montant, 0, ',', ' ') }} F
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-2 flex gap-2">
                        @if($sessionCaisseId)
                            <button type="button"
                                    wire:click="$set('afficherModalOuverture', false)"
                                    class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-2xl transition-colors">
                                Annuler
                            </button>
                        @endif
                        <button type="submit"
                                class="flex-1 py-3 bg-primary hover:bg-primary/95 text-white text-sm font-bold rounded-2xl shadow-lg shadow-primary/25 transition-all flex items-center justify-center gap-2">
                            <span>Démarrer la session</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL 2 : CLÔTURE DE CAISSE & CALCUL DES ÉCARTS
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($afficherModalCloture && $resumeCloture)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-100 animate-in fade-in zoom-in-95 duration-200 my-auto">
                <div class="p-4 sm:p-5 bg-gradient-to-r from-red-600 to-rose-700 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 rounded-xl bg-white/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="font-bold text-base leading-tight">Clôture de Caisse</h3>
                            <p class="text-xs text-white/80">Session {{ $resumeCloture['reference'] }} • Durée : {{ $resumeCloture['duree'] }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('afficherModalCloture', false)" class="text-white/80 hover:text-white p-1 text-sm">
                        ✕
                    </button>
                </div>

                <div class="p-5 space-y-4 max-h-[78vh] overflow-y-auto">
                    {{-- Récapitulatif des encaissements --}}
                    <div class="bg-gray-50 rounded-2xl p-3.5 border border-gray-100 space-y-2">
                        <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            Synthèse des ventes de la session
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="flex justify-between bg-white p-2 rounded-lg border border-gray-100">
                                <span class="text-gray-600">💵 Espèces :</span>
                                <strong class="text-gray-900">{{ number_format($resumeCloture['ventes_cash'], 0, ',', ' ') }} F</strong>
                            </div>
                            <div class="flex justify-between bg-white p-2 rounded-lg border border-gray-100">
                                <span class="text-gray-600">Wave :</span>
                                <strong class="text-sky-600">{{ number_format($resumeCloture['ventes_wave'], 0, ',', ' ') }} F</strong>
                            </div>
                            <div class="flex justify-between bg-white p-2 rounded-lg border border-gray-100">
                                <span class="text-gray-600">Orange M. :</span>
                                <strong class="text-orange-600">{{ number_format($resumeCloture['ventes_om'], 0, ',', ' ') }} F</strong>
                            </div>
                            <div class="flex justify-between bg-white p-2 rounded-lg border border-gray-100">
                                <span class="text-gray-600">💳 Carte :</span>
                                <strong class="text-purple-600">{{ number_format($resumeCloture['ventes_carte'], 0, ',', ' ') }} F</strong>
                            </div>
                        </div>
                        <div class="flex justify-between text-xs pt-1 border-t border-gray-200 font-bold">
                            <span class="text-gray-700">Total Ventes Encaissées :</span>
                            <span class="text-primary text-sm">{{ number_format($resumeCloture['total_ventes'], 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>

                    {{-- Calcul théorique des espèces --}}
                    <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-3.5 text-xs space-y-1.5">
                        <div class="font-bold text-amber-900 text-[11px] uppercase tracking-wider flex items-center justify-between">
                            <span>💵 1. Bilan Tiroir-Caisse (Espèces)</span>
                            <span class="text-[10px] text-amber-700 font-semibold">Tiroir physique</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Fond initial à l'ouverture :</span>
                            <span class="font-semibold">{{ number_format($resumeCloture['fond_initial'], 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>+ Recettes en espèces :</span>
                            <span class="font-semibold text-green-700">+ {{ number_format($resumeCloture['ventes_cash'], 0, ',', ' ') }} FCFA</span>
                        </div>
                        @if(($resumeCloture['monnaie_rendue_wave'] ?? 0) > 0)
                            <div class="flex justify-between text-emerald-800 bg-emerald-100/60 p-1.5 rounded-lg border border-emerald-200/60 font-medium">
                                <span class="flex items-center gap-1">
                                    <span>➕ Monnaie compensée par Wave :</span>
                                    <span class="text-[10px] text-emerald-600 font-normal">(gardée en espèces)</span>
                                </span>
                                <span class="font-bold text-emerald-700">+ {{ number_format($resumeCloture['monnaie_rendue_wave'], 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-gray-700">
                            <span>- Dépenses décaissées en espèces :</span>
                            <span class="font-semibold text-red-600">- {{ number_format($resumeCloture['depenses_cash'], 0, ',', ' ') }} FCFA</span>
                        </div>
                        @if(($resumeCloture['achats_cash'] ?? 0) > 0)
                            <div class="flex justify-between text-orange-800 bg-orange-50/70 p-1.5 rounded-lg border border-orange-200/60 font-medium">
                                <span class="flex items-center gap-1">
                                    <span>🛒 Achats payés en espèces :</span>
                                </span>
                                <span class="font-semibold text-orange-700">- {{ number_format($resumeCloture['achats_cash'], 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif
                        <div class="flex justify-between pt-1 border-t border-amber-200 text-xs sm:text-sm font-bold text-amber-950">
                            <span>Solde Espèces Théorique :</span>
                            <span>{{ number_format($resumeCloture['theorique_cash'], 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>

                    {{-- Saisie des espèces réelles & écart espèces --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1 flex items-center justify-between">
                            <span>Espèces physiques comptées <span class="text-red-500">*</span></span>
                            <button type="button"
                                    wire:click="$set('montantReelEspeces', '{{ $resumeCloture['theorique_cash'] }}')"
                                    class="text-[10px] text-primary hover:underline font-bold capitalize">
                                Copier théorique ({{ number_format($resumeCloture['theorique_cash'], 0, ',', ' ') }} F)
                            </button>
                        </label>
                        <div class="relative">
                            <input type="number" step="any" min="0"
                                   wire:model.live.debounce.150ms="montantReelEspeces"
                                   placeholder="Saisissez le total physique compté"
                                   class="w-full px-4 py-2.5 text-base font-bold border-2 rounded-2xl focus:ring-primary focus:border-primary {{ $resumeCloture['ecart'] < 0 ? 'border-red-300 bg-red-50/30' : ($resumeCloture['ecart'] > 0 ? 'border-blue-300 bg-blue-50/30' : 'border-green-300 bg-green-50/30') }}">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">FCFA</span>
                        </div>
                        @error('montantReelEspeces')
                            <p class="text-xs text-red-600 mt-0.5 font-semibold">{{ $message }}</p>
                        @enderror

                        {{-- Badge Écart Espèces --}}
                        @php $ecartCloture = (float) $resumeCloture['ecart']; @endphp
                        <div class="p-2.5 rounded-xl text-xs flex items-center justify-between font-bold {{ $ecartCloture === 0.0 ? 'bg-green-100 text-green-900 border border-green-200' : ($ecartCloture > 0 ? 'bg-blue-100 text-blue-900 border border-blue-200' : 'bg-red-100 text-red-900 border border-red-200') }}">
                            <span>
                                @if($ecartCloture === 0.0)
                                    ✓ Espèces conformes (0 F)
                                @elseif($ecartCloture > 0)
                                    ▲ Excédent espèces constaté
                                @else
                                    ▼ Manquant espèces (Déficit)
                                @endif
                            </span>
                            <span class="text-sm">
                                {{ $ecartCloture > 0 ? '+' : '' }}{{ number_format($ecartCloture, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                    </div>

                    {{-- Calcul théorique Wave --}}
                    <div class="bg-sky-50/70 border border-sky-200/80 rounded-2xl p-3.5 text-xs space-y-1.5 mt-3">
                        <div class="font-bold text-sky-950 text-[11px] uppercase tracking-wider flex items-center justify-between">
                            <span>📱 2. Bilan Compte Wave (Caissier)</span>
                            <span class="text-[10px] text-sky-700 font-semibold">Compte mobile</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Solde initial Wave :</span>
                            <span class="font-semibold">{{ number_format($resumeCloture['fond_wave_initial'] ?? 0, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>+ Encaissements Wave reçus :</span>
                            <span class="font-semibold text-sky-700">+ {{ number_format($resumeCloture['ventes_wave'], 0, ',', ' ') }} FCFA</span>
                        </div>
                        @if(($resumeCloture['monnaie_rendue_wave'] ?? 0) > 0)
                            <div class="flex justify-between text-red-700 bg-red-50 p-1.5 rounded-lg border border-red-100 font-medium">
                                <span>- Monnaies rendues via Wave :</span>
                                <span class="font-bold text-red-600">- {{ number_format($resumeCloture['monnaie_rendue_wave'], 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif
                        @if(($resumeCloture['depenses_wave'] ?? 0) > 0)
                            <div class="flex justify-between text-gray-700">
                                <span>- Dépenses payées par Wave :</span>
                                <span class="font-semibold text-red-600">- {{ number_format($resumeCloture['depenses_wave'], 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif
                        @if(($resumeCloture['achats_wave'] ?? 0) > 0)
                            <div class="flex justify-between text-orange-800 bg-orange-50/70 p-1.5 rounded-lg border border-orange-200/60 font-medium">
                                <span>🛒 Achats payés par Wave :</span>
                                <span class="font-semibold text-orange-700">- {{ number_format($resumeCloture['achats_wave'], 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif
                        <div class="flex justify-between pt-1 border-t border-sky-200 text-xs sm:text-sm font-bold text-sky-950">
                            <span>Solde Wave Théorique :</span>
                            <span>{{ number_format($resumeCloture['theorique_wave'] ?? 0, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>

                    {{-- Saisie solde Wave réel & écart Wave --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1 flex items-center justify-between">
                            <span>Solde final réel sur compte Wave <span class="text-red-500">*</span></span>
                            <button type="button"
                                    wire:click="$set('montantReelWave', '{{ $resumeCloture['theorique_wave'] ?? 0 }}')"
                                    class="text-[10px] text-sky-600 hover:underline font-bold capitalize">
                                Copier théorique ({{ number_format($resumeCloture['theorique_wave'] ?? 0, 0, ',', ' ') }} F)
                            </button>
                        </label>
                        <div class="relative">
                            <input type="number" step="any" min="0"
                                   wire:model.live.debounce.150ms="montantReelWave"
                                   placeholder="Solde affiché sur l'application Wave"
                                   class="w-full px-4 py-2.5 text-base font-bold border-2 rounded-2xl focus:ring-sky-500 focus:border-sky-500 {{ ($resumeCloture['ecart_wave'] ?? 0) < 0 ? 'border-red-300 bg-red-50/30 text-red-900' : (($resumeCloture['ecart_wave'] ?? 0) > 0 ? 'border-blue-300 bg-blue-50/30 text-sky-900' : 'border-green-300 bg-green-50/30 text-sky-900') }}">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-sky-400">FCFA</span>
                        </div>
                        @error('montantReelWave')
                            <p class="text-xs text-red-600 mt-0.5 font-semibold">{{ $message }}</p>
                        @enderror

                        {{-- Badge Écart Wave --}}
                        @php $ecartWave = (float) ($resumeCloture['ecart_wave'] ?? 0); @endphp
                        <div class="p-2.5 rounded-xl text-xs flex items-center justify-between font-bold {{ $ecartWave === 0.0 ? 'bg-green-100 text-green-900 border border-green-200' : ($ecartWave > 0 ? 'bg-blue-100 text-blue-900 border border-blue-200' : 'bg-red-100 text-red-900 border border-red-200') }}">
                            <span>
                                @if($ecartWave === 0.0)
                                    ✓ Solde Wave conforme (0 F)
                                @elseif($ecartWave > 0)
                                    ▲ Excédent Wave constaté
                                @else
                                    ▼ Manquant Wave (Déficit)
                                @endif
                            </span>
                            <span class="text-sm">
                                {{ $ecartWave > 0 ? '+' : '' }}{{ number_format($ecartWave, 0, ',', ' ') }} FCFA
                            </span>
                        </div>

                        {{-- Notes / Justifications --}}
                        <div class="pt-1">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">
                                Justification / Observations (optionnel)
                            </label>
                            <textarea wire:model="notesCloture"
                                      rows="2"
                                      placeholder="Explication d'un écart, remarque sur le service..."
                                      class="w-full p-2.5 text-xs border border-gray-200 rounded-xl focus:ring-primary focus:border-primary"></textarea>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-2 flex gap-2">
                        <button type="button"
                                wire:click="$set('afficherModalCloture', false)"
                                class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-2xl transition-colors">
                            Retour au POS
                        </button>
                        <button type="button"
                                wire:click="cloturerSession"
                                class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-bold rounded-2xl shadow-lg shadow-red-600/25 transition-all flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Confirmer la clôture</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL 3 : SUCCÈS CLÔTURE DE CAISSE & ACTIONS (TICKET Z, RAPPORT)
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($afficherModalSuccesCloture)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6 space-y-4 border border-gray-100 animate-in fade-in zoom-in-95 duration-200">
                <div class="w-16 h-16 rounded-full bg-green-100 text-green-600 mx-auto flex items-center justify-center text-3xl">
                    ✓
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Session Clôturée avec Succès !</h3>
                    <p class="text-xs text-gray-500 mt-1">Les chiffres ont été enregistrés et figés dans le journal comptable.</p>
                </div>

                <div class="space-y-2 pt-2">
                    @if($derniereSessionClotureeId)
                        <a href="{{ route('caisse.sessions.ticket-z', $derniereSessionClotureeId) }}"
                           target="_blank"
                           class="w-full py-2.5 bg-primary hover:bg-primary/95 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-primary/25 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Télécharger Ticket Z (PDF)</span>
                        </a>
                    @endif

                    <a href="{{ route('caisse.rapport-journalier') }}"
                       class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                        📊 Voir mon Rapport Quotidien
                    </a>

                    <button wire:click="$set('afficherModalSuccesCloture', false); $set('afficherModalOuverture', true);"
                            class="w-full py-2.5 bg-secondary hover:bg-secondary/90 text-white text-xs sm:text-sm font-bold rounded-xl transition-colors">
                        Ouvrir une nouvelle session
                    </button>
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