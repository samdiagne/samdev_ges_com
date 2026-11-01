<div>
    {{-- Stats cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-dark mt-1">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Produits</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Actifs</p>
            <p class="text-2xl font-bold text-primary mt-1">{{ $stats['actifs'] }}</p>
            <p class="text-xs text-gray-400 mt-1">au menu</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Stock bas</p>
            <p class="text-2xl font-bold text-secondary mt-1">{{ $stats['faible'] }}</p>
            <p class="text-xs text-gray-400 mt-1">à réapprovisionner</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Rupture</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ $stats['rupture'] }}</p>
            <p class="text-xs text-gray-400 mt-1">indisponible(s)</p>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <div>
            <h3 class="text-lg font-bold text-dark">Produits</h3>
            <p class="text-sm text-gray-400">{{ $produits->total() }} Produit(s) trouvé(s)</p>
        </div>
        <a href="{{ route('produits.create', ['return_url' => $returnUrl]) }}"
           class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90
                  text-white font-medium px-4 py-2.5 rounded-xl transition-all
                  shadow-lg shadow-primary/25 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau Produit
        </a>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="relative sm:col-span-2 lg:col-span-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Nom du Produit..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl
                              text-sm focus:outline-none focus:border-primary bg-gray-50"/>
            </div>
            <select wire:model.live="filtreCategorie"
                    class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-primary bg-gray-50">
                <option value="">Toutes catégories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nom }}</option>
                @endforeach
            </select>
            <select wire:model.live="filtreStock"
                    class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-primary bg-gray-50">
                <option value="">Tout le stock</option>
                <option value="ok">Stock OK</option>
                <option value="faible">Stock bas</option>
                <option value="rupture">Rupture</option>
            </select>
            <select wire:model.live="filtreStatut"
                    class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-primary bg-gray-50">
                <option value="">Tous statuts</option>
                <option value="1">Actifs</option>
                <option value="0">Inactifs</option>
            </select>
        </div>

        @if($search || $filtreCategorie || $filtreStock || $filtreStatut !== '')
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    Filtres actifs
                </span>
                <button wire:click="resetFilters" type="button"
                        class="text-primary hover:text-primary/80 font-semibold hover:underline flex items-center gap-1 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Réinitialiser les filtres
                </button>
            </div>
        @endif
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200
                    text-green-800 px-4 py-3 rounded-xl text-sm font-medium">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Produit</th>
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 hidden md:table-cell">
                            Catégorie
                        </th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Prix</th>
                        <th class="text-center text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Stock</th>
                        <th class="text-center text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Statut</th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 sticky right-0 bg-gray-50 border-l border-gray-100">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($produits as $produit)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            {{-- Produit --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex-shrink-0
                                                overflow-hidden">
                                        @if($produit->image_principale)
                                            <img src="{{ Storage::url($produit->image_principale) }}"
                                                 class="w-full h-full object-cover"/>
                                        @else
                                            <div class="w-full h-full flex items-center
                                                        justify-center text-gray-400">
                                                <svg class="w-5 h-5" fill="none"
                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0
                                                             00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 max-w-xs">
                                        <p class="text-sm font-semibold text-dark break-words">
                                            {{ $produit->nom }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ $produit->categorie->nom ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Catégorie --}}
                            <td class="px-4 py-3 hidden md:table-cell">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg
                                             bg-accent/10 text-primary text-xs font-medium">
                                    {{ $produit->categorie->nom ?? '—' }}
                                </span>
                            </td>

                            {{-- Prix --}}
                            <td class="px-4 py-3 text-right">
                                <p class="text-sm font-bold text-dark">
                                    {{ number_format($produit->prix_vente, 0, ',', ' ') }}
                                    <span class="text-xs font-normal text-gray-400">FCFA</span>
                                </p>
                                @if($produit->estEnPromotion())
                                    <p class="text-xs text-gray-400 line-through">
                                        {{ number_format($produit->ancien_prix, 0, ',', ' ') }}
                                    </p>
                                @endif
                            </td>

                            {{-- Stock --}}
                            <td class="px-4 py-3 text-center">
                                @if($produit->sur_commande)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1
                                                 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                        Sur commande
                                    </span>
                                @elseif($produit->stock <= 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1
                                                 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                        Rupture
                                    </span>
                                @elseif($produit->stock <= $produit->seuil_alerte_stock)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1
                                                 bg-orange-100 text-orange-700 rounded-full text-xs font-medium">
                                        <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                                        {{ $produit->stock }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1
                                                 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                        {{ $produit->stock }}
                                    </span>
                                @endif
                            </td>

                            {{-- Statut --}}
                            <td class="px-4 py-3 text-center">
                                <button wire:click="toggleStatus({{ $produit->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1
                                               rounded-full text-xs font-medium transition-all
                                               {{ $produit->est_actif
                                                  ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                  : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full
                                                 {{ $produit->est_actif ? 'bg-green-500' : 'bg-gray-400' }}">
                                    </span>
                                    {{ $produit->est_actif ? 'Actif' : 'Inactif' }}
                                </button>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3 sticky right-0 bg-white border-l border-gray-100">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('produits.edit', ['produit' => $produit->id, 'return_url' => $returnUrl]) }}"
                                       class="w-8 h-8 flex items-center justify-center rounded-lg
                                              text-gray-400 hover:text-primary hover:bg-primary/10
                                              transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2
                                                     0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828
                                                     15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button wire:click="confirmDelete({{ $produit->id }})"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg
                                                   text-gray-400 hover:text-red-500 hover:bg-red-50
                                                   transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2
                                                     2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1
                                                     1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center
                                                justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0
                                                     00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">
                                        Aucun Produit trouvé
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($produits->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $produits->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>

    {{-- Modal suppression --}}
    @if($afficherModalSuppression)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="$set('afficherModalSuppression', false)"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                                     01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                     00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-dark">Supprimer le Produit</h3>
                        <p class="text-sm text-gray-400">Cette action est irréversible</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-6">
                    Êtes-vous sûr de vouloir supprimer ce Produit du menu ?
                </p>
                <div class="flex items-center justify-end gap-3">
                    <button wire:click="$set('afficherModalSuppression', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-600
                                   hover:bg-gray-100 rounded-xl transition-colors">
                        Annuler
                    </button>
                    <button wire:click="delete"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white
                                   text-sm font-medium rounded-xl transition-colors">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>