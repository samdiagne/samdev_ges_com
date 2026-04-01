<div>
    {{-- ── Raccourcis rapides --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <a href="{{ route('caisse.pos') }}"
           class="flex items-center gap-3 bg-primary hover:bg-primary/90
                  text-white rounded-2xl p-4 transition-all shadow-lg
                  shadow-primary/20 hover:-translate-y-0.5">
            <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-white/70 leading-tight">Caisse</p>
                <p class="text-sm font-bold leading-tight truncate">Nouvelle vente</p>
            </div>
        </a>

        <a href="{{ route('devis.create') }}"
           class="flex items-center gap-3 bg-white hover:bg-gray-50 border
                  border-gray-100 rounded-2xl p-4 transition-all shadow-sm
                  hover:-translate-y-0.5">
            <div class="w-9 h-9 bg-secondary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0
                             01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-gray-400 leading-tight">Créer</p>
                <p class="text-sm font-bold text-dark leading-tight truncate">Nouveau devis</p>
            </div>
        </a>

        <a href="{{ route('factures.index') }}"
           class="flex items-center gap-3 bg-white hover:bg-gray-50 border
                  border-gray-100 rounded-2xl p-4 transition-all shadow-sm
                  hover:-translate-y-0.5">
            <div class="w-9 h-9 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0
                             00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2
                             2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-gray-400 leading-tight">Consulter</p>
                <p class="text-sm font-bold text-dark leading-tight truncate">Factures</p>
            </div>
        </a>

        <a href="{{ route('commandes.index') }}"
           class="flex items-center gap-3 bg-white hover:bg-gray-50 border
                  border-gray-100 rounded-2xl p-4 transition-all shadow-sm
                  hover:-translate-y-0.5 relative">
            <div class="w-9 h-9 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                             1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-gray-400 leading-tight">Gérer</p>
                <p class="text-sm font-bold text-dark leading-tight truncate">Commandes</p>
            </div>
            @if($recent['pending_orders']->count() > 0)
                <span class="absolute top-2.5 right-2.5 min-w-[18px] h-[18px] px-1
                             bg-red-500 text-white text-[10px] font-bold rounded-full
                             flex items-center justify-center leading-none">
                    {{ $recent['pending_orders']->count() > 99 ? '99+' : $recent['pending_orders']->count() }}
                </span>
            @endif
        </a>
    </div>

    {{-- Sélecteur période --}}
    <div class="flex items-center gap-2 mb-6 bg-white rounded-2xl border
                border-gray-100 shadow-sm p-1.5 w-fit">
        @foreach([
            'today' => "Aujourd'hui",
            'week'  => 'Cette semaine',
            'month' => 'Ce mois',
            'year'  => 'Cette année',
        ] as $value => $label)
            <button wire:click="$set('period', '{{ $value }}')"
                    @class([
                        'px-4 py-2 rounded-xl text-sm font-medium transition-all',
                        'bg-primary text-white shadow-lg shadow-primary/25' => $period === $value,
                        'text-gray-500 hover:text-dark hover:bg-gray-50' => $period !== $value,
                    ])>
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ── KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">

        {{-- CA TTC --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11
                                 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21
                                 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                @if($stats['ca_evolution'] != 0)
                    <span @class([
                        'text-xs font-bold px-2 py-1 rounded-full',
                        'bg-green-100 text-green-700' => $stats['ca_evolution'] > 0,
                        'bg-red-100 text-red-700'     => $stats['ca_evolution'] < 0,
                    ])>
                        {{ $stats['ca_evolution'] > 0 ? '+' : '' }}{{ $stats['ca_evolution'] }}%
                    </span>
                @endif
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Chiffre d'affaires</p>
            <p class="text-2xl font-bold text-dark mt-1">
                {{ number_format($stats['ca'], 0, ',', ' ') }}
                <span class="text-sm font-normal text-gray-400">FCFA</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">{{ $stats['sales_count'] }} vente(s)</p>
        </div>

        {{-- Bénéfice --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Bénéfice brut</p>
            <p class="text-2xl font-bold mt-1 {{ $stats['benefice'] >= 0 ? 'text-green-600' : 'text-red-500' }}">
                {{ number_format($stats['benefice'], 0, ',', ' ') }}
                <span class="text-sm font-normal text-gray-400">FCFA</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">CA - Achats - Dépenses</p>
        </div>

        {{-- Dépenses --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9
                             5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Dépenses</p>
            <p class="text-2xl font-bold text-red-500 mt-1">
                {{ number_format($stats['depenses_total'], 0, ',', ' ') }}
                <span class="text-sm font-normal text-gray-400">FCFA</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">charges de la période</p>
        </div>

        {{-- Panier moyen --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-10 h-10 bg-secondary/10 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                             1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Panier moyen</p>
            <p class="text-2xl font-bold text-secondary mt-1">
                {{ number_format($stats['avg_basket'], 0, ',', ' ') }}
                <span class="text-sm font-normal text-gray-400">FCFA</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">par transaction</p>
        </div>
    </div>

    {{-- ── Graphiques --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Ventes 7 jours --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-dark">Ventes — 7 derniers jours</h3>
                    <p class="text-xs text-gray-400">Chiffre d'affaires journalier</p>
                </div>
            </div>
            <canvas id="salesChart" height="120"></canvas>
        </div>

        {{-- Répartition paiements --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-bold text-dark mb-4">Modes de paiement</h3>
            <canvas id="paymentChart" height="160"></canvas>
            <div class="space-y-2 mt-4">
                @php
                    $pmColors = ['cash' => '#10B981', 'wave' => '#3B82F6', 'om' => '#F97316', 'virement' => '#5a16f9'];
                    $pmLabels = ['cash' => 'Cash', 'wave' => 'Wave', 'om' => 'Orange Money', 'virement' => 'Virement bancaire',];
                    $totalPm  = array_sum($charts['payment_data']);
                @endphp
                @foreach($charts['payment_data'] as $method => $amount)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full"
                                 style="background: {{ $pmColors[$method] ?? '#6B7280' }}"></div>
                            <span class="text-gray-600">{{ $pmLabels[$method] ?? $method }}</span>
                        </div>
                        <span class="font-bold text-dark">
                            {{ $totalPm > 0 ? round(($amount / $totalPm) * 100) : 0 }}%
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── CA Mensuel --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-dark">CA Mensuel — 12 derniers mois</h3>
                <p class="text-xs text-gray-400">Évolution du chiffre d'affaires</p>
            </div>
        </div>
        <canvas id="monthlyChart" height="80"></canvas>
    </div>

    {{-- ── Commandes en attente --}}
    @if($recent['pending_orders']->count() > 0)
    <div class="bg-white rounded-2xl border border-orange-100 shadow-sm p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-bold text-dark">Commandes en attente</h3>
                <span class="min-w-[22px] h-5 px-1.5 bg-red-500 text-white text-xs font-bold
                             rounded-full flex items-center justify-center leading-none">
                    {{ $recent['pending_orders']->count() }}
                </span>
            </div>
            <a href="{{ route('commandes.index') }}"
               class="text-xs text-primary hover:underline font-medium">
                Tout gérer →
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($recent['pending_orders'] as $order)
                <div class="flex items-start gap-3 p-3 rounded-xl border transition-colors
                            {{ $order->status === 'pending' ? 'border-orange-200 bg-orange-50' : 'border-blue-100 bg-blue-50' }}">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0
                                {{ $order->status === 'pending' ? 'bg-orange-200' : 'bg-blue-200' }}">
                        <svg class="w-4 h-4 {{ $order->status === 'pending' ? 'text-orange-700' : 'text-blue-700' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-1">
                            <p class="text-xs font-bold text-dark truncate">{{ $order->reference }}</p>
                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full flex-shrink-0
                                         {{ $order->status === 'pending' ? 'bg-orange-200 text-orange-800' : 'bg-blue-200 text-blue-800' }}">
                                {{ $order->status === 'pending' ? 'En attente' : 'Confirmée' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 mt-0.5 truncate">{{ $order->customer_name }}</p>
                        <p class="text-xs font-semibold text-dark mt-0.5">
                            {{ number_format($order->total, 0, ',', ' ') }} FCFA
                        </p>
                        <p class="text-[10px] text-gray-400 mt-0.5">
                            {{ $order->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Ligne du bas --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Alertes stock --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-dark">Alertes stock</h3>
                @if($stats['stock_low'] > 0 || $stats['stock_out'] > 0)
                    <span class="w-5 h-5 bg-red-500 rounded-full text-white text-xs
                                 flex items-center justify-center font-bold">
                        {{ $stats['stock_low'] + $stats['stock_out'] }}
                    </span>
                @endif
            </div>

            @if($alerts['out_stock']->count() > 0)
                <p class="text-xs font-semibold text-red-500 uppercase tracking-wider mb-2">
                    🔴 Rupture
                </p>
                <div class="space-y-2 mb-3">
                    @foreach($alerts['out_stock'] as $product)
                        <div class="flex items-center justify-between p-2 bg-red-50 rounded-xl">
                            <p class="text-xs font-medium text-dark truncate">{{ $product->name }}</p>
                            <span class="text-xs font-bold text-red-600 ml-2">0</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($alerts['low_stock']->count() > 0)
                <p class="text-xs font-semibold text-orange-500 uppercase tracking-wider mb-2">
                    🟠 Stock bas
                </p>
                <div class="space-y-2">
                    @foreach($alerts['low_stock'] as $product)
                        <div class="flex items-center justify-between p-2 bg-orange-50 rounded-xl">
                            <p class="text-xs font-medium text-dark truncate">{{ $product->name }}</p>
                            <span class="text-xs font-bold text-orange-600 ml-2">
                                {{ $product->stock_quantity }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($alerts['out_stock']->count() === 0 && $alerts['low_stock']->count() === 0)
                <div class="text-center py-6">
                    <p class="text-2xl mb-2">✅</p>
                    <p class="text-sm text-gray-400">Stock OK</p>
                </div>
            @endif
        </div>

        {{-- Dernières ventes --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-dark">Dernières ventes</h3>
                <a href="{{ route('caisse.ventes.index') }}"
                   class="text-xs text-primary hover:underline">Voir tout</a>
            </div>
            <div class="space-y-3">
                @forelse($recent['sales'] as $sale)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-8 h-8 bg-primary/10 rounded-xl flex items-center
                                        justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-primary" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2
                                             4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2
                                             0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-dark truncate">
                                    {{ $sale->customer?->name ?? 'Anonyme' }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $sale->sold_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <p class="text-xs font-bold text-primary flex-shrink-0 ml-2">
                            {{ number_format($sale->total_ttc, 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-4">Aucune vente</p>
                @endforelse
            </div>
        </div>

        {{-- Top produits --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-dark">Top produits</h3>
                <span class="text-xs text-gray-400">ce mois</span>
            </div>
            <div class="space-y-3">
                @forelse($recent['top_products'] as $index => $item)
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-lg flex items-center justify-center
                                     text-xs font-bold flex-shrink-0
                                     {{ $index === 0 ? 'bg-secondary text-white' :
                                        ($index === 1 ? 'bg-gray-200 text-gray-600' :
                                        'bg-gray-100 text-gray-500') }}">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-dark truncate">
                                {{ $item->product_name }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $item->total_qty }} vendu(s)</p>
                        </div>
                        <p class="text-xs font-bold text-dark flex-shrink-0">
                            {{ number_format($item->total_ca, 0, ',', ' ') }}
                            <span class="font-normal text-gray-400">FCFA</span>
                        </p>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-4">Aucune donnée</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
        const chartInstances = {};
        let _lastCharts = null;

        function isDark() {
            return document.documentElement.classList.contains('dark');
        }

        function theme() {
            const dark = isDark();
            return {
                grid:        dark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)',
                tick:        dark ? '#64748b' : '#9ca3af',
                tooltipBg:   dark ? '#1e293b' : '#ffffff',
                tooltipText: dark ? '#f1f5f9' : '#111827',
                tooltipBorder: dark ? '#334155' : '#e5e7eb',

                barBg:     dark ? 'rgba(96, 165, 250, 0.20)' : 'rgba(30, 111, 191, 0.12)',
                barBorder: dark ? '#60a5fa' : '#1E6FBF',
                barHover:  dark ? 'rgba(96, 165, 250, 0.35)' : 'rgba(30, 111, 191, 0.25)',

                lineBorder: dark ? '#818cf8' : '#1E6FBF',
                lineFill:   dark ? 'rgba(129, 140, 248, 0.12)' : 'rgba(30, 111, 191, 0.07)',
                linePoint:  dark ? '#818cf8' : '#1E6FBF',
                linePointBorder: dark ? '#1e293b' : '#ffffff',

                donut: dark
                    ? { cash: '#34d399', wave: '#60a5fa', om: '#fb923c', card: '#a78bfa', virement: '#a78bfa' }
                    : { cash: '#10b981', wave: '#3b82f6', om: '#f97316', card: '#7c3aed', virement: '#7c3aed' },

                donutBorder: dark ? '#0f172a' : '#ffffff',
            };
        }

        const tooltipPlugin = (t) => ({
            backgroundColor: t.tooltipBg,
            titleColor: t.tooltipText,
            bodyColor: t.tooltipText,
            borderColor: t.tooltipBorder,
            borderWidth: 1,
            padding: 10,
            cornerRadius: 10,
        });

        function destroyChart(id) {
            if (chartInstances[id]) {
                chartInstances[id].destroy();
                delete chartInstances[id];
            }
        }

        function initSalesChart(labels, data) {
            destroyChart('salesChart');
            const ctx = document.getElementById('salesChart');
            if (!ctx) return;
            const t = theme();
            chartInstances['salesChart'] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'CA (FCFA)',
                        data,
                        backgroundColor: t.barBg,
                        hoverBackgroundColor: t.barHover,
                        borderColor: t.barBorder,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            ...tooltipPlugin(t),
                            callbacks: { label: (c) => '  ' + c.raw.toLocaleString('fr-FR') + ' FCFA' }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: t.grid },
                            border: { dash: [4, 4], display: false },
                            ticks: {
                                color: t.tick,
                                callback: (v) => v >= 1000 ? (v/1000).toLocaleString('fr-FR') + 'k' : v
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: t.tick }
                        }
                    }
                }
            });
        }

        function initPaymentChart(data) {
            destroyChart('paymentChart');
            const ctx = document.getElementById('paymentChart');
            if (!ctx) return;
            const t = theme();
            const labelMap = { cash: '💵 Cash', wave: 'Wave', om: 'Orange Money', card: 'Carte', virement: 'Virement' };
            const labels = Object.keys(data).map(k => labelMap[k] || k);
            const values = Object.values(data);
            const colors = Object.keys(data).map(k => t.donut[k] || '#6b7280');
            chartInstances['paymentChart'] = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderColor: t.donutBorder,
                        borderWidth: 3,
                        hoverOffset: 6,
                        hoverBorderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            ...tooltipPlugin(t),
                            callbacks: { label: (c) => '  ' + c.raw.toLocaleString('fr-FR') + ' FCFA' }
                        }
                    }
                }
            });
        }

        function initMonthlyChart(labels, data) {
            destroyChart('monthlyChart');
            const ctx = document.getElementById('monthlyChart');
            if (!ctx) return;
            const t = theme();
            chartInstances['monthlyChart'] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'CA mensuel',
                        data,
                        borderColor: t.lineBorder,
                        backgroundColor: t.lineFill,
                        borderWidth: 2.5,
                        pointBackgroundColor: t.linePoint,
                        pointBorderColor: t.linePointBorder,
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: t.lineBorder,
                        fill: true,
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            ...tooltipPlugin(t),
                            callbacks: {
                                label: (c) => '  ' + c.raw.toLocaleString('fr-FR') + ' FCFA'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: t.grid },
                            border: { dash: [4, 4], display: false },
                            ticks: {
                                color: t.tick,
                                callback: (v) =>
                                    v >= 1000000 ? (v/1000000).toFixed(1) + 'M' :
                                    v >= 1000    ? (v/1000).toFixed(0) + 'k' : v
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: t.tick }
                        }
                    }
                }
            });
        }

        function initAllCharts(charts) {
            _lastCharts = charts;
            setTimeout(() => {
                initSalesChart(charts.daily_labels, charts.daily_sales);
                initPaymentChart(charts.payment_data);
                initMonthlyChart(charts.monthly_labels, charts.monthly_ca);
            }, 50);
        }

        // Re-render charts when dark mode is toggled
        new MutationObserver(() => {
            if (_lastCharts) initAllCharts(_lastCharts);
        }).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

        document.addEventListener('DOMContentLoaded', () => {
            initAllCharts(@json($charts));
        });

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('chartsUpdated', ({ charts }) => {
                initAllCharts(charts);
            });
        });
    </script>
</div>