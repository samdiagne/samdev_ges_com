<div>
    {{-- Filtre dates --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Période du rapport
        </h3>
        <div class="flex flex-col sm:flex-row gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Du</label>
                <input wire:model="dateFrom" type="date"
                       class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                              bg-gray-50 focus:outline-none focus:border-primary focus:bg-white transition-colors"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Au</label>
                <input wire:model="dateTo" type="date"
                       class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                              bg-gray-50 focus:outline-none focus:border-primary focus:bg-white transition-colors"/>
            </div>
        </div>
    </div>

    {{-- Rapports disponibles --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- Ventes --}}
        <a href="{{ route('etats.ventes', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-lg
                  hover:border-primary/30 transition-all group">
            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4
                        group-hover:bg-primary/20 transition-colors">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0
                             012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2
                             0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <h4 class="text-sm font-bold text-dark mb-1">État des ventes</h4>
            <p class="text-xs text-gray-400">Détail des ventes, modes de paiement, panier moyen</p>
            <span class="inline-flex items-center gap-1 mt-3 text-xs font-medium text-primary">
                Télécharger PDF
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </span>
        </a>

        {{-- Dépenses --}}
        <a href="{{ route('etats.depenses', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-lg
                  hover:border-red-200 transition-all group">
            <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center mb-4
                        group-hover:bg-red-100 transition-colors">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0
                             002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h4 class="text-sm font-bold text-dark mb-1">État des dépenses</h4>
            <p class="text-xs text-gray-400">Dépenses par catégorie et par période</p>
            <span class="inline-flex items-center gap-1 mt-3 text-xs font-medium text-red-500">
                Télécharger PDF
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </span>
        </a>

        {{-- Trésorerie --}}
        <a href="{{ route('etats.tresorerie', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-lg
                  hover:border-green-200 transition-all group">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center mb-4
                        group-hover:bg-green-100 transition-colors">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11
                             0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21
                             12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h4 class="text-sm font-bold text-dark mb-1">État de trésorerie</h4>
            <p class="text-xs text-gray-400">Recettes, achats, dépenses et solde journalier</p>
            <span class="inline-flex items-center gap-1 mt-3 text-xs font-medium text-green-600">
                Télécharger PDF
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </span>
        </a>

        {{-- Stocks --}}
        <a href="{{ route('etats.stocks') }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-lg
                  hover:border-orange-200 transition-all group">
            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center mb-4
                        group-hover:bg-orange-100 transition-colors">
                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <h4 class="text-sm font-bold text-dark mb-1">Bilan des stocks</h4>
            <p class="text-xs text-gray-400">Inventaire complet, valorisation et alertes</p>
            <span class="inline-flex items-center gap-1 mt-3 text-xs font-medium text-orange-500">
                Télécharger PDF
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </span>
        </a>
    </div>
</div>
