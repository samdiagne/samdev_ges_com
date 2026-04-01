<div>
    {{-- Sélecteur période --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <h3 class="text-sm font-bold text-dark mb-4">Sélectionner la période</h3>

        {{-- Raccourcis --}}
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach([
                'today'      => "Aujourd'hui",
                'week'       => 'Cette semaine',
                'month'      => 'Ce mois',
                'last_month' => 'Mois dernier',
                'year'       => 'Cette année',
            ] as $key => $label)
                <button wire:click="setQuickPeriod('{{ $key }}')"
                        class="px-3 py-1.5 text-xs font-medium bg-gray-100
                               hover:bg-primary hover:text-white text-gray-600
                               rounded-lg transition-colors">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Dates personnalisées --}}
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs text-gray-400 mb-1">Du</label>
                <input wire:model="dateFrom" type="date"
                       class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                              focus:outline-none focus:border-primary bg-gray-50"/>
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Au</label>
                <input wire:model="dateTo" type="date"
                       class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                              focus:outline-none focus:border-primary bg-gray-50"/>
            </div>
            <p class="text-xs text-gray-400 pb-2">
                Période sélectionnée :
                <strong class="text-dark">
                    {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
                    →
                    {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
                </strong>
            </p>
        </div>
    </div>

    {{-- États disponibles --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- État des ventes --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6
                    hover:shadow-md transition-shadow">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center
                            justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2
                                 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0
                                 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2
                                 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-dark">État des ventes</h3>
                    <p class="text-xs text-gray-400 mt-1 mb-4">
                        Liste détaillée de toutes les ventes, répartition par mode
                        de paiement et top produits.
                    </p>
                    <a href="{{ route('etats.ventes', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90
                              text-white text-xs font-medium px-4 py-2 rounded-xl
                              transition-all shadow-lg shadow-primary/25">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2
                                     0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0
                                     01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Télécharger PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- État des dépenses --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6
                    hover:shadow-md transition-shadow">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center
                            justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2
                                 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0
                                 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-dark">État des dépenses</h3>
                    <p class="text-xs text-gray-400 mt-1 mb-4">
                        Détail de toutes les dépenses avec répartition
                        par catégorie.
                    </p>
                    <a href="{{ route('etats.depenses', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600
                              text-white text-xs font-medium px-4 py-2 rounded-xl
                              transition-all shadow-lg shadow-red-500/25">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2
                                     0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0
                                     01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Télécharger PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- État de trésorerie --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6
                    hover:shadow-md transition-shadow">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center
                            justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343
                                 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1
                                 c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-dark">État de trésorerie</h3>
                    <p class="text-xs text-gray-400 mt-1 mb-4">
                        Flux de trésorerie journaliers — recettes, achats,
                        dépenses et solde net.
                    </p>
                    <a href="{{ route('etats.tresorerie', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700
                              text-white text-xs font-medium px-4 py-2 rounded-xl
                              transition-all shadow-lg shadow-green-600/25">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2
                                     0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0
                                     01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Télécharger PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Bilan stocks --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6
                    hover:shadow-md transition-shadow">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center
                            justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4
                                 7v10l8 4"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-dark">Bilan des stocks</h3>
                    <p class="text-xs text-gray-400 mt-1 mb-4">
                        Inventaire complet avec valeurs d'achat/vente,
                        marges potentielles et statuts de stock.
                    </p>
                    <a href="{{ route('etats.stocks') }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700
                              text-white text-xs font-medium px-4 py-2 rounded-xl
                              transition-all shadow-lg shadow-purple-600/25">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2
                                     0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0
                                     01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Télécharger PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>