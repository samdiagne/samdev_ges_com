<div>
    {{-- Tabs --}}
    <div class="flex items-center gap-1 mb-6 bg-white rounded-2xl border
                border-gray-100 shadow-sm p-1.5 w-fit">
        @foreach([
            'ventes'       => '📊 Ventes',
            'stocks'       => '📦 Inventaire',
            'comptabilite' => '💰 Comptabilité',
        ] as $tab => $label)
            <button wire:click="$set('activeTab', '{{ $tab }}')"
                    @class([
                        'px-4 py-2 rounded-xl text-sm font-medium transition-all',
                        'bg-primary text-white shadow-lg shadow-primary/25' => $activeTab === $tab,
                        'text-gray-500 hover:text-dark hover:bg-gray-50' => $activeTab !== $tab,
                    ])>
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ══ TAB VENTES ══ --}}
    @if($activeTab === 'ventes')

        {{-- Filtres dates --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Du</label>
                    <input wire:model.live="dateFrom" type="date"
                           class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                                  focus:outline-none focus:border-primary bg-gray-50"/>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Au</label>
                    <input wire:model.live="dateTo" type="date"
                           class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                                  focus:outline-none focus:border-primary bg-gray-50"/>
                </div>
                {{-- Raccourcis --}}
                <div class="flex gap-2 mt-4">
                    @foreach([
                        'today'  => "Aujourd'hui",
                        'week'   => 'Cette semaine',
                        'month'  => 'Ce mois',
                    ] as $key => $label)
                        <button wire:click="$set('dateFrom', '{{ match($key) {
                            'today' => now()->format('Y-m-d'),
                            'week'  => now()->startOfWeek()->format('Y-m-d'),
                            'month' => now()->startOfMonth()->format('Y-m-d'),
                        } }}')"
                                class="px-3 py-1.5 text-xs font-medium bg-gray-100
                                       hover:bg-primary hover:text-white text-gray-600
                                       rounded-lg transition-colors">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Chiffre d'affaires</p>
                <p class="text-xl font-bold text-primary mt-1">
                    {{ number_format($totalCa, 0, ',', ' ') }}
                    <span class="text-xs font-normal">FCFA</span>
                </p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Nb ventes</p>
                <p class="text-xl font-bold text-dark mt-1">{{ $nbVentes }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Panier moyen</p>
                <p class="text-xl font-bold text-dark mt-1">
                    {{ number_format($panierMoyen, 0, ',', ' ') }}
                    <span class="text-xs font-normal">FCFA</span>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

            {{-- Par mode de paiement --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-dark mb-4">Par mode de paiement</h3>
                @if($byPayment->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-4">Aucune donnée</p>
                @else
                    <div class="space-y-3">
                        @php
                            $pmLabels = ['cash'=>'Cash','wave'=>'Wave','om'=>'Orange Money', 'card'=>'Carte bancaire'];
                            $pmColors = ['cash'=>'bg-green-500','wave'=>'bg-blue-500','om'=>'bg-orange-500','card'=>'bg-purple-500' ];
                            $maxTotal = $byPayment->max('total') ?: 1;
                        @endphp
                        @foreach($byPayment as $method => $data)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-medium text-dark">
                                        {{ $pmLabels[$method] ?? $method }}
                                    </span>
                                    <span class="text-gray-500">
                                        {{ $data['count'] }} vente(s) ·
                                        {{ number_format($data['total'], 0, ',', ' ') }} FCFA
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $pmColors[$method] ?? 'bg-primary' }}"
                                         style="width: {{ round(($data['total'] / $maxTotal) * 100) }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Top produits --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-dark mb-4">Top produits vendus</h3>
                @if($topProducts->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-4">Aucune donnée</p>
                @else
                    <div class="space-y-2">
                        @foreach($topProducts as $index => $item)
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
                                    <p class="text-xs text-gray-400">
                                        {{ $item->qty }} unité(s) vendue(s)
                                    </p>
                                </div>
                                <p class="text-xs font-bold text-primary flex-shrink-0">
                                    {{ number_format($item->ca, 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Table ventes détaillée --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-dark">
                    Détail des ventes ({{ $sales->count() }})
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500
                                    uppercase tracking-wider px-4 py-3">Référence</th>
                            <th class="text-left text-xs font-semibold text-gray-500
                                    uppercase tracking-wider px-4 py-3">Client</th>
                            <th class="text-left text-xs font-semibold text-gray-500
                                    uppercase tracking-wider px-4 py-3">Date</th>
                            <th class="text-center text-xs font-semibold text-gray-500
                                    uppercase tracking-wider px-4 py-3">Paiement</th>
                            <th class="text-right text-xs font-semibold text-gray-500
                                    uppercase tracking-wider px-4 py-3">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($sales as $sale)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-2.5">
                                    <p class="text-xs font-bold text-dark">{{ $sale->reference }}</p>
                                </td>
                                <td class="px-4 py-2.5">
                                    <p class="text-xs text-dark">
                                        {{ $sale->customer?->name ?? 'Anonyme' }}
                                    </p>
                                </td>
                                <td class="px-4 py-2.5">
                                    <p class="text-xs text-dark">
                                        {{ $sale->sold_at->format('d/m/Y H:i') }}
                                    </p>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="text-xs font-medium capitalize">
                                        {{ $sale->payment_method }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <p class="text-xs font-bold text-primary">
                                        {{ number_format($sale->total_ttc, 0, ',', ' ') }} FCFA
                                    </p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">
                                    Aucune vente sur cette période
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($sales->count() > 0)
                        <tfoot>
                            <tr class="bg-primary/5 border-t-2 border-primary/20">
                                <td colspan="4" class="px-4 py-3 text-xs font-bold text-dark">
                                TOTAL ({{ $sales->count() }} ventes)
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-primary">
                                {{ number_format($totalCa, 0, ',', ' ') }} FCFA
                            </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @endif

    {{-- ══ TAB STOCKS ══ --}}
    @if($activeTab === 'stocks')

        {{-- Stats stock --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Produits</p>
                <p class="text-2xl font-bold text-dark mt-1">{{ $stockStats['total_products'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Valeur stock</p>
                <p class="text-xl font-bold text-primary mt-1">
                    {{ number_format($stockStats['total_value'], 0, ',', ' ') }}
                    <span class="text-xs font-normal">FCFA</span>
                </p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Stock bas</p>
                <p class="text-2xl font-bold text-orange-500 mt-1">
                    {{ $stockStats['low_stock'] }}
                </p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Ruptures</p>
                <p class="text-2xl font-bold text-red-500 mt-1">
                    {{ $stockStats['out_of_stock'] }}
                </p>
            </div>
        </div>

        {{-- Inventaire --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-4">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="text-sm font-bold text-dark">Inventaire complet</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500
                                       uppercase tracking-wider px-4 py-3">Produit</th>
                            <th class="text-left text-xs font-semibold text-gray-500
                                       uppercase tracking-wider px-4 py-3">Catégorie</th>
                            <th class="text-right text-xs font-semibold text-gray-500
                                       uppercase tracking-wider px-4 py-3">Prix achat</th>
                            <th class="text-right text-xs font-semibold text-gray-500
                                       uppercase tracking-wider px-4 py-3">Prix vente</th>
                            <th class="text-center text-xs font-semibold text-gray-500
                                       uppercase tracking-wider px-4 py-3">Stock</th>
                            <th class="text-right text-xs font-semibold text-gray-500
                                       uppercase tracking-wider px-4 py-3">Valeur stock</th>
                            <th class="text-center text-xs font-semibold text-gray-500
                                       uppercase tracking-wider px-4 py-3">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($products as $product)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-2.5 max-w-xs">
                                    <p class="text-xs font-semibold text-dark break-words">
                                        {{ $product->name }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $product->sku }}</p>
                                </td>
                                <td class="px-4 py-2.5">
                                    <p class="text-xs text-gray-600">
                                        {{ $product->category?->name ?? '—' }}
                                    </p>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <p class="text-xs text-gray-600">
                                        {{ number_format($product->purchase_price, 0, ',', ' ') }}
                                    </p>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <p class="text-xs font-medium text-dark">
                                        {{ number_format($product->selling_price_ttc, 0, ',', ' ') }}
                                    </p>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="text-xs font-bold
                                                 {{ $product->stock_quantity <= 0
                                                    ? 'text-red-600'
                                                    : ($product->isLowStock()
                                                       ? 'text-orange-500'
                                                       : 'text-green-600') }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <p class="text-xs font-medium text-primary">
                                        {{ number_format($product->stock_quantity * $product->purchase_price, 0, ',', ' ') }}
                                        FCFA
                                    </p>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    @if($product->stock_quantity <= 0)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs
                                                     font-medium bg-red-100 text-red-700">
                                            Rupture
                                        </span>
                                    @elseif($product->isLowStock())
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs
                                                     font-medium bg-orange-100 text-orange-700">
                                            Stock bas
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs
                                                     font-medium bg-green-100 text-green-700">
                                            OK
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-primary/5 border-t-2 border-primary/20">
                            <td colspan="5" class="px-4 py-3 text-xs font-bold text-dark">
                                VALEUR TOTALE DU STOCK
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-primary">
                                {{ number_format($stockStats['total_value'], 0, ',', ' ') }} FCFA
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Mouvements de stock --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="text-sm font-bold text-dark">
                    Mouvements de stock récents
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500
                                       uppercase tracking-wider px-4 py-3">Produit</th>
                            <th class="text-center text-xs font-semibold text-gray-500
                                       uppercase tracking-wider px-4 py-3">Type</th>
                            <th class="text-center text-xs font-semibold text-gray-500
                                       uppercase tracking-wider px-4 py-3">Source</th>
                            <th class="text-center text-xs font-semibold text-gray-500
                                       uppercase tracking-wider px-4 py-3">Avant</th>
                            <th class="text-center text-xs font-semibold text-gray-500
                                       uppercase tracking-wider px-4 py-3">Mouvement</th>
                            <th class="text-center text-xs font-semibold text-gray-500
                                       uppercase tracking-wider px-4 py-3">Après</th>
                            <th class="text-left text-xs font-semibold text-gray-500
                                       uppercase tracking-wider px-4 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($movements as $mv)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-2.5">
                                    <p class="text-xs font-medium text-dark">
                                        {{ $mv->product?->name ?? '—' }}
                                    </p>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs
                                                 font-medium
                                                 {{ $mv->type === 'in'
                                                    ? 'bg-green-100 text-green-700'
                                                    : 'bg-red-100 text-red-700' }}">
                                        {{ $mv->type === 'in' ? '↑ Entrée' : '↓ Sortie' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="text-xs text-gray-500 capitalize">
                                        {{ $mv->source }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="text-xs text-gray-500">
                                        {{ $mv->quantity_before }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="text-xs font-bold
                                                 {{ $mv->type === 'in'
                                                    ? 'text-green-600'
                                                    : 'text-red-600' }}">
                                        {{ $mv->type === 'in' ? '+' : '-' }}{{ $mv->quantity_change }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="text-xs font-bold text-dark">
                                        {{ $mv->quantity_after }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <p class="text-xs text-gray-500">
                                        {{ $mv->moved_at->format('d/m/Y H:i') }}
                                    </p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">
                                    Aucun mouvement sur cette période
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ══ TAB COMPTABILITÉ ══ --}}
    @if($activeTab === 'comptabilite')

        {{-- Sélecteur année --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
            <div class="flex items-center gap-4">
                <label class="text-sm font-medium text-dark">Année :</label>
                <select wire:model.live="filterYear"
                        class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                               focus:outline-none focus:border-primary bg-gray-50">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        {{-- KPIs annuels --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Total achats</p>
                <p class="text-xl font-bold text-dark mt-1">
                    {{ number_format($annuel['achats'], 0, ',', ' ') }}
                    <span class="text-xs font-normal">FCFA</span>
                </p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Total dépenses</p>
                <p class="text-xl font-bold text-red-500 mt-1">
                    {{ number_format($annuel['depenses'], 0, ',', ' ') }}
                    <span class="text-xs font-normal">FCFA</span>
                </p>
            </div>
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider">
                    Bénéfice brut {{ $year }}
                </p>
                <p class="text-2xl font-bold mt-1
                           {{ $annuel['benefice'] >= 0 ? 'text-green-600' : 'text-red-500' }}">
                    {{ number_format($annuel['benefice'], 0, ',', ' ') }}
                    <span class="text-sm font-normal text-gray-400">FCFA</span>
                </p>
                <p class="text-xs text-gray-400 mt-1">CA − Achats − Dépenses</p>
            </div>
        </div>

        {{-- Tableau mensuel --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="text-sm font-bold text-dark">
                    Récapitulatif mensuel {{ $year }}
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500
                                    uppercase tracking-wider px-4 py-3">Mois</th>
                            <th class="text-right text-xs font-semibold text-gray-500
                                    uppercase tracking-wider px-4 py-3">CA</th>
                            <th class="text-right text-xs font-semibold text-gray-500
                                    uppercase tracking-wider px-4 py-3">Achats</th>
                            <th class="text-right text-xs font-semibold text-gray-500
                                    uppercase tracking-wider px-4 py-3">Dépenses</th>
                            <th class="text-right text-xs font-semibold text-gray-500
                                    uppercase tracking-wider px-4 py-3">Bénéfice</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($monthly as $row)
                            <tr class="hover:bg-gray-50/50
                                       {{ $row['ca'] > 0 ? '' : 'opacity-40' }}">
                                <td class="px-4 py-2.5">
                                    <p class="text-xs font-semibold text-dark capitalize">{{ $row['mois'] }}</p>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <p class="text-xs font-bold text-primary">
                                        {{ number_format($row['ca'], 0, ',', ' ') }}
                                    </p>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <p class="text-xs text-gray-600">
                                        {{ number_format($row['achats'], 0, ',', ' ') }}
                                    </p>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <p class="text-xs text-red-500">
                                        {{ number_format($row['depenses'], 0, ',', ' ') }}
                                    </p>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <p class="text-xs font-bold
                                            {{ $row['benefice'] >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                        {{ number_format($row['benefice'], 0, ',', ' ') }}
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-primary/5 border-t-2 border-primary/20 font-bold">
                            <td class="px-4 py-3 text-xs font-bold text-dark">TOTAL {{ $year }}</td>
                            <td class="px-4 py-3 text-right text-xs font-bold text-primary">
                                {{ number_format($annuel['ca'], 0, ',', ' ') }}
                            </td>
                            <td class="px-4 py-3 text-right text-xs font-bold text-dark">
                                {{ number_format($annuel['achats'], 0, ',', ' ') }}
                            </td>
                            <td class="px-4 py-3 text-right text-xs font-bold text-red-500">
                                {{ number_format($annuel['depenses'], 0, ',', ' ') }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-bold
                                    {{ $annuel['benefice'] >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                {{ number_format($annuel['benefice'], 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</div>