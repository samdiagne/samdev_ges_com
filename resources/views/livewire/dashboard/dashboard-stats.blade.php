<div>
    {{-- ── Actions rapides --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('caisse.pos') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  text-white shadow-lg shadow-primary/25 hover:brightness-110
                  transition-all hover:-translate-y-0.5"
           style="background-color: #1E6FBF;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                         1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Nouvelle vente
        </a>

        <a href="{{ route('caisse.achats.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-white border border-gray-200 text-dark hover:bg-gray-50 hover:border-gray-300
                  transition-all hover:-translate-y-0.5 shadow-sm">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
            </svg>
            Nouvel achat
        </a>

        <a href="{{ route('caisse.depenses.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-white border border-gray-200 text-dark hover:bg-gray-50 hover:border-gray-300
                  transition-all hover:-translate-y-0.5 shadow-sm">
            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2
                         2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Nouvelle dépense
        </a>

        <a href="{{ route('devis.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-white border border-gray-200 text-dark hover:bg-gray-50 hover:border-gray-300
                  transition-all hover:-translate-y-0.5 shadow-sm">
            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0
                         01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Nouveau devis
        </a>

        <a href="{{ route('produits.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-white border border-gray-200 text-dark hover:bg-gray-50 hover:border-gray-300
                  transition-all hover:-translate-y-0.5 shadow-sm">
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16M4 12h16"/>
            </svg>
            Nouveau produit
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
        {{-- <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
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
        </div> --}}
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
                    $pmColors = ['cash' => '#10B981', 'wave' => '#3B82F6', 'om' => '#F97316', 'visa' => '#1a1f71'];
                    $pmLabels = ['cash' => 'Cash', 'wave' => 'Wave', 'om' => 'Orange Money', 'visa' => 'VISA',];
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

        function theme() {
            const dark = document.documentElement.classList.contains('dark');
            return {
                donut: dark
                    ? { cash: '#34d399', wave: '#60a5fa', om: '#fb923c', card: '#a78bfa', visa: '#1a1f71' }
                    : { cash: '#10b981', wave: '#3b82f6', om: '#f97316', card: '#7c3aed', visa: '#1a1f71' },
            };
        }

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
            chartInstances['salesChart'] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'CA (FCFA)',
                        data: data,
                        backgroundColor: 'rgba(30, 111, 191, 0.15)',
                        borderColor: '#1E6FBF',
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
                            callbacks: {
                                label: (ctx) => ' ' + ctx.raw.toLocaleString('fr-FR') + ' FCFA'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f0f0f0' },
                            ticks: {
                                callback: (v) => v >= 1000 ? (v/1000).toLocaleString('fr-FR') + 'k' : v
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        function initPaymentChart(data) {
            destroyChart('paymentChart');
            const ctx = document.getElementById('paymentChart');
            if (!ctx) return;
            const t = theme();
            const labelMap = { cash: '💵 Cash', wave: 'Wave', om: 'Orange Money', card: 'Carte bancaire', visa: 'VISA' };
            const labels = Object.keys(data).map(k => labelMap[k] || k);
            const values = Object.values(data);
            const colors = Object.keys(data).map(k => t.donut[k] || '#6B7280');
            chartInstances['paymentChart'] = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ' ' + ctx.raw.toLocaleString('fr-FR') + ' FCFA'
                            }
                        }
                    }
                }
            });
        }

        function initMonthlyChart(labels, data) {
            destroyChart('monthlyChart');
            const ctx = document.getElementById('monthlyChart');
            if (!ctx) return;
            chartInstances['monthlyChart'] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'CA mensuel',
                        data: data,
                        borderColor: '#1E6FBF',
                        backgroundColor: 'rgba(30, 111, 191, 0.08)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#1E6FBF',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ' ' + ctx.raw.toLocaleString('fr-FR') + ' FCFA'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f0f0f0' },
                            ticks: {
                                callback: (v) =>
                                    v >= 1000000 ? (v/1000000).toFixed(1) + 'M' :
                                    v >= 1000    ? (v/1000).toFixed(0) + 'k' : v
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        function initAllCharts(charts) {
            setTimeout(() => {
                initSalesChart(charts.daily_labels, charts.daily_sales);
                initPaymentChart(charts.payment_data);
                initMonthlyChart(charts.monthly_labels, charts.monthly_ca);
            }, 50);
        }

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