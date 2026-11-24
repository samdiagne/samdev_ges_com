<div>
    {{-- Filtres --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs text-gray-400 mb-1">Du</label>
                <input wire:model="dateFrom" wire:change="appliquerFiltre" type="date"
                       class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                              focus:outline-none focus:border-primary bg-gray-50"/>
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Au</label>
                <input wire:model="dateTo" wire:change="appliquerFiltre" type="date"
                       class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                              focus:outline-none focus:border-primary bg-gray-50"/>
            </div>
            {{-- Raccourcis rapides --}}
            <div class="flex items-center gap-2 flex-wrap">
                <button wire:click="periodeAujourdhui"
                        class="px-3 py-2 text-xs font-medium bg-gray-100 hover:bg-primary hover:text-white
                               text-gray-600 rounded-xl transition-all">
                    Aujourd'hui
                </button>
                <button wire:click="periode7Jours"
                        class="px-3 py-2 text-xs font-medium bg-gray-100 hover:bg-primary hover:text-white
                               text-gray-600 rounded-xl transition-all">
                    7 jours
                </button>
                <button wire:click="periodeCeMois"
                        class="px-3 py-2 text-xs font-medium bg-gray-100 hover:bg-primary hover:text-white
                               text-gray-600 rounded-xl transition-all">
                    Ce mois
                </button>
                <button wire:click="periodeMoisDernier"
                        class="px-3 py-2 text-xs font-medium bg-gray-100 hover:bg-primary hover:text-white
                               text-gray-600 rounded-xl transition-all">
                    Mois dernier
                </button>
            </div>
            {{-- Bouton Appliquer --}}
            <button wire:click="appliquerFiltre"
                    class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl
                           hover:bg-primary/90 transition-all flex items-center gap-2 ml-auto">
                <svg wire:loading wire:target="appliquerFiltre,periodeAujourdhui,periode7Jours,periodeCeMois,periodeMoisDernier"
                     class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <svg wire:loading.remove
                     wire:target="appliquerFiltre,periodeAujourdhui,periode7Jours,periodeCeMois,periodeMoisDernier"
                     class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filtrer
            </button>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-1 mb-6 bg-white rounded-2xl border
                border-gray-100 shadow-sm p-1.5 w-fit flex-wrap">
        @foreach([
            'ventes'     => '📊 Ventes',
            'produits'      => '🍽️ Produits',
            'clients'    => '👥 Clients',
            'achats'     => '📦 Achats',
            'tresorerie' => '💰 Trésorerie',
        ] as $tab => $label)
            <button wire:click="$set('typeRapport', '{{ $tab }}')"
                    @class([
                        'px-4 py-2 rounded-xl text-sm font-medium transition-all',
                        'bg-primary text-white shadow-lg shadow-primary/25' => $typeRapport === $tab,
                        'text-gray-500 hover:text-dark hover:bg-gray-50' => $typeRapport !== $tab,
                    ])>
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ══ TAB VENTES ══ --}}
    @if($typeRapport === 'ventes' && $rapport)
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">CA Total</p>
                <p class="text-xl font-bold text-primary mt-1">
                    {{ number_format($rapport['total'], 0, ',', ' ') }}
                    <span class="text-sm font-normal text-gray-400">FCFA</span>
                </p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Ventes</p>
                <p class="text-2xl font-bold text-dark mt-1">{{ $rapport['count'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Panier moyen</p>
                <p class="text-xl font-bold text-dark mt-1">
                    {{ number_format($rapport['panier_moyen'], 0, ',', ' ') }}
                    <span class="text-sm font-normal text-gray-400">FCFA</span>
                </p>
            </div>
        </div>

        @if(!empty($rapport['par_mode']))
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4">
                <h3 class="text-sm font-bold text-dark mb-4">Répartition par mode de paiement</h3>
                <div class="space-y-3">
                    @php
                        $modeLabels = [
                            'cash'  => '💵 Cash',
                            'wave'  => 'Wave',
                            'om'    => 'Orange Money',
                            'carte' => '💳 Carte bancaire',
                        ];
                    @endphp
                    @foreach($rapport['par_mode'] as $mode => $data)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-dark">{{ $modeLabels[$mode] ?? ucfirst($mode) }}</span>
                            <div class="text-right">
                                <span class="text-sm font-bold text-primary">
                                    {{ number_format($data['total'], 0, ',', ' ') }} FCFA
                                </span>
                                <span class="text-xs text-gray-400 ml-2">({{ $data['count'] }} paiements)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(!empty($rapport['par_jour']))
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-dark mb-4">CA par jour</h3>
                <div class="space-y-2">
                    @foreach($rapport['par_jour'] as $jour => $ca)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-sm text-dark">{{ $jour }}</span>
                            <span class="text-sm font-bold text-primary">
                                {{ number_format($ca, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    {{-- ══ TAB Produits ══ --}}
    @if($typeRapport === 'produits' && $rapport)
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">CA Brut Produits</p>
                <p class="text-xl font-bold text-primary mt-1">
                    {{ number_format($rapport['total_ca'], 0, ',', ' ') }}
                    <span class="text-sm font-normal text-gray-400">FCFA</span>
                </p>
                @if(($rapport['remises_globales'] ?? 0) > 0)
                    <p class="text-xs text-gray-400 mt-1">
                        Net après remises ({{ number_format($rapport['remises_globales'], 0, ',', ' ') }} F) :
                        <span class="font-semibold text-dark">{{ number_format($rapport['ca_ventes'], 0, ',', ' ') }} FCFA</span>
                    </p>
                @endif
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Portions vendues</p>
                <p class="text-2xl font-bold text-dark mt-1">{{ $rapport['total_portions'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="text-sm font-bold text-dark">Top Produits</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">#</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">Produit</th>
                            <th class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">Quantité</th>
                            <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">CA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($rapport['lignes'] as $i => $ligne)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-3 text-xs text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-dark">{{ $ligne->nom_produit }}</td>
                                <td class="px-4 py-3 text-center text-sm text-dark">{{ $ligne->total_qte }}</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-primary">
                                    {{ number_format($ligne->total_ca, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400">
                                    Aucune vente sur cette période
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ══ TAB CLIENTS ══ --}}
    @if($typeRapport === 'clients' && $rapport)
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Clients actifs</p>
                <p class="text-2xl font-bold text-dark mt-1">{{ $rapport['total_clients'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Nouveaux</p>
                <p class="text-2xl font-bold text-primary mt-1">{{ $rapport['nouveaux'] }}</p>
            </div>
            @if($rapport['client_top'])
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Meilleur client</p>
                    <p class="text-sm font-bold text-dark mt-1">{{ $rapport['client_top']['nom'] }}</p>
                    <p class="text-xs text-primary">{{ number_format($rapport['client_top']['total'], 0, ',', ' ') }} FCFA</p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="text-sm font-bold text-dark">Classement clients</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">Client</th>
                            <th class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">Commandes</th>
                            <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($rapport['par_client'] as $clientData)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-3 text-sm font-medium text-dark">{{ $clientData['nom'] }}</td>
                                <td class="px-4 py-3 text-center text-sm text-dark">{{ $clientData['count'] }}</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-primary">
                                    {{ number_format($clientData['total'], 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-400">
                                    Aucune vente sur cette période
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ══ TAB ACHATS ══ --}}
    @if($typeRapport === 'achats' && $rapport)
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Achats</p>
                <p class="text-2xl font-bold text-dark mt-1">{{ $rapport['count'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total</p>
                <p class="text-xl font-bold text-primary mt-1">
                    {{ number_format($rapport['total'], 0, ',', ' ') }}
                    <span class="text-sm font-normal text-gray-400">FCFA</span>
                </p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Payé</p>
                <p class="text-xl font-bold text-green-600 mt-1">
                    {{ number_format($rapport['paye'], 0, ',', ' ') }}
                    <span class="text-sm font-normal text-gray-400">FCFA</span>
                </p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Restant dû</p>
                <p class="text-xl font-bold text-red-500 mt-1">
                    {{ number_format($rapport['du'], 0, ',', ' ') }}
                    <span class="text-sm font-normal text-gray-400">FCFA</span>
                </p>
            </div>
        </div>

        @if(!empty($rapport['par_fournisseur']))
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-dark mb-4">Par fournisseur</h3>
                <div class="space-y-3">
                    @foreach($rapport['par_fournisseur'] as $fData)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-dark">{{ $fData['nom'] }}</span>
                            <div class="text-right">
                                <span class="text-sm font-bold text-primary">
                                    {{ number_format($fData['total'], 0, ',', ' ') }} FCFA
                                </span>
                                <span class="text-xs text-gray-400 ml-2">({{ $fData['count'] }} achats)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    {{-- ══ TAB TRÉSORERIE ══ --}}
    @if($typeRapport === 'tresorerie' && $rapport)
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Recettes</p>
                <p class="text-xl font-bold text-green-600 mt-1">
                    {{ number_format($rapport['recettes'], 0, ',', ' ') }}
                    <span class="text-sm font-normal text-gray-400">FCFA</span>
                </p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Achats</p>
                <p class="text-xl font-bold text-orange-500 mt-1">
                    {{ number_format($rapport['achats'], 0, ',', ' ') }}
                    <span class="text-sm font-normal text-gray-400">FCFA</span>
                </p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Dépenses</p>
                <p class="text-xl font-bold text-red-500 mt-1">
                    {{ number_format($rapport['depenses'], 0, ',', ' ') }}
                    <span class="text-sm font-normal text-gray-400">FCFA</span>
                </p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Solde</p>
                <p class="text-xl font-bold mt-1 {{ $rapport['solde'] >= 0 ? 'text-green-600' : 'text-red-500' }}">
                    {{ number_format($rapport['solde'], 0, ',', ' ') }}
                    <span class="text-sm font-normal text-gray-400">FCFA</span>
                </p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-bold text-dark mb-4">Résumé trésorerie</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-sm text-dark">+ Recettes (ventes)</span>
                    <span class="text-sm font-bold text-green-600">
                        {{ number_format($rapport['recettes'], 0, ',', ' ') }} FCFA
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-sm text-dark">− Achats fournisseurs</span>
                    <span class="text-sm font-bold text-orange-500">
                        {{ number_format($rapport['achats'], 0, ',', ' ') }} FCFA
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-sm text-dark">− Dépenses</span>
                    <span class="text-sm font-bold text-red-500">
                        {{ number_format($rapport['depenses'], 0, ',', ' ') }} FCFA
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 font-bold">
                    <span class="text-sm text-dark">= Solde net</span>
                    <span class="text-sm {{ $rapport['solde'] >= 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ number_format($rapport['solde'], 0, ',', ' ') }} FCFA
                    </span>
                </div>
            </div>
        </div>
    @endif
</div>
