<div class="space-y-5 pb-10">

    {{-- En-tête de la page avec sélecteurs --}}
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                Point de Vente & Caisse
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">
                Rapport Quotidien de Caisse
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                Statistiques des sessions de caisse, chiffre d'affaires par mode de règlement, fonds et écarts.
            </p>
        </div>

        {{-- Filtres Date & Caissier + Bouton Export --}}
        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Boutons d'accès rapide --}}
            <div class="inline-flex rounded-xl p-1 bg-gray-100 border border-gray-200 text-xs">
                <button wire:click="setDateAujourdhui"
                        class="px-3 py-1.5 rounded-lg font-medium transition-all {{ $date === now()->toDateString() ? 'bg-white text-gray-900 shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                    Aujourd'hui
                </button>
                <button wire:click="setDateHier"
                        class="px-3 py-1.5 rounded-lg font-medium transition-all {{ $date === now()->subDay()->toDateString() ? 'bg-white text-gray-900 shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                    Hier
                </button>
            </div>

            {{-- Datepicker --}}
            <input type="date" wire:model.live="date"
                   class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-primary focus:border-primary">

            {{-- Sélecteur de caissier (admin uniquement) --}}
            @if($isAdmin && $caissiers->isNotEmpty())
                <select wire:model.live="selectedUserId"
                        class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-primary focus:border-primary">
                    <option value="">Tous les caissiers</option>
                    @foreach($caissiers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            @endif

            {{-- Bouton Télécharger PDF --}}
            <a href="{{ route('caisse.rapport-journalier.pdf', ['date' => $date, 'user_id' => $selectedUserId]) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-xl text-xs sm:text-sm font-semibold shadow-sm transition-all shadow-primary/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Télécharger PDF</span>
            </a>
        </div>
    </div>

    {{-- 4 Cartes Métriques Clés --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        {{-- Total CA --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">CA Total Journalier</span>
                <span class="p-2 bg-primary/10 text-primary rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <div class="mt-2 text-xl sm:text-2xl font-black text-gray-900">
                {{ number_format($stats['total_ca'], 0, ',', ' ') }} <span class="text-xs font-bold text-gray-400">FCFA</span>
            </div>
            <div class="text-xs text-gray-500 mt-1">
                {{ $stats['nb_ventes'] }} vente{{ $stats['nb_ventes'] > 1 ? 's' : '' }} au total
            </div>
        </div>

        {{-- Fonds de caisse --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Fonds de Caisse</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </span>
            </div>
            <div class="mt-2 text-xl sm:text-2xl font-black text-blue-600">
                {{ number_format($stats['fond_total'], 0, ',', ' ') }} <span class="text-xs font-bold text-gray-400">FCFA</span>
            </div>
            <div class="text-xs text-gray-500 mt-1">
                Total des montants au démarrage
            </div>
        </div>

        {{-- Sorties / Dépenses & Achats --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sorties Caisse</span>
                <span class="p-2 bg-red-50 text-red-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </span>
            </div>
            <div class="mt-2 text-xl sm:text-2xl font-black text-red-600">
                {{ number_format($stats['total_sorties'], 0, ',', ' ') }} <span class="text-xs font-bold text-gray-400">FCFA</span>
            </div>
            <div class="text-xs text-gray-500 mt-1 flex flex-wrap items-center justify-between gap-1 pt-1 border-t border-gray-100">
                <span>Dépenses : <strong>{{ number_format($stats['total_depenses'], 0, ',', ' ') }} F</strong></span>
                <span>Achats : <strong class="text-orange-600">{{ number_format($stats['total_achats'], 0, ',', ' ') }} F</strong></span>
            </div>
        </div>

        {{-- Écart Net --}}
        @php $ecart = $stats['ecart_net']; @endphp
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Écart de Caisse Net</span>
                <span class="p-2 rounded-xl {{ $ecart < 0 ? 'bg-red-50 text-red-600' : ($ecart > 0 ? 'bg-blue-50 text-blue-600' : 'bg-green-50 text-green-600') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <div class="mt-2 text-xl sm:text-2xl font-black {{ $ecart < 0 ? 'text-red-600' : ($ecart > 0 ? 'text-blue-600' : 'text-green-600') }}">
                {{ $ecart > 0 ? '+' : '' }}{{ number_format($ecart, 0, ',', ' ') }} <span class="text-xs font-bold text-gray-400">FCFA</span>
            </div>
            <div class="text-xs font-medium mt-1 {{ $ecart < 0 ? 'text-red-600' : ($ecart > 0 ? 'text-blue-600' : 'text-green-600') }}">
                {{ $ecart === 0.0 ? '✓ Caisse équilibrée' : ($ecart > 0 ? '▲ Excédent net' : '▼ Manquant net') }}
            </div>
        </div>
    </div>

    {{-- Répartition par Mode de Paiement --}}
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-sm">
        <h2 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <span class="w-1.5 h-4 bg-primary rounded-full"></span>
            Répartition du Chiffre d'Affaires par Mode de Règlement
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            {{-- Espèces --}}
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                <div class="flex items-center justify-between text-xs text-gray-500 font-semibold mb-1">
                    <span>💵 Espèces (Cash)</span>
                    <span>{{ $stats['total_ca'] > 0 ? round(($stats['ca_cash'] / $stats['total_ca']) * 100) : 0 }}%</span>
                </div>
                <div class="text-base sm:text-lg font-bold text-gray-900">
                    {{ number_format($stats['ca_cash'], 0, ',', ' ') }} <span class="text-xs text-gray-400 font-normal">FCFA</span>
                </div>
            </div>

            {{-- Wave --}}
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                <div class="flex items-center justify-between text-xs text-gray-500 font-semibold mb-1">
                    <span>Wave</span>
                    <span>{{ $stats['total_ca'] > 0 ? round(($stats['ca_wave'] / $stats['total_ca']) * 100) : 0 }}%</span>
                </div>
                <div class="text-base sm:text-lg font-bold text-sky-600">
                    {{ number_format($stats['ca_wave'], 0, ',', ' ') }} <span class="text-xs text-gray-400 font-normal">FCFA</span>
                </div>
            </div>

            {{-- Orange Money --}}
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                <div class="flex items-center justify-between text-xs text-gray-500 font-semibold mb-1">
                    <span>Orange Money</span>
                    <span>{{ $stats['total_ca'] > 0 ? round(($stats['ca_om'] / $stats['total_ca']) * 100) : 0 }}%</span>
                </div>
                <div class="text-base sm:text-lg font-bold text-orange-600">
                    {{ number_format($stats['ca_om'], 0, ',', ' ') }} <span class="text-xs text-gray-400 font-normal">FCFA</span>
                </div>
            </div>

            {{-- Carte Bancaire --}}
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                <div class="flex items-center justify-between text-xs text-gray-500 font-semibold mb-1">
                    <span>💳 Carte Bancaire</span>
                    <span>{{ $stats['total_ca'] > 0 ? round(($stats['ca_carte'] / $stats['total_ca']) * 100) : 0 }}%</span>
                </div>
                <div class="text-base sm:text-lg font-bold text-purple-600">
                    {{ number_format($stats['ca_carte'], 0, ',', ' ') }} <span class="text-xs text-gray-400 font-normal">FCFA</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Liste des Sessions de Caisse de la Journée --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-primary rounded-full"></span>
                    Sessions de Caisse ({{ $sessions->count() }})
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Détail des ouvertures, fermetures, encaissements et tickets Z</p>
            </div>
        </div>

        @if($sessions->isEmpty())
            <div class="p-8 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="font-medium text-gray-600">Aucune session de caisse pour cette sélection.</p>
                <p class="text-xs text-gray-400 mt-1">Sélectionnez une autre date ou ouvrez une session depuis le point de vente.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-semibold uppercase text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="py-3 px-4">Session & Caissier</th>
                            <th class="py-3 px-3">Ouverture</th>
                            <th class="py-3 px-3">Fermeture</th>
                            <th class="py-3 px-3">Durée</th>
                            <th class="py-3 px-3 text-right">Fond Init.</th>
                            <th class="py-3 px-3 text-right">CA Session</th>
                            <th class="py-3 px-3 text-right">Sorties (Dép. & Achats)</th>
                            <th class="py-3 px-3 text-right">Écart</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($sessions as $session)
                            @php
                                $totaux = $session->estOuverte() ? $session->totaux_en_direct : [
                                    'total_ventes'   => (float) $session->total_ventes,
                                    'total_depenses' => (float) $session->total_depenses,
                                    'total_achats'   => (float) ($session->total_achats ?? 0),
                                    'theorique_cash' => (float) $session->solde_theorique_especes,
                                ];
                                $ecartSess = (float) ($session->ecart ?? 0);
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-gray-900">{{ $session->reference }}</div>
                                    <div class="text-xs text-gray-500">{{ $session->user->name }}</div>
                                    @if($session->estOuverte())
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 mt-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                            En cours
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">
                                            Clôturée
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-gray-700">
                                    <div class="font-semibold">{{ $session->ouvert_le->format('H:i') }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $session->ouvert_le->format('d/m/Y') }}</div>
                                </td>
                                <td class="py-3 px-3 text-gray-700">
                                    @if($session->ferme_le)
                                        <div class="font-semibold">{{ $session->ferme_le->format('H:i') }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $session->ferme_le->format('d/m/Y') }}</div>
                                    @else
                                        <span class="text-green-600 font-semibold">—</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 font-medium text-gray-600">
                                    {{ $session->duree() }}
                                </td>
                                <td class="py-3 px-3 text-right font-medium text-gray-800">
                                    {{ number_format($session->fond_caisse_ouverture, 0, ',', ' ') }} F
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-primary">
                                    {{ number_format($totaux['total_ventes'], 0, ',', ' ') }} F
                                </td>
                                <td class="py-3 px-3 text-right font-medium">
                                    @php
                                        $sortiesSession = ($totaux['total_depenses'] ?? 0) + ($totaux['total_achats'] ?? 0);
                                    @endphp
                                    @if($sortiesSession > 0)
                                        <div class="text-red-600 font-bold">- {{ number_format($sortiesSession, 0, ',', ' ') }} F</div>
                                        <div class="text-[10px] text-gray-500 font-normal">
                                            @if(($totaux['total_depenses'] ?? 0) > 0)
                                                <span>Dép: {{ number_format($totaux['total_depenses'], 0, ',', ' ') }} F</span>
                                            @endif
                                            @if(($totaux['total_depenses'] ?? 0) > 0 && ($totaux['total_achats'] ?? 0) > 0)
                                                <span> • </span>
                                            @endif
                                            @if(($totaux['total_achats'] ?? 0) > 0)
                                                <span class="text-orange-600 font-semibold">🛒 Achats: {{ number_format($totaux['total_achats'], 0, ',', ' ') }} F</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right">
                                    @if($session->ferme_le)
                                        <span class="font-bold {{ $ecartSess < 0 ? 'text-red-600' : ($ecartSess > 0 ? 'text-blue-600' : 'text-green-600') }}">
                                            {{ $ecartSess > 0 ? '+' : '' }}{{ number_format($ecartSess, 0, ',', ' ') }} F
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">En cours</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        {{-- Bouton dérouler les ventes --}}
                                        <button wire:click="toggleDetail({{ $session->id }})"
                                                title="Voir les ventes de la session"
                                                class="p-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-600 transition-colors">
                                            <svg class="w-4 h-4 transition-transform {{ $sessionDetailId === $session->id ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        {{-- Bouton Télécharger Ticket Z --}}
                                        @if($session->ferme_le)
                                            <a href="{{ route('caisse.sessions.ticket-z', $session->id) }}"
                                               target="_blank"
                                               title="Télécharger Ticket Z (PDF)"
                                               class="p-1.5 rounded-lg bg-primary/10 hover:bg-primary/20 text-primary transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- Accordéon Détail complet de la session (Ventes, Achats, Dépenses) --}}
                            @if($sessionDetailId === $session->id)
                                <tr class="bg-gray-50/90">
                                    <td colspan="9" class="p-4 sm:p-5">
                                        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-4">

                                            {{-- Bilan Financier Rapide de la Session --}}
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 p-3 bg-gray-50 rounded-xl border border-gray-100 text-xs">
                                                <div>
                                                    <span class="text-gray-500 block text-[10px] uppercase font-bold">CA Encaissé</span>
                                                    <span class="font-extrabold text-primary text-sm">{{ number_format($totaux['total_ventes'], 0, ',', ' ') }} F</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500 block text-[10px] uppercase font-bold">Dépenses Caisse</span>
                                                    <span class="font-extrabold text-red-600 text-sm">- {{ number_format($totaux['total_depenses'], 0, ',', ' ') }} F</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500 block text-[10px] uppercase font-bold">Achats Caisse</span>
                                                    <span class="font-extrabold text-orange-600 text-sm">- {{ number_format($totaux['total_achats'] ?? 0, 0, ',', ' ') }} F</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500 block text-[10px] uppercase font-bold">Solde Espèces Attendu</span>
                                                    <span class="font-extrabold text-emerald-700 text-sm">{{ number_format($totaux['theorique_cash'], 0, ',', ' ') }} F</span>
                                                </div>
                                            </div>

                                            {{-- SECTION 1 : RÈGLEMENTS & ACHATS DE LA SESSION --}}
                                            @php
                                                $reglementsSession = $session->achatReglements;
                                            @endphp
                                            <div class="border border-orange-200/80 rounded-xl p-3 bg-orange-50/30">
                                                <div class="flex items-center justify-between pb-2 mb-2 border-b border-orange-100">
                                                    <h4 class="font-bold text-xs uppercase tracking-wider text-orange-950 flex items-center gap-1.5">
                                                        <span>🛒 Règlements d'Achats & Approvisionnements (Sorties réelles)</span>
                                                        <span class="px-1.5 py-0.2 rounded-full bg-orange-200/70 text-orange-800 text-[10px]">
                                                            {{ $reglementsSession->count() }}
                                                        </span>
                                                    </h4>
                                                    <span class="text-xs font-bold text-orange-800">
                                                        Total décaissé : {{ number_format($totaux['total_achats'] ?? 0, 0, ',', ' ') }} FCFA
                                                    </span>
                                                </div>

                                                @if($reglementsSession->isEmpty())
                                                    <p class="text-xs text-gray-400 italic py-1">Aucune sortie de caisse pour achat lors de cette session.</p>
                                                @else
                                                    <div class="overflow-x-auto max-h-48 overflow-y-auto">
                                                        <table class="w-full text-xs">
                                                            <thead class="bg-orange-100/60 text-orange-950">
                                                                <tr>
                                                                    <th class="p-2 text-left">Réf. Règlement</th>
                                                                    <th class="p-2 text-left">Facture Achat</th>
                                                                    <th class="p-2 text-left">Date / Heure</th>
                                                                    <th class="p-2 text-left">Fournisseur</th>
                                                                    <th class="p-2 text-left">Mode</th>
                                                                    <th class="p-2 text-right">Montant Décaissé</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-orange-100/60 bg-white">
                                                                @foreach($reglementsSession as $regl)
                                                                    <tr>
                                                                        <td class="p-2 font-mono font-bold text-gray-800">{{ $regl->reference }}</td>
                                                                        <td class="p-2 font-mono text-gray-600">{{ $regl->achat?->reference ?? '-' }}</td>
                                                                        <td class="p-2 text-gray-500">{{ ($regl->date_reglement ?? $regl->created_at)->format('H:i • d/m/Y') }}</td>
                                                                        <td class="p-2 text-gray-700 font-medium">{{ $regl->achat?->fournisseur?->nom ?? 'Comptant / Marché' }}</td>
                                                                        <td class="p-2 uppercase font-semibold text-[11px]">
                                                                            <span class="px-2 py-0.5 rounded {{ $regl->mode_paiement === 'cash' ? 'bg-amber-100 text-amber-800' : 'bg-sky-100 text-sky-800' }}">
                                                                                {{ $regl->mode_paiement ?? 'cash' }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="p-2 text-right font-black text-orange-700">- {{ number_format($regl->montant, 0, ',', ' ') }} F</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- SECTION 2 : DÉPENSES DE LA SESSION --}}
                                            @if($session->depenses->isNotEmpty())
                                                <div class="border border-red-200/80 rounded-xl p-3 bg-red-50/20">
                                                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-red-100">
                                                        <h4 class="font-bold text-xs uppercase tracking-wider text-red-950 flex items-center gap-1.5">
                                                            <span>💸 Dépenses sorties de caisse</span>
                                                            <span class="px-1.5 py-0.2 rounded-full bg-red-200/70 text-red-800 text-[10px]">
                                                                {{ $session->depenses->count() }}
                                                            </span>
                                                        </h4>
                                                        <span class="text-xs font-bold text-red-700">
                                                            Total : - {{ number_format($totaux['total_depenses'], 0, ',', ' ') }} FCFA
                                                        </span>
                                                    </div>

                                                    <div class="overflow-x-auto max-h-40 overflow-y-auto">
                                                        <table class="w-full text-xs">
                                                            <thead class="bg-red-100/60 text-red-950">
                                                                <tr>
                                                                    <th class="p-2 text-left">Description</th>
                                                                    <th class="p-2 text-left">Catégorie</th>
                                                                    <th class="p-2 text-left">Mode</th>
                                                                    <th class="p-2 text-right">Montant</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-red-100/60 bg-white">
                                                                @foreach($session->depenses as $d)
                                                                    <tr>
                                                                        <td class="p-2 font-medium text-gray-800">{{ $d->description }}</td>
                                                                        <td class="p-2 text-gray-500">{{ $d->categorie?->nom ?? 'Général' }}</td>
                                                                        <td class="p-2 uppercase text-[11px] font-semibold text-gray-600">{{ $d->mode_paiement }}</td>
                                                                        <td class="p-2 text-right font-black text-red-600">- {{ number_format($d->montant, 0, ',', ' ') }} F</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- SECTION 3 : VENTES DE LA SESSION --}}
                                            <div class="border border-gray-200 rounded-xl p-3">
                                                <div class="flex items-center justify-between pb-2 mb-2 border-b border-gray-100">
                                                    <h4 class="font-bold text-xs uppercase tracking-wider text-gray-700 flex items-center gap-1.5">
                                                        <span>🧾 Ventes de la session</span>
                                                        <span class="px-1.5 py-0.2 rounded-full bg-gray-200 text-gray-800 text-[10px]">
                                                            {{ $session->ventes->count() }}
                                                        </span>
                                                    </h4>
                                                    <span class="text-xs font-bold text-primary">
                                                        Total ventes : {{ number_format($totaux['total_ventes'], 0, ',', ' ') }} FCFA
                                                    </span>
                                                </div>

                                                @if($session->ventes->isEmpty())
                                                    <p class="text-xs text-gray-400 italic py-1">Aucune vente enregistrée durant cette session.</p>
                                                @else
                                                    <div class="overflow-x-auto max-h-56 overflow-y-auto">
                                                        <table class="w-full text-xs">
                                                            <thead class="bg-gray-100 text-gray-600">
                                                                <tr>
                                                                    <th class="p-2 text-left">Réf. Vente</th>
                                                                    <th class="p-2 text-left">Heure</th>
                                                                    <th class="p-2 text-left">Client</th>
                                                                    <th class="p-2 text-left">Règlement</th>
                                                                    <th class="p-2 text-right">Montant</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-100 bg-white">
                                                                @foreach($session->ventes as $v)
                                                                    <tr>
                                                                        <td class="p-2 font-mono font-medium text-gray-700">{{ $v->reference }}</td>
                                                                        <td class="p-2 text-gray-500">{{ $v->vendu_le ? $v->vendu_le->format('H:i') : $v->created_at->format('H:i') }}</td>
                                                                        <td class="p-2 text-gray-700">{{ $v->client?->nom ?? 'Client Comptoir' }}</td>
                                                                        <td class="p-2 uppercase text-[11px] font-semibold">
                                                                            <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">{{ $v->mode_paiement }}</span>
                                                                        </td>
                                                                        <td class="p-2 text-right font-bold text-gray-900">{{ number_format($v->total, 0, ',', ' ') }} F</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
