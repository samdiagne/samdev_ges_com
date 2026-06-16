<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between
                gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-dark">Catalogue</h1>
            <p class="text-sm text-gray-400 mt-1">
                {{ $plats->total() }} plat(s) trouvé(s)
                @if($search)
                    pour "<span class="text-primary font-medium">{{ $search }}</span>"
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Tri --}}
            <select wire:model.live="sort"
                    class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-primary bg-white">
                <option value="latest">Plus récents</option>
                <option value="price_asc">Prix croissant</option>
                <option value="price_desc">Prix décroissant</option>
                <option value="name">Nom A-Z</option>
            </select>

            {{-- Toggle filtres mobile --}}
            <button wire:click="$toggle('showFilters')"
                    @class([
                        'sm:hidden flex items-center gap-2 px-3 py-2 border rounded-xl text-sm',
                        'bg-primary/10 border-primary text-primary' => $showFilters,
                        'bg-white border-gray-200' => !$showFilters,
                    ])>
                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0
                             01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4
                             4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0
                             013 6.586V4z"/>
                </svg>
                Plus
            </button>
        </div>
    </div>

    {{-- ── Filtres rapides mobile (toujours visibles) ── --}}
    <div class="sm:hidden mb-4 space-y-3">
        {{-- Recherche inline --}}
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input wire:model.live.debounce.400ms="search"
                   type="text" placeholder="Rechercher un plat..."
                   class="w-full pl-9 pr-4 py-2.5 border border-gray-200
                          rounded-xl text-sm focus:outline-none
                          focus:border-primary bg-white"/>
        </div>

        {{-- Catégories horizontales scrollables --}}
        <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
            <button wire:click="$set('categorie', '')"
                    @class([
                        'flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors border',
                        'bg-primary text-white border-primary' => $categorie === '',
                        'bg-white text-gray-600 border-gray-200' => $categorie !== '',
                    ])>
                Tout
            </button>
            @foreach($categories as $cat)
                <button wire:click="$set('categorie', '{{ $cat->slug }}')"
                        @class([
                            'flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors border whitespace-nowrap',
                            'bg-primary text-white border-primary' => $categorie === $cat->slug,
                            'bg-white text-gray-600 border-gray-200' => $categorie !== $cat->slug,
                        ])>
                    {{ $cat->nom }}
                </button>
            @endforeach
        </div>

        {{-- Budget chips --}}
        <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
            @foreach(\App\Livewire\Shop\CatalogueShop::BUDGETS as $key => $range)
                <button wire:click="toggleBudget('{{ $key }}')"
                        @class([
                            'flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors border whitespace-nowrap',
                            'bg-primary text-white border-primary' => $budget === $key,
                            'bg-white text-gray-600 border-gray-200' => $budget !== $key,
                        ])>
                    {{ $range['label'] }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── Panneau filtres mobile (dépliable) ── --}}
    @if($showFilters)
        <div class="sm:hidden mb-4 bg-white rounded-2xl border border-gray-100 shadow-sm p-4 space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-dark">En stock uniquement</span>
                <button wire:click="$toggle('inStockOnly')"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200
                               {{ $inStockOnly ? 'bg-primary' : 'bg-gray-200' }}">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200
                                 {{ $inStockOnly ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
            </div>
            <button wire:click="resetFilters"
                    class="w-full py-2 text-sm font-medium text-gray-500
                           hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                Réinitialiser les filtres
            </button>
        </div>
    @endif

    <div class="flex gap-6">

        {{-- ── Sidebar filtres (desktop only) ── --}}
        <aside class="hidden sm:block w-64 flex-shrink-0">
            <div class="bg-white rounded-2xl border border-gray-100
                        shadow-sm p-5 space-y-6 sticky top-20">

                {{-- Recherche --}}
                <div>
                    <label class="block text-xs font-bold text-dark uppercase
                                  tracking-wider mb-2">Recherche</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2
                                    w-4 h-4 text-gray-400"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                        <input wire:model.live.debounce.400ms="search"
                               type="text" placeholder="Nom, marque..."
                               class="w-full pl-9 pr-4 py-2.5 border border-gray-200
                                      rounded-xl text-sm focus:outline-none
                                      focus:border-primary bg-gray-50"/>
                    </div>
                </div>

                {{-- Catégories --}}
                <div>
                    <label class="block text-xs font-bold text-dark uppercase
                                  tracking-wider mb-3">Catégories</label>
                    <div class="space-y-1">
                        <button wire:click="$set('categorie', '')"
                                @class([
                                    'w-full flex items-center justify-between px-3 py-2
                                     rounded-xl text-sm transition-colors text-left',
                                    'bg-primary text-white font-semibold' => $categorie === '',
                                    'text-gray-600 hover:bg-gray-50' => $categorie !== '',
                                ])>
                            <span>Toutes</span>
                            <span class="{{ $categorie === '' ? 'bg-white/20' : 'bg-gray-100' }}
                                        text-xs px-2 py-0.5 rounded-full">
                                {{ $categories->sum('plats_actifs_count') }}
                            </span>
                        </button>
                        @foreach($categories as $cat)
                            <button wire:click="$set('categorie', '{{ $cat->slug }}')"
                                    @class([
                                        'w-full flex items-center justify-between px-3 py-2
                                         rounded-xl text-sm transition-colors text-left',
                                        'bg-primary text-white font-semibold'
                                            => $categorie === $cat->slug,
                                        'text-gray-600 hover:bg-gray-50'
                                            => $categorie !== $cat->slug,
                                    ])>
                                <span>{{ $cat->nom }}</span>
                                <span @class([
                                    'text-xs px-2 py-0.5 rounded-full',
                                    'bg-white/20' => $categorie === $cat->slug,
                                    'bg-gray-100' => $categorie !== $cat->slug,
                                ])>
                                    {{ $cat->plats_actifs_count }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Budget --}}
                <div>
                    <label class="block text-xs font-bold text-dark uppercase
                                  tracking-wider mb-3">Budget</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(\App\Livewire\Shop\CatalogueShop::BUDGETS as $key => $range)
                            <button wire:click="toggleBudget('{{ $key }}')"
                                    @class([
                                        'px-3 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 border',
                                        'bg-primary text-white border-primary shadow-sm' => $budget === $key,
                                        'bg-white text-gray-600 border-gray-200 hover:border-primary hover:text-primary' => $budget !== $key,
                                    ])>
                                {{ $range['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Stock --}}
                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium text-dark">
                        En stock uniquement
                    </label>
                    <button wire:click="$toggle('inStockOnly')"
                            class="relative inline-flex h-6 w-11 items-center
                                   rounded-full transition-colors duration-200
                                   {{ $inStockOnly ? 'bg-primary' : 'bg-gray-200' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full
                                     bg-white shadow transition-transform duration-200
                                     {{ $inStockOnly ? 'translate-x-6' : 'translate-x-1' }}">
                        </span>
                    </button>
                </div>

                {{-- Reset --}}
                <button wire:click="resetFilters"
                        class="w-full py-2 text-sm font-medium text-gray-500
                               hover:text-red-500 hover:bg-red-50 rounded-xl
                               transition-colors">
                    Réinitialiser les filtres
                </button>
            </div>
        </aside>

        {{-- ── Grille plats ── --}}
        <div class="flex-1 min-w-0">

            {{-- Loading --}}
            <div wire:loading
                 class="flex items-center justify-center py-12">
                <div class="flex items-center gap-3 text-gray-400">
                    <svg class="animate-spin w-5 h-5" fill="none"
                         viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Chargement...
                </div>
            </div>

            <div wire:loading.remove>
                @if($plats->count() > 0)
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($plats as $plat)
                            @include('shop.partials.plat-card',
                                compact('plat'))
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($plats->hasPages())
                        <div class="mt-8 flex justify-center">
                            {{ $plats->links() }}
                        </div>
                    @endif
                @else
                    <div class="flex flex-col items-center justify-center
                                py-20 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-3xl flex
                                    items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-300" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                            </svg>
                        </div>
                        <p class="text-lg font-semibold text-dark mb-2">
                            Aucun plat trouvé
                        </p>
                        <p class="text-sm text-gray-400 mb-6">
                            Essayez de modifier vos filtres
                        </p>
                        <button wire:click="resetFilters"
                                class="px-6 py-2.5 bg-primary text-white text-sm
                                       font-medium rounded-xl hover:bg-primary/90
                                       transition-colors">
                            Voir tout le menu
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>