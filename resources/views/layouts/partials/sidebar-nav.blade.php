@php
    $user = Auth::user();
    $pendingOrdersCount  = \App\Models\Order::where('status', 'pending')->count();
    $pendingReviewsCount = \App\Models\ProductReview::where('is_approved', false)->count();

    $groups = [
        [
            'label' => 'Caisse',
            'items' => [
                [
                    'label' => 'Point de Vente',
                    'route' => 'caisse.pos',
                    'icon'  => 'shopping-cart',
                    'roles' => ['admin', 'gestionnaire'],
                ],
                [
                    'label' => 'Ventes',
                    'route' => 'caisse.ventes.index',
                    'icon'  => 'cash',
                    'roles' => ['admin', 'gestionnaire'],
                ],
                [
                    'label' => 'Devis',
                    'route' => 'devis.index',
                    'icon'  => 'document-text',
                    'roles' => ['admin', 'gestionnaire'],
                ],
                [
                    'label' => 'Factures',
                    'route' => 'factures.index',
                    'icon'  => 'receipt',
                    'roles' => ['admin', 'gestionnaire'],
                ],
            ],
        ],
        [
            'label' => 'Gestion',
            'items' => [
                [
                    'label' => 'Achats',
                    'route' => 'caisse.achats.index',
                    'icon'  => 'truck',
                    'roles' => ['admin', 'gestionnaire'],
                ],
                [
                    'label' => 'Dépenses',
                    'route' => 'caisse.depenses.index',
                    'icon'  => 'credit-card',
                    'roles' => ['admin', 'gestionnaire'],
                ],
                [
                    'label' => 'Clients',
                    'route' => 'clients.index',
                    'icon'  => 'users',
                    'roles' => ['admin', 'gestionnaire'],
                ],
                [
                    'label' => 'Fournisseurs',
                    'route' => 'fournisseurs.index',
                    'icon'  => 'office-building',
                    'roles' => ['admin', 'gestionnaire'],
                ],
                [
                    'label' => 'Commandes',
                    'route' => 'commandes.index',
                    'icon'  => 'shopping-cart',
                    'roles' => ['admin', 'gestionnaire'],
                    'badge' => $pendingOrdersCount ?: null,
                ],
            ],
        ],
        [
            'label' => 'Catalogue',
            'items' => [
                [
                    'label' => 'Produits',
                    'route' => 'produits.index',
                    'icon'  => 'device-mobile',
                    'roles' => ['admin', 'gestionnaire'],
                ],
                [
                    'label' => 'Catégories',
                    'route' => 'categories.index',
                    'icon'  => 'tag',
                    'roles' => ['admin', 'gestionnaire'],
                ],
            ],
        ],
        [
            'label' => 'Analyse',
            'items' => [
                [
                    'label' => 'Rapports',
                    'route' => 'rapports.index',
                    'icon'  => 'chart-bar',
                    'roles' => ['admin', 'gestionnaire'],
                ],
                [
                    'label' => 'États',
                    'route' => 'etats.index',
                    'icon'  => 'document-text',
                    'roles' => ['admin', 'gestionnaire'],
                ],
            ],
        ],
        [
            'label' => 'Admin',
            'items' => [
                [
                    'label' => 'Paramètres',
                    'route' => 'parametres.index',
                    'icon'  => 'cog',
                    'roles' => ['admin'],
                ],
                [
                    'label' => 'Utilisateurs',
                    'route' => 'utilisateurs.index',
                    'icon'  => 'user-group',
                    'roles' => ['admin'],
                ],
                [
                    'label' => 'Avis clients',
                    'route' => 'avis.index',
                    'icon'  => 'star',
                    'roles' => ['admin', 'gestionnaire'],
                    'badge' => $pendingReviewsCount ?: null,
                ],
            ],
        ],
    ];
@endphp

@foreach($groups as $group)
    @php
        $hasAccess = collect($group['items'])->some(
            fn($item) => $user->hasAnyRole($item['roles'])
        );
    @endphp

    @if($hasAccess)

        {{-- Label groupe --}}
        @if($group['label'])
            <div x-show="sidebarOpen" x-transition.opacity
                 class="px-3 pt-4 pb-1">
                <p class="text-xs font-semibold uppercase tracking-widest"
                   style="color: #F5A800; opacity: 0.8;">
                    {{ $group['label'] }}
                </p>
            </div>
            {{-- Séparateur sidebar réduite --}}
            <div x-show="!sidebarOpen"
                 class="mx-4 my-1 border-t border-white/5"></div>
        @endif

        {{-- Items --}}
        @foreach($group['items'] as $item)
            @if($user->hasAnyRole($item['roles']))
                @php $isActive = request()->routeIs($item['route'] . '*'); @endphp
                <a href="{{ route($item['route']) }}"
                @click="sidebarMobile = false"
                   @class([
                       'group flex items-center gap-3 px-3 py-2 mx-1 rounded-lg
                        transition-all duration-150 relative',
                       'bg-white/15 text-white' => $isActive,
                       'text-white/85 hover:bg-white/10 hover:text-white' => !$isActive,
                   ])>

                    {{-- Indicateur actif --}}
                    @if($isActive)
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-5
                                    rounded-r-full"
                             style="background-color: #F5A800;"></div>
                    @endif

                    {{-- Icône --}}
                    <div @class([
                        'w-5 h-5 flex-shrink-0 transition-colors',
                        'text-white' => $isActive,
                        'text-white/80 group-hover:text-white' => !$isActive,
                    ])>
                        @include('layouts.partials.icons.' . $item['icon'])
                    </div>

                    {{-- Label --}}
                    <span x-show="sidebarOpen ?? true"
                          x-transition.opacity
                          @class([
                              'text-sm truncate transition-colors flex-1',
                              'font-semibold text-white' => $isActive,
                              'font-medium' => !$isActive,
                          ])>
                        {{ $item['label'] }}
                    </span>

                    {{-- Badge notification --}}
                    @if(!empty($item['badge']))
                        <span x-show="sidebarOpen ?? true"
                              x-transition.opacity
                              class="ml-auto flex-shrink-0 min-w-[20px] h-5 px-1.5
                                     bg-red-500 text-white text-xs font-bold
                                     rounded-full flex items-center justify-center
                                     leading-none">
                            {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                        </span>
                        {{-- Pastille réduite quand sidebar fermée --}}
                        <span x-show="!(sidebarOpen ?? true)"
                              class="absolute top-1 right-1 w-2.5 h-2.5
                                     bg-red-500 rounded-full border-2 border-dark">
                        </span>
                    @endif
                </a>
            @endif
        @endforeach
    @endif
@endforeach