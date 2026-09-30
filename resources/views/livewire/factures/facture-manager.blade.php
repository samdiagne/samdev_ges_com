<div>
    {{-- ── Statistiques ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Facturé</p>
            <p class="text-xl sm:text-2xl font-bold text-dark mt-1">
                {{ number_format($stats['total'], 0, ',', ' ') }}
                <span class="text-xs font-normal text-gray-400">FCFA</span>
            </p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $stats['count'] }} facture{{ $stats['count'] > 1 ? 's' : '' }} ce mois</p>
        </div>

        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-4 bg-emerald-50/20">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Payées</p>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-emerald-700 mt-1">
                {{ number_format($stats['total_paye'], 0, ',', ' ') }}
                <span class="text-xs font-normal text-emerald-600">FCFA</span>
            </p>
            <p class="text-xs text-emerald-600/80 mt-0.5">{{ $stats['count_paye'] }} réglée{{ $stats['count_paye'] > 1 ? 's' : '' }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-4 bg-amber-50/20">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider">À régler</p>
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-amber-700 mt-1">
                {{ number_format($stats['total_due'], 0, ',', ' ') }}
                <span class="text-xs font-normal text-amber-600">FCFA</span>
            </p>
            <p class="text-xs text-amber-600/80 mt-0.5">{{ $stats['count_due'] }} en attente</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col justify-between">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Taux d'encaissement</p>
            @php
                $taux = $stats['total'] > 0 ? round(($stats['total_paye'] / $stats['total']) * 100) : 100;
            @endphp
            <div>
                <p class="text-xl sm:text-2xl font-bold text-primary">{{ $taux }}%</p>
                <div class="w-full bg-gray-100 h-1.5 rounded-full mt-2 overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $taux }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filtres de recherche et statut ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-3 sm:p-4 mb-4 space-y-3">
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Recherche --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="N° facture, client..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl
                              text-sm focus:outline-none focus:border-primary bg-gray-50"/>
            </div>

            {{-- Sélecteur mois --}}
            <input wire:model.live="filtreMonth" type="month"
                   class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                          focus:outline-none focus:border-primary bg-gray-50"/>
        </div>

        {{-- Onglets de filtre statut --}}
        <div class="flex items-center gap-2 pt-1 border-t border-gray-100">
            <span class="text-xs text-gray-400 font-semibold mr-1">Statut :</span>
            <button wire:click="$set('filtreStatut', '')"
                    @class([
                        'px-3 py-1 rounded-xl text-xs font-bold transition-all',
                        'bg-dark text-white shadow-sm' => $filtreStatut === '',
                        'bg-gray-100 text-gray-600 hover:bg-gray-200' => $filtreStatut !== '',
                    ])>
                Toutes ({{ $stats['count'] }})
            </button>
            <button wire:click="$set('filtreStatut', 'payee')"
                    @class([
                        'px-3 py-1 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5',
                        'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' => $filtreStatut === 'payee',
                        'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' => $filtreStatut !== 'payee',
                    ])>
                <span>Payées ({{ $stats['count_paye'] }})</span>
            </button>
            <button wire:click="$set('filtreStatut', 'a_regler')"
                    @class([
                        'px-3 py-1 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5',
                        'bg-amber-600 text-white shadow-sm shadow-amber-600/20' => $filtreStatut === 'a_regler',
                        'bg-amber-50 text-amber-700 hover:bg-amber-100' => $filtreStatut !== 'a_regler',
                    ])>
                <span>À régler ({{ $stats['count_due'] }})</span>
            </button>
        </div>
    </div>

    {{-- Flash message --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 3500)"
             class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Tableau des factures ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">N° Facture</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 hidden md:table-cell">Client</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 hidden lg:table-cell">Date</th>
                        <th class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">Statut</th>
                        <th class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 hidden md:table-cell">Règlement</th>
                        <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">Total</th>
                        <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3 sticky right-0 bg-gray-50 border-l border-gray-100">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($factures as $facture)
                        @php
                            $pmLabels = [
                                'cash'     => 'Cash',
                                'wave'     => 'Wave',
                                'om'       => 'Orange Money',
                                'mix'      => 'Mixx',
                                'card'     => 'Carte',
                                'carte'    => 'Carte',
                                'a_regler' => 'En attente',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            {{-- N° Facture + Réf --}}
                            <td class="px-4 py-3">
                                <a href="{{ route('factures.show', $facture->vente_id) }}" class="text-sm font-bold text-dark hover:text-primary transition-colors">
                                    {{ $facture->numero_facture }}
                                </a>
                                <p class="text-[11px] text-gray-400">
                                    {{ $facture->vente->reference }}
                                </p>
                            </td>

                            {{-- Client --}}
                            <td class="px-4 py-3 hidden md:table-cell">
                                <p class="text-sm text-dark font-medium">
                                    {{ $facture->vente->client?->nom ?? 'Client anonyme' }}
                                </p>
                            </td>

                            {{-- Date --}}
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <p class="text-sm text-dark">
                                    {{ $facture->emise_le->format('d/m/Y') }}
                                </p>
                                <p class="text-[11px] text-gray-400">
                                    {{ $facture->emise_le->format('H:i') }}
                                </p>
                            </td>

                            {{-- Statut (Badge + bouton bascule direct) --}}
                            <td class="px-4 py-3 text-center">
                                <button wire:click="toggleStatut({{ $facture->id }})"
                                        wire:loading.attr="disabled"
                                        title="Cliquez pour changer le statut (Payée <-> À régler)"
                                        @class([
                                            'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold transition-all shadow-xs hover:scale-105 active:scale-95',
                                            'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' => $facture->estPayee(),
                                            'bg-amber-100 text-amber-700 hover:bg-amber-200' => !$facture->estPayee(),
                                        ])>
                                    <span class="w-1.5 h-1.5 rounded-full {{ $facture->estPayee() ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    <span>{{ $facture->estPayee() ? 'Payée' : 'À régler' }}</span>
                                    <svg class="w-3 h-3 opacity-60 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </button>
                            </td>

                            {{-- Mode Règlement --}}
                            <td class="px-4 py-3 text-center hidden md:table-cell">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-600">
                                    {{ $pmLabels[$facture->vente->mode_paiement] ?? ucfirst($facture->vente->mode_paiement) }}
                                </span>
                            </td>

                            {{-- Total --}}
                            <td class="px-4 py-3 text-right">
                                <p class="text-sm font-bold text-dark">
                                    {{ number_format($facture->total, 0, ',', ' ') }}
                                </p>
                                <span class="text-[10px] text-gray-400">FCFA</span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3 sticky right-0 bg-white border-l border-gray-100">
                                <div class="flex items-center justify-end gap-1">
                                    {{-- Bascule rapide statut --}}
                                    <button wire:click="toggleStatut({{ $facture->id }})"
                                            wire:loading.attr="disabled"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg transition-colors {{ $facture->estPayee() ? 'text-amber-500 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }}"
                                            title="{{ $facture->estPayee() ? 'Passer en À régler' : 'Marquer comme payée' }}">
                                        @if($facture->estPayee())
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                    </button>

                                    {{-- Voir --}}
                                    <a href="{{ route('factures.show', $facture->vente_id) }}"
                                       class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-primary hover:bg-primary/10 transition-colors"
                                       title="Aperçu de la facture">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    {{-- Télécharger --}}
                                    <a href="{{ route('factures.download', $facture->vente_id) }}"
                                       class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-secondary hover:bg-secondary/10 transition-colors"
                                       title="Télécharger le PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-600">Aucune facture trouvée</p>
                                    <p class="text-xs text-gray-400">Modifiez les filtres de recherche ou sélectionnez un autre mois.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($factures->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $factures->links() }}
            </div>
        @endif
    </div>
</div>