<div class="h-full w-full flex flex-col lg:flex-row gap-3 lg:gap-4 overflow-hidden min-h-0 flex-1">

    {{-- Erreur flash / message items --}}
    @error('items')
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 4000)"
             class="fixed top-20 right-6 z-50 flex items-center gap-3 bg-red-600 text-white px-5 py-3 rounded-2xl shadow-xl text-sm font-medium animate-bounce">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0"/>
            </svg>
            {{ $message }}
        </div>
    @enderror

    {{-- ══════════════════════════════════════════════════════════════════════
         COLONNE GAUCHE / CENTRE — CATALOGUE PRODUITS PLEINE HAUTEUR (70%)
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="flex-1 min-w-0 flex flex-col h-full bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- ── En-tête : Recherche & Filtres Catégories + Bouton Ligne Libre ── --}}
        <div class="p-3 sm:p-4 border-b border-gray-100 bg-white space-y-2.5 flex-shrink-0">
            <div class="flex items-center gap-2.5 sm:gap-3">
                {{-- Barre de recherche --}}
                <div class="relative flex-1">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input wire:model.live.debounce.250ms="searchProduit"
                           type="text"
                           placeholder="Rechercher un plat, burger, tacos, boisson..."
                           class="w-full pl-10 pr-9 py-2 sm:py-2.5 border border-gray-200 rounded-xl
                                  text-sm focus:outline-none focus:border-primary bg-gray-50 focus:bg-white transition-all"/>
                    @if($searchProduit)
                        <button wire:click="$set('searchProduit', '')"
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
                    <span>{{ count($allProduits) }} produit{{ count($allProduits) > 1 ? 's' : '' }}</span>
                </div>

                {{-- Bouton Ajout Ligne Libre / Personnalisée --}}
                <button type="button"
                        wire:click="openFreeLineModal"
                        class="flex items-center gap-1.5 px-3 py-2 sm:py-2.5 bg-secondary/10 hover:bg-secondary/15 text-secondary border border-secondary/20 rounded-xl text-xs font-bold transition-all flex-shrink-0 shadow-xs active:scale-95"
                        title="Ajouter une prestation ou produit hors catalogue">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="hidden md:inline">+ Ligne personnalisée</span>
                    <span class="md:hidden">+ Ligne</span>
                </button>
            </div>

            {{-- Filtres Catégories horizontaux --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-hide">
                <button type="button"
                        wire:click="$set('filterCategorie', '')"
                        @class([
                            'flex-shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5',
                            'bg-primary text-white shadow-md shadow-primary/25'
                                => $filterCategorie === '',
                            'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                => $filterCategorie !== '',
                        ])>
                    <span>Tous</span>
                </button>
                @foreach($categories as $cat)
                    <button type="button"
                            wire:click="$set('filterCategorie', '{{ $cat->id }}')"
                            @class([
                                'flex-shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5',
                                'bg-primary text-white shadow-md shadow-primary/25'
                                    => $filterCategorie == $cat->id,
                                'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                    => $filterCategorie != $cat->id,
                            ])>
                        <span>{{ $cat->nom }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ── Grille Produits PLEINE HAUTEUR DÉFILANTE ── --}}
        <div class="flex-1 min-h-0 overflow-y-auto p-3 sm:p-4 bg-gray-50/40">
            @if(count($allProduits) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-2.5 sm:gap-3.5">
                    @foreach($allProduits as $produit)
                        @php
                            $quoteItem = collect($items)->firstWhere('produit_id', $produit['id']);
                            $qtyInQuote = $quoteItem['quantite'] ?? 0;
                        @endphp

                        <button type="button"
                                wire:click="addToQuote({{ $produit['id'] }})"
                                @class([
                                    'group relative rounded-2xl border text-left transition-all duration-150 overflow-hidden flex flex-col',
                                    'bg-white border-primary ring-2 ring-primary/20 shadow-md -translate-y-0.5'
                                        => $qtyInQuote > 0,
                                    'bg-white border-gray-100 hover:border-primary/40 hover:shadow-lg hover:-translate-y-1'
                                        => $qtyInQuote === 0,
                                ])>

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

                                {{-- Badge Quantité dans le Devis --}}
                                @if($qtyInQuote > 0)
                                    <div class="absolute top-2 left-2 px-2 py-0.5 bg-primary text-white text-xs font-extrabold rounded-lg shadow flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>{{ $qtyInQuote }}</span>
                                    </div>
                                @endif

                                {{-- Badges Stock indicatif --}}
                                @if(! $produit['sur_commande'] && $produit['stock'] > 0 && $produit['stock'] <= 3)
                                    <div class="absolute top-2 right-2 bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md shadow-sm">
                                        {{ $produit['stock'] }} rest.
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
                    <p class="text-xs text-gray-400 mt-1">Modifiez votre recherche ou utilisez le bouton "+ Ligne personnalisée"</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         COLONNE DROITE — PANIER DU DEVIS & OPTIONS (330px)
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="w-full lg:w-80 xl:w-[340px] flex flex-col h-full bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex-shrink-0">

        {{-- ── En-tête : Client ── --}}
        <div class="p-3 border-b border-gray-100 bg-gray-50/60 flex-shrink-0">
            @if($clientId && $clientName)
                {{-- Client sélectionné --}}
                <div class="flex items-center justify-between bg-white rounded-xl border border-primary/20 px-3 py-2 shadow-sm">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 bg-primary text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($clientName, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-400 font-medium">Client du devis</p>
                            <p class="text-xs sm:text-sm font-bold text-dark truncate">{{ $clientName }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="clearClient"
                            class="text-gray-400 hover:text-red-500 p-1 rounded-lg hover:bg-red-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

            @elseif($useQuickClient)
                {{-- Saisie rapide --}}
                <div class="bg-white rounded-xl border border-secondary/30 p-2.5 shadow-sm space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-secondary flex items-center gap-1">
                            ✏️ Client rapide
                        </span>
                        <button type="button" wire:click="clearClient" class="text-xs text-gray-400 hover:text-red-500">
                            Annuler
                        </button>
                    </div>
                    <input wire:model="quickClientName"
                           type="text"
                           placeholder="Nom du client *"
                           class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs bg-gray-50 focus:outline-none focus:border-primary"/>
                    <input wire:model="quickClientPhone"
                           type="text"
                           placeholder="Téléphone / WhatsApp"
                           class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs bg-gray-50 focus:outline-none focus:border-primary"/>
                </div>

            @else
                {{-- Recherche / Bouton rapide --}}
                <div class="space-y-1.5">
                    <div class="relative">
                        <input wire:model.live.debounce.250ms="searchClient"
                               type="text"
                               placeholder="Rechercher un client (nom / tél)..."
                               class="w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-xl text-xs bg-white focus:outline-none focus:border-primary"/>
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>

                        @if(count($clientResults) > 0)
                            <div class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-gray-100 z-30 overflow-hidden max-h-48 overflow-y-auto">
                                @foreach($clientResults as $c)
                                    <button type="button"
                                            wire:click="selectClient({{ $c['id'] }}, '{{ addslashes($c['nom']) }}')"
                                            class="w-full flex items-center justify-between px-3 py-2 hover:bg-primary/5 transition-colors text-left border-b border-gray-50 last:border-0">
                                        <div class="truncate">
                                            <p class="text-xs font-bold text-dark truncate">{{ $c['nom'] }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $c['telephone'] }}</p>
                                        </div>
                                        <span class="text-[10px] text-primary font-semibold flex-shrink-0">Choisir</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-between px-1">
                        <button type="button" wire:click="$set('useQuickClient', true)"
                                class="text-[11px] font-semibold text-secondary hover:underline flex items-center gap-1">
                            <span>+ Saisie rapide client</span>
                        </button>
                        <span class="text-[10px] text-gray-400">Devis anonyme par défaut</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- ── En-tête Panier : Titre & Vider ── --}}
        <div class="px-4 py-2.5 border-b border-gray-100 bg-white flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="font-bold text-xs sm:text-sm text-dark">
                    Lignes du devis
                    <span class="text-gray-400 font-normal">({{ count($items) }})</span>
                </h3>
            </div>
            @if(count($items) > 0)
                <button type="button"
                        wire:click="$set('afficherConfirmationVider', true)"
                        class="text-xs text-red-500 hover:text-red-700 hover:bg-red-50 px-2 py-1 rounded-lg transition-colors font-medium">
                    Vider
                </button>
            @endif
        </div>

        {{-- ── Corps du Panier Défilant ── --}}
        <div class="flex-1 min-h-0 overflow-y-auto divide-y divide-gray-50 px-2 sm:px-3">
            @forelse($items as $index => $item)
                <div wire:key="quote-item-{{ $item['produit_id'] ?? 'free' }}-{{ $index }}"
                     class="py-2.5 px-2 flex items-center gap-2.5 hover:bg-gray-50/70 rounded-xl transition-colors">
                    {{-- Titre & Prix unitaire --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1">
                            @if($item['is_free_line'] ?? false)
                                <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 text-[9px] font-bold">Libre</span>
                            @endif
                            <p class="text-xs sm:text-sm font-bold text-dark truncate">
                                {{ $item['description'] }}
                            </p>
                        </div>
                        <p class="text-[11px] text-gray-400">
                            {{ number_format($item['prix_unitaire'], 0, ',', ' ') }} FCFA/u
                        </p>
                    </div>

                    {{-- Boutons & Saisie Quantité (directe et interactive) --}}
                    <div class="flex items-center gap-1 flex-shrink-0 bg-gray-100 rounded-lg p-0.5">
                        <button type="button"
                                wire:click="updateQuantity({{ $index }}, {{ $item['quantite'] - 1 }})"
                                class="w-6 h-6 flex items-center justify-center rounded-md bg-white hover:bg-gray-200 text-gray-700 font-extrabold text-xs shadow-xs transition-colors"
                                title="Diminuer">
                            −
                        </button>
                        <input type="number"
                               inputmode="numeric"
                               min="1"
                               value="{{ $item['quantite'] }}"
                               x-on:focus="$event.target.select()"
                               x-on:change="let val = parseInt($event.target.value); $wire.updateQuantity({{ $index }}, isNaN(val) ? 1 : val)"
                               x-on:keydown.enter.prevent="$event.target.blur()"
                               class="w-10 h-6 text-center text-xs font-bold text-dark bg-white border border-gray-200 hover:border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary rounded-md p-0 shadow-xs cursor-text [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none transition-all"
                               title="Cliquer pour saisir le nombre" />
                        <button type="button"
                                wire:click="updateQuantity({{ $index }}, {{ $item['quantite'] + 1 }})"
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
                    <button type="button"
                            wire:click="removeItem({{ $index }})"
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
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-xs sm:text-sm font-semibold text-gray-500">Devis vide</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Touchez un plat à gauche ou ajoutez une ligne</p>
                </div>
            @endforelse
        </div>

        {{-- ── Pied : Récapitulatif, Options & Bouton Enregistrer (Fixé) ── --}}
        <div class="p-3.5 bg-gray-50/90 border-t border-gray-100 flex-shrink-0 space-y-3">
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between text-gray-500">
                    <span>Sous-total</span>
                    <span class="font-medium text-dark">{{ number_format($sousTotal, 0, ',', ' ') }} FCFA</span>
                </div>

                {{-- Remise globale --}}
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Remise globale</span>
                    <div class="flex items-center gap-1">
                        <span class="text-gray-400 text-xs">−</span>
                        <input wire:model.live="discount"
                               type="number" min="0" step="100"
                               placeholder="0"
                               class="w-20 text-right px-2 py-0.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-primary bg-white"/>
                        <span class="text-[10px] text-gray-400">FCFA</span>
                    </div>
                </div>

                {{-- Total TTC en grand --}}
                <div class="border-t border-gray-200/80 pt-2 flex items-baseline justify-between">
                    <span class="text-xs sm:text-sm font-bold text-dark">TOTAL DU DEVIS</span>
                    <div class="text-right">
                        <span class="text-lg sm:text-xl font-extrabold text-primary">
                            {{ number_format($total, 0, ',', ' ') }}
                        </span>
                        <span class="text-xs font-semibold text-primary ml-0.5">FCFA</span>
                    </div>
                </div>
            </div>

            {{-- Bouton Options Devis (Validité & Notes) --}}
            <button type="button"
                    wire:click="$set('showOptionsModal', true)"
                    class="w-full py-2 px-3 bg-white border border-gray-200 hover:border-primary/40 rounded-xl text-xs font-medium text-gray-700 flex items-center justify-between shadow-xs transition-all">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Validité & Notes</span>
                </div>
                <div class="flex items-center gap-1 text-[11px] text-gray-500">
                    @if($valid_until)
                        <span class="bg-gray-100 px-1.5 py-0.5 rounded font-semibold">{{ \Carbon\Carbon::parse($valid_until)->format('d/m/Y') }}</span>
                    @endif
                    @if($notes)
                        <span class="w-2 h-2 rounded-full bg-primary" title="Note ajoutée"></span>
                    @endif
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </button>

            {{-- Bouton Enregistrer Devis --}}
            <button type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    @class([
                        'w-full py-3.5 rounded-xl font-bold text-sm sm:text-base transition-all flex items-center justify-center gap-2 shadow-lg',
                        'bg-primary hover:bg-primary/95 text-white shadow-primary/30 hover:shadow-primary/40 active:scale-[0.99]'
                            => count($items) > 0,
                        'bg-gray-200 text-gray-400 cursor-not-allowed shadow-none'
                            => count($items) === 0,
                    ])
                    @disabled(count($items) === 0)>
                <svg wire:loading wire:target="save" class="animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <svg wire:loading.remove wire:target="save" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                <span>
                    {{ $isEditing ? 'Modifier le devis' : 'Enregistrer le devis' }} &nbsp;{{ number_format($total, 0, ',', ' ') }} FCFA
                </span>
            </button>

            {{-- Lien retour --}}
            <div class="text-center pt-0.5">
                <a href="{{ route('devis.index') }}"
                   class="text-xs text-gray-500 hover:text-dark font-medium transition-colors">
                    &larr; Retour à la liste des devis
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL 1 : LIGNE PERSONNALISÉE / LIBRE
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($showFreeLineModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="$wire.closeFreeLineModal()">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm cursor-pointer"
                 wire:click="closeFreeLineModal"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10 space-y-4 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-secondary/10 flex items-center justify-center text-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </span>
                        <h3 class="font-bold text-dark text-base">Ajouter une ligne personnalisée</h3>
                    </div>
                    <button type="button" wire:click="closeFreeLineModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Désignation / Prestation *</label>
                        <input wire:model="freeLineDescription"
                               type="text"
                               placeholder="Ex: Formule buffet traiteur, Frais de livraison, Menu sur mesure..."
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary bg-gray-50 focus:bg-white"/>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Prix unitaire (FCFA) *</label>
                            <input wire:model="freeLinePrice"
                                   type="number"
                                   min="0"
                                   step="50"
                                   placeholder="0"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary bg-gray-50 focus:bg-white"/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Quantité *</label>
                            <input wire:model="freeLineQty"
                                   type="number"
                                   min="1"
                                   placeholder="1"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary bg-gray-50 focus:bg-white"/>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                    <button type="button"
                            wire:click="closeFreeLineModal"
                            class="px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                        Annuler
                    </button>
                    <button type="button"
                            wire:click="addFreeLine"
                            class="px-5 py-2.5 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-primary/25">
                        Ajouter au devis
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL 2 : OPTIONS DU DEVIS (VALIDITÉ & NOTES)
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($showOptionsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="$wire.set('showOptionsModal', false)">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm cursor-pointer"
                 wire:click="$set('showOptionsModal', false)"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10 space-y-4 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </span>
                        <h3 class="font-bold text-dark text-base">Options & Conditions du devis</h3>
                    </div>
                    <button type="button" wire:click="$set('showOptionsModal', false)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Date de fin de validité
                        </label>
                        <input wire:model="valid_until"
                               type="date"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary bg-gray-50 focus:bg-white"/>
                        <p class="text-[11px] text-gray-400 mt-1">Par défaut : 30 jours à compter d'aujourd'hui</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Notes / Conditions particulières
                        </label>
                        <textarea wire:model="notes"
                                  rows="4"
                                  placeholder="Conditions de livraison, acompte demandé, délais de préparation, mention légale..."
                                  class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary bg-gray-50 focus:bg-white resize-none"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end pt-3 border-t border-gray-100">
                    <button type="button"
                            wire:click="$set('showOptionsModal', false)"
                            class="px-5 py-2.5 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-primary/25">
                        Valider les options
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL 3 : CONFIRMATION VIDER DEVIS
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($afficherConfirmationVider)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="$wire.set('afficherConfirmationVider', false)">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm cursor-pointer"
                 wire:click="$set('afficherConfirmationVider', false)"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 z-10 text-center space-y-4 animate-in fade-in zoom-in-95 duration-150">
                <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                <div>
                    <h3 class="font-bold text-dark text-base">Vider le devis ?</h3>
                    <p class="text-xs text-gray-500 mt-1">Toutes les lignes sélectionnées seront retirées du devis.</p>
                </div>

                <div class="flex gap-2 justify-center pt-2">
                    <button type="button"
                            wire:click="$set('afficherConfirmationVider', false)"
                            class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                        Annuler
                    </button>
                    <button type="button"
                            wire:click="clearQuote"
                            class="px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors shadow-sm">
                        Oui, vider
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
