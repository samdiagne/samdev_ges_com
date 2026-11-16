@php
    $user = Auth::user();
    $pendingOrdersCount  = \App\Models\Commande::where('statut', 'en_attente')->count();
    $pendingReviewsCount = \App\Models\Avis::where('est_approuve', false)->count();

    $groups = [
        [
            'label' => null,
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'dashboard',
                    'icon'  => 'home',
                    'roles' => ['admin', 'gestionnaire'],
                ],
            ],
        ],
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
                    'label' => 'Factures',
                    'route' => 'factures.index',
                    'icon'  => 'receipt',
                    'roles' => ['admin', 'gestionnaire'],
                ],
                [
                    'label' => 'Devis',
                    'route' => 'devis.index',
                    'icon'  => 'receipt',
                    'roles' => ['admin', 'gestionnaire'],
                ],
                [
                    'label' => 'Rapport Quotidien',
                    'route' => 'caisse.rapport-journalier',
                    'icon'  => 'chart-bar',
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
                [
                    'label' => 'Paliers de Prix',
                    'route' => 'paliers.index',
                    'icon'  => 'receipt',
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
                 class="px-3 pt-5 pb-2">
                <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-white/60">
                    {{ $group['label'] }}
                </p>
            </div>
            <div x-show="!sidebarOpen"
                 class="mx-3 my-2 border-t border-white/[0.06]"></div>
        @endif

        {{-- Items --}}
        @foreach($group['items'] as $item)
            @if($user->hasAnyRole($item['roles']))
                @php $isActive = request()->routeIs($item['route'] . '*'); @endphp
                <a href="{{ route($item['route']) }}"
                   @click="sidebarMobile = false"
                   @class([
                       'group flex items-center gap-3 px-3 py-2.5 mx-1 rounded-xl
                        transition-all duration-200 relative',
                       'nav-item-active bg-white/[0.08] text-white shadow-sm' => $isActive,
                       'text-white hover:bg-white/[0.1] hover:text-white' => !$isActive,
                   ])
                   @if($isActive)
                   style="box-shadow: inset 0 0 20px rgba(255,255,255,0.06);"
                   @endif>

                    {{-- Icône --}}
                    <div @class([
                        'w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                         transition-all duration-200',
                        'text-white' => $isActive,
                        'text-white group-hover:text-white' => !$isActive,
                    ])
                    @if($isActive)
                    style="background: rgba(255,255,255,0.2);"
                    @endif>
                        <div class="w-[18px] h-[18px]">
                            @include('layouts.partials.icons.' . $item['icon'])
                        </div>
                    </div>

                    {{-- Label --}}
                    <span x-show="sidebarOpen ?? true"
                          x-transition.opacity
                          @class([
                              'text-[13px] truncate transition-colors flex-1',
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
                                     text-white text-[10px] font-bold
                                     rounded-full flex items-center justify-center
                                     leading-none animate-pulse"
                              style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                            {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                        </span>
                        <span x-show="!(sidebarOpen ?? true)"
                              class="absolute top-1.5 right-1.5 w-2 h-2
                                     bg-red-500 rounded-full ring-2 ring-white/30">
                        </span>
                    @endif
                </a>
            @endif
        @endforeach
    @endif
@endforeach