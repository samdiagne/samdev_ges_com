<div>
    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">
                Ventes
            </p>
            <p class="text-2xl font-bold text-dark mt-1">{{ $stats['count'] }}</p>
            <p class="text-xs text-gray-400 mt-1">transactions</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">
                Chiffre d'affaires
            </p>
            <p class="text-xl font-bold text-primary mt-1">
                {{ number_format($stats['total'], 0, ',', ' ') }}
                <span class="text-sm font-normal">FCFA</span>
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">
                Wave
            </p>
            <p class="text-xl font-bold text-blue-500 mt-1">
                {{ number_format($stats['wave'], 0, ',', ' ') }}
                <span class="text-sm font-normal">FCFA</span>
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">
                Orange Money
            </p>
            <p class="text-xl font-bold text-orange-500 mt-1">
                {{ number_format($stats['om'], 0, ',', ' ') }}
                <span class="text-sm font-normal">FCFA</span>
            </p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Recherche --}}
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Référence, client..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl
                              text-sm focus:outline-none focus:border-primary bg-gray-50"/>
            </div>

            {{-- Filtre Jour Précis --}}
            <div class="relative flex items-center">
                <input wire:model.live="filtreDate"
                       type="date"
                       title="Filtrer par jour précis"
                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary bg-gray-50 {{ $filtreDate ? 'border-primary ring-1 ring-primary/20 font-semibold text-primary' : '' }}"/>
                @if($filtreDate)
                    <button type="button"
                            wire:click="reinitialiserDate"
                            title="Effacer le filtre par jour"
                            class="absolute right-2 text-gray-400 hover:text-red-500 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>

            {{-- Filtre Mois --}}
            <div>
                <input wire:model.live="filtreMonth"
                       type="month"
                       title="Filtrer par mois"
                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary bg-gray-50 {{ ! $filtreDate && $filtreMonth ? 'font-semibold text-gray-800' : 'text-gray-500' }}"/>
            </div>

            {{-- Mode de paiement --}}
            <div>
                <select wire:model.live="filtrePaiement"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm
                               focus:outline-none focus:border-primary bg-gray-50">
                    <option value="">Tous paiements</option>
                    <option value="cash">Cash</option>
                    <option value="wave">Wave</option>
                    <option value="om">Orange Money</option>
                    <option value="carte">Carte bancaire</option>
                </select>
            </div>

            {{-- Source --}}
            <div>
                <select wire:model.live="filtreSource"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm
                               focus:outline-none focus:border-primary bg-gray-50">
                    <option value="">Toutes sources</option>
                    <option value="boutique">Boutique</option>
                    <option value="en_ligne">Site web</option>
                </select>
            </div>
        </div>

        {{-- Raccourcis rapides de dates --}}
        <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-xs text-gray-400 font-medium mr-1">Raccourcis :</span>
                <button type="button"
                        wire:click="filtrerAujourdhui"
                        class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all {{ $filtreDate === now()->toDateString() ? 'bg-primary text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Aujourd'hui
                </button>
                <button type="button"
                        wire:click="$set('filtreDate', '{{ now()->subDay()->toDateString() }}')"
                        class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all {{ $filtreDate === now()->subDay()->toDateString() ? 'bg-primary text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Hier
                </button>
                <button type="button"
                        wire:click="reinitialiserDate"
                        class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all {{ empty($filtreDate) ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Tout le mois
                </button>
            </div>

            @if($filtreDate)
                <div class="inline-flex items-center gap-1.5 text-xs text-primary font-semibold bg-primary/10 px-2.5 py-1 rounded-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Ventes du {{ \Carbon\Carbon::parse($filtreDate)->translatedFormat('l d F Y') }}</span>
                    <button type="button" wire:click="reinitialiserDate" class="ml-1 text-primary/70 hover:text-red-500">×</button>
                </div>
            @endif
        </div>
    </div>

    {{-- Barre d'actions groupées (affichée si des lignes sont sélectionnées) --}}
    @if(count($selectedVentes) > 0)
        <div class="mb-4 bg-primary/10 border border-primary/20 rounded-2xl p-3 sm:p-4 flex flex-col sm:flex-row items-center justify-between gap-3 animate-in fade-in slide-in-from-top-2 duration-150">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-xs shadow-sm">
                    {{ count($selectedVentes) }}
                </span>
                <div>
                    <p class="text-sm font-bold text-dark">
                        {{ count($selectedVentes) }} vente{{ count($selectedVentes) > 1 ? 's' : '' }} sélectionnée{{ count($selectedVentes) > 1 ? 's' : '' }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Total sélection : <span class="font-bold text-primary">{{ number_format($this->totalSelection, 0, ',', ' ') }} FCFA</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <button type="button"
                        wire:click="clearSelection"
                        class="px-3.5 py-2 text-xs font-semibold text-gray-600 hover:bg-white rounded-xl transition-colors border border-gray-200">
                    Tout désélectionner
                </button>

                @if(auth()->user()?->isAdmin())
                    <button type="button"
                            wire:click="confirmerSuppressionSelection"
                            class="px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-all shadow-md shadow-red-600/25 flex items-center gap-1.5 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Supprimer la sélection ({{ count($selectedVentes) }})</span>
                    </button>
                @endif
            </div>
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="w-10 px-3 py-3 text-center">
                            <input type="checkbox"
                                   wire:model.live="selectAll"
                                   class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer"
                                   title="Sélectionner toutes les lignes de la page"/>
                        </th>
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Référence</th>
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 hidden md:table-cell">
                            Client
                        </th>
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 hidden lg:table-cell">
                            Date
                        </th>
                        <th class="text-center text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 hidden md:table-cell">
                            Paiement
                        </th>
                        <th class="text-center text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 hidden lg:table-cell">
                            Source
                        </th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Total</th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 sticky right-0 bg-gray-50 border-l border-gray-100">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($ventes as $vente)
                        @php $isSelected = in_array((string)$vente->id, array_map('strval', $selectedVentes)); @endphp
                        <tr @class([
                                'transition-colors',
                                'bg-primary/5 hover:bg-primary/10' => $isSelected,
                                'hover:bg-gray-50/50' => !$isSelected,
                            ])>
                            <td class="w-10 px-3 py-3 text-center" onclick="event.stopPropagation()">
                                <input type="checkbox"
                                       wire:model.live="selectedVentes"
                                       value="{{ $vente->id }}"
                                       class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer"/>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-bold text-dark">
                                    {{ $vente->reference }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $vente->lignes->count() }} article(s)
                                </p>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <p class="text-sm text-dark">
                                    {{ $vente->client?->nom ?? 'Client anonyme' }}
                                </p>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <p class="text-sm text-dark">
                                    {{ $vente->vendu_le->format('d/m/Y') }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $vente->vendu_le->format('H:i') }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-center hidden md:table-cell">
                                @php
                                    $paymentColors = [
                                        'cash'  => 'bg-green-100 text-green-700',
                                        'wave'  => 'bg-blue-100 text-blue-700',
                                        'om'    => 'bg-orange-100 text-orange-700',
                                        'mix'   => 'bg-yellow-100 text-yellow-700',
                                        'card'  => 'bg-purple-100 text-purple-700',
                                    ];
                                    $paymentLabels = [
                                        'cash'  => '💵 Cash',
                                        'wave'  => 'Wave',
                                        'om'    => 'OM',
                                        'mix'   => 'Mixx by Yass',
                                        'card'  => '💳 Carte',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                             text-xs font-medium
                                             {{ $paymentColors[$vente->mode_paiement] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $paymentLabels[$vente->mode_paiement] ?? $vente->mode_paiement }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center hidden lg:table-cell">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                             text-xs font-medium
                                             {{ $vente->source === 'boutique'
                                                ? 'bg-dark/10 text-dark'
                                                : 'bg-accent/20 text-primary' }}">
                                    {{ $vente->source === 'boutique' ? '🏪 Boutique' : '🌐 Site web' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="text-sm font-bold text-dark">
                                    {{ number_format($vente->total, 0, ',', ' ') }}
                                    <span class="text-xs font-normal text-gray-400">FCFA</span>
                                </p>
                                @if($vente->remise > 0)
                                    <p class="text-xs text-secondary">
                                        -{{ number_format($vente->remise, 0, ',', ' ') }} remise
                                    </p>
                                @endif
                            </td>
                            <td class="px-4 py-3 sticky right-0 bg-white border-l border-gray-100">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button"
                                            onclick="imprimerTicketDirect('{{ route('caisse.ticket', $vente->id) }}?auto=1')"
                                            class="w-8 h-8 flex items-center justify-center
                                                   rounded-lg text-gray-400 hover:text-emerald-600
                                                   hover:bg-emerald-50 transition-colors"
                                            title="Imprimer le ticket">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="view({{ $vente->id }})"
                                            class="w-8 h-8 flex items-center justify-center
                                                   rounded-lg text-gray-400 hover:text-primary
                                                   hover:bg-primary/10 transition-colors"
                                            title="Voir détail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478
                                                     0 8.268 2.943 9.542 7-1.274 4.057-5.064
                                                     7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <a href="{{ route('factures.show', $vente->id) }}"
                                       class="w-8 h-8 flex items-center justify-center
                                              rounded-lg text-gray-400 hover:text-secondary
                                              hover:bg-secondary/10 transition-colors"
                                       title="Voir facture">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2
                                                     0 012-2h5.586a1 1 0 01.707.293l5.414
                                                     5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>

                                    @if(auth()->user()?->isAdmin())
                                        {{-- Modifier la vente (Admin) --}}
                                        <button wire:click="edit({{ $vente->id }})"
                                                class="w-8 h-8 flex items-center justify-center
                                                       rounded-lg text-gray-400 hover:text-amber-600
                                                       hover:bg-amber-50 transition-colors"
                                                title="Modifier la vente (Admin)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        {{-- Supprimer la vente (Admin) --}}
                                        <button wire:click="confirmerSuppression({{ $vente->id }})"
                                                class="w-8 h-8 flex items-center justify-center
                                                       rounded-lg text-gray-400 hover:text-red-600
                                                       hover:bg-red-50 transition-colors"
                                                title="Supprimer la vente (Admin)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <p class="text-sm text-gray-400">Aucune vente trouvée</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ventes->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $ventes->links() }}
            </div>
        @endif
    </div>

    {{-- Modal détail vente --}}
    @if($afficherModalDetail && $voirVente)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="$set('afficherModalDetail', false)"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg
                        max-h-[90vh] overflow-y-auto">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100
                            sticky top-0 bg-white rounded-t-2xl z-10">
                    <div>
                        <h3 class="text-base font-bold text-dark">
                            {{ $voirVente->reference }}
                        </h3>
                        <p class="text-xs text-gray-400">
                            {{ $voirVente->vendu_le->format('d/m/Y à H:i') }}
                            · par {{ $voirVente->user->name }}
                        </p>
                    </div>
                    <button wire:click="$set('afficherModalDetail', false)"
                            class="w-8 h-8 flex items-center justify-center rounded-lg
                                   text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">

                    {{-- Client --}}
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <div>
                            <p class="text-xs text-gray-400">Client</p>
                            <p class="text-sm font-semibold text-dark">
                                {{ $voirVente->client?->nom ?? 'Client anonyme' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400">Paiement</p>
                            <p class="text-sm font-semibold text-dark capitalize">
                                {{ $voirVente->mode_paiement }}
                            </p>
                        </div>
                    </div>

                    {{-- Articles --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase
                                  tracking-wider mb-2">
                            Articles ({{ $voirVente->lignes->count() }})
                        </p>
                        <div class="space-y-2">
                            @foreach($voirVente->lignes as $item)
                                <div class="flex items-center justify-between p-3
                                            bg-gray-50 rounded-xl">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-dark truncate">
                                            {{ $item->nom_produit }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ number_format($item->prix_unitaire, 0, ',', ' ') }}
                                            FCFA × {{ $item->quantite }}
                                        </p>
                                    </div>
                                    <p class="text-sm font-bold text-dark flex-shrink-0 ml-3">
                                        {{ number_format($item->total, 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Totaux --}}
                    <div class="border-t border-gray-100 pt-4 space-y-2">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Sous-total</span>
                            <span>{{ number_format($voirVente->total, 0, ',', ' ') }} FCFA</span>
                        </div>
                        @if($voirVente->remise > 0)
                            <div class="flex justify-between text-sm text-secondary">
                                <span>Remise</span>
                                <span>-{{ number_format($voirVente->remise, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-base font-bold text-dark
                                    border-t border-gray-100 pt-2">
                            <span>Total</span>
                            <span class="text-primary">
                                {{ number_format($voirVente->total, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Montant reçu</span>
                            <span>{{ number_format($voirVente->montant_recu, 0, ',', ' ') }} FCFA</span>
                        </div>
                        @if($voirVente->monnaie_rendue > 0)
                            <div class="flex justify-between text-sm text-green-600">
                                <span>Monnaie rendue</span>
                                <span>{{ number_format($voirVente->monnaie_rendue, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-2.5 pt-2">
                        <button type="button"
                                onclick="imprimerTicketDirect('{{ route('caisse.ticket', $voirVente->id) }}?auto=1')"
                                class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white
                                       text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm active:scale-98">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>🖨️ Imprimer le ticket</span>
                        </button>
                        <a href="{{ route('factures.show', $voirVente->id) }}"
                           class="flex-1 py-2.5 bg-primary hover:bg-primary/90 text-white
                                  text-sm font-semibold rounded-xl transition-colors text-center flex items-center justify-center gap-2">
                            <span>🧾 Voir la facture</span>
                        </a>
                    </div>

                    @if(auth()->user()?->isAdmin())
                        <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                            <button type="button"
                                    wire:click="edit({{ $voirVente->id }})"
                                    class="flex-1 py-2 text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-xl transition-colors flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span>Modifier cette vente</span>
                            </button>
                            <button type="button"
                                    wire:click="confirmerSuppression({{ $voirVente->id }})"
                                    class="flex-1 py-2 text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 rounded-xl transition-colors flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <span>Supprimer cette vente</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL 2 : CONFIRMATION SUPPRESSION VENTE (ADMIN)
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($afficherModalSupprimer && $venteASupprimer)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="$wire.annulerSuppression()">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm cursor-pointer"
                 wire:click="annulerSuppression"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10 space-y-4 animate-in fade-in zoom-in-95 duration-150">
                <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                <div class="text-center">
                    <h3 class="font-bold text-dark text-base">Supprimer la vente #{{ $venteASupprimer->reference }} ?</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Montant : <span class="font-bold text-dark">{{ number_format($venteASupprimer->total, 0, ',', ' ') }} FCFA</span>
                        · Client : <span class="font-bold text-dark">{{ $venteASupprimer->client?->nom ?? 'Client anonyme' }}</span>
                    </p>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800 space-y-1">
                    <p class="font-bold flex items-center gap-1">
                        <span>⚠️ Impact sur les stocks :</span>
                    </p>
                    <p>
                        Les quantités des {{ $venteASupprimer->lignes->count() }} article(s) de cette vente seront <strong>automatiquement réintégrées en stock</strong>. La facture associée sera également supprimée.
                    </p>
                </div>

                <div class="flex gap-2.5 justify-center pt-2">
                    <button type="button"
                            wire:click="annulerSuppression"
                            class="flex-1 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                        Annuler
                    </button>
                    <button type="button"
                            wire:click="supprimerVente"
                            wire:loading.attr="disabled"
                            class="flex-1 py-2.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-all shadow-md shadow-red-600/25 flex items-center justify-center gap-1.5 disabled:opacity-50">
                        <svg wire:loading wire:target="supprimerVente" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span>Oui, supprimer</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL 3 : MODIFICATION DE LA VENTE (ADMIN)
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($afficherModalModifier)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4"
             @keydown.escape.window="$wire.annulerModification()">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm cursor-pointer"
                 wire:click="annulerModification"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[92vh] flex flex-col z-10 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-white flex-shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-dark text-base">Modifier la vente #{{ $editReference }}</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800">Admin</span>
                            </div>
                            <p class="text-xs text-gray-400">Les modifications ajusteront automatiquement le stock et la facture</p>
                        </div>
                    </div>
                    <button type="button" wire:click="annulerModification" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Corps scrollable --}}
                <div class="p-5 space-y-4 overflow-y-auto flex-1 divide-y divide-gray-100">

                    {{-- Paramètres de base (Client, Date, Paiement) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pb-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Client</label>
                            <select wire:model.live="editClientId"
                                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs bg-gray-50 focus:outline-none focus:border-primary">
                                <option value="">-- Client anonyme --</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->nom }} ({{ $c->telephone }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Date & Heure de vente</label>
                            <input wire:model="editDate"
                                   type="datetime-local"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs bg-gray-50 focus:outline-none focus:border-primary"/>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Mode de paiement</label>
                            <select wire:model.live="editModePaiement"
                                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs bg-gray-50 focus:outline-none focus:border-primary">
                                <option value="cash">💵 Cash (Espèces)</option>
                                <option value="wave">Wave</option>
                                <option value="om">Orange Money</option>
                                <option value="mix">Mixx by Yass</option>
                                <option value="carte">💳 Carte bancaire</option>
                                <option value="a_regler">⏳ À régler (Crédit / Facture)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Section Articles / Lignes --}}
                    <div class="pt-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-dark uppercase tracking-wider">
                                Articles de la vente ({{ count($editLignes) }})
                            </h4>

                            {{-- Recherche pour ajout produit --}}
                            <div class="relative w-64">
                                <input wire:model.live.debounce.250ms="searchAjoutProduit"
                                       type="text"
                                       placeholder="+ Ajouter un plat/produit..."
                                       class="w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-xl text-xs bg-gray-50 focus:outline-none focus:border-primary"/>
                                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>

                                @if(count($produitsDisponibles) > 0)
                                    <div class="absolute right-0 top-full mt-1 bg-white rounded-xl shadow-xl border border-gray-100 z-30 overflow-hidden w-72 max-h-48 overflow-y-auto">
                                        @foreach($produitsDisponibles as $p)
                                            <button type="button"
                                                    wire:click="addProduitToEdit({{ $p['id'] }})"
                                                    class="w-full flex items-center justify-between px-3 py-2 hover:bg-primary/5 text-left border-b border-gray-50 last:border-0 transition-colors">
                                                <div class="truncate">
                                                    <p class="text-xs font-bold text-dark truncate">{{ $p['nom'] }}</p>
                                                    <p class="text-[10px] text-gray-400">Stock : {{ $p['stock'] }} rest.</p>
                                                </div>
                                                <div class="text-right flex-shrink-0 ml-2">
                                                    <span class="text-xs font-bold text-primary">{{ number_format($p['prix_vente'], 0, ',', ' ') }} F</span>
                                                    <span class="block text-[9px] text-secondary font-semibold">+ Ajouter</span>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        @error('editLignes')
                            <p class="text-xs text-red-500 font-semibold">{{ $message }}</p>
                        @enderror

                        {{-- Liste des lignes --}}
                        <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                            @forelse($editLignes as $idx => $item)
                                <div class="flex items-center gap-2.5 p-2.5 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-dark truncate">{{ $item['nom_produit'] }}</p>
                                        <p class="text-[10px] text-gray-400">{{ number_format($item['prix_unitaire'], 0, ',', ' ') }} FCFA/u</p>
                                    </div>

                                    {{-- Quantité --}}
                                    <div class="flex items-center gap-1 flex-shrink-0 bg-white rounded-lg p-0.5 border border-gray-200">
                                        <button type="button"
                                                wire:click="updateEditQuantite({{ $idx }}, {{ $item['quantite'] - 1 }})"
                                                class="w-6 h-6 flex items-center justify-center rounded bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs">
                                            −
                                        </button>
                                        <input type="number"
                                               min="1"
                                               value="{{ $item['quantite'] }}"
                                               x-on:focus="$event.target.select()"
                                               x-on:change="let val = parseInt($event.target.value); $wire.updateEditQuantite({{ $idx }}, isNaN(val) ? 1 : val)"
                                               class="w-10 h-6 text-center text-xs font-bold bg-transparent border-0 focus:ring-0 p-0"/>
                                        <button type="button"
                                                wire:click="updateEditQuantite({{ $idx }}, {{ $item['quantite'] + 1 }})"
                                                class="w-6 h-6 flex items-center justify-center rounded bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs">
                                            +
                                        </button>
                                    </div>

                                    {{-- Total ligne --}}
                                    <div class="text-right flex-shrink-0 w-24">
                                        <p class="text-xs font-bold text-dark">{{ number_format($item['total'], 0, ',', ' ') }} F</p>
                                    </div>

                                    {{-- Supprimer ligne --}}
                                    <button type="button"
                                            wire:click="removeEditLigne({{ $idx }})"
                                            class="w-7 h-7 flex items-center justify-center text-gray-300 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @empty
                                <div class="text-center py-6 text-gray-400 text-xs">
                                    Aucun article dans cette vente. Utilisez la barre ci-dessus pour en ajouter.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Section Récapitulatif Totaux & Encaissement --}}
                    <div class="pt-4 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Notes / Remarques</label>
                                    <textarea wire:model="editNotes"
                                              rows="3"
                                              placeholder="Motif de la modification, détails..."
                                              class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs bg-gray-50 focus:outline-none focus:border-primary resize-none"></textarea>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 space-y-2 text-xs">
                                <div class="flex justify-between text-gray-500">
                                    <span>Sous-total articles :</span>
                                    <span class="font-bold text-dark">{{ number_format($editSousTotal, 0, ',', ' ') }} FCFA</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Remise globale :</span>
                                    <div class="flex items-center gap-1">
                                        <input wire:model.live="editRemise"
                                               type="number" min="0" step="50"
                                               class="w-24 text-right px-2 py-1 border border-gray-200 rounded-lg text-xs bg-white font-semibold"/>
                                        <span class="text-[10px] text-gray-400">FCFA</span>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 pt-2 flex items-baseline justify-between">
                                    <span class="font-bold text-dark">TOTAL NET :</span>
                                    <span class="text-base font-extrabold text-primary">{{ number_format($editTotal, 0, ',', ' ') }} FCFA</span>
                                </div>

                                @if($editModePaiement !== 'a_regler')
                                    <div class="flex items-center justify-between pt-1">
                                        <span class="text-gray-500">Montant reçu :</span>
                                        <div class="flex items-center gap-1">
                                            <input wire:model.live="editMontantRecu"
                                                   type="number" min="0" step="100"
                                                   class="w-24 text-right px-2 py-1 border border-gray-200 rounded-lg text-xs bg-white font-semibold"/>
                                            <span class="text-[10px] text-gray-400">FCFA</span>
                                        </div>
                                    </div>

                                    <div class="flex justify-between text-emerald-700 font-semibold pt-1 border-t border-dashed border-gray-200">
                                        <span>Monnaie rendue :</span>
                                        <span>{{ number_format($editMonnaieRendue, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer actions --}}
                <div class="flex items-center justify-end gap-2.5 px-5 py-3.5 border-t border-gray-100 bg-gray-50/70 flex-shrink-0">
                    <button type="button"
                            wire:click="annulerModification"
                            class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-200 rounded-xl transition-colors">
                        Annuler
                    </button>
                    <button type="button"
                            wire:click="updateVente"
                            wire:loading.attr="disabled"
                            class="px-5 py-2.5 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-primary/25 flex items-center gap-2 disabled:opacity-50">
                        <svg wire:loading wire:target="updateVente" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span>Enregistrer les modifications</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL 4 : CONFIRMATION SUPPRESSION SÉLECTION MULTIPLE (ADMIN)
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($afficherModalSupprimerSelection)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="$wire.annulerSuppressionSelection()">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm cursor-pointer"
                 wire:click="annulerSuppressionSelection"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10 space-y-4 animate-in fade-in zoom-in-95 duration-150">
                <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                <div class="text-center">
                    <h3 class="font-bold text-dark text-base">Supprimer {{ count($selectedVentes) }} ventes sélectionnées ?</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Montant total cumulé : <span class="font-bold text-primary">{{ number_format($this->totalSelection, 0, ',', ' ') }} FCFA</span>
                    </p>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-xs text-red-800 space-y-1">
                    <p class="font-bold flex items-center gap-1">
                        <span>⚠️ Attention : Action groupée irréversible</span>
                    </p>
                    <p>
                        Les quantités de tous les articles vendus dans ces <strong>{{ count($selectedVentes) }} ventes</strong> seront <strong>automatiquement restituées en stock</strong>. Les factures correspondantes seront également supprimées.
                    </p>
                </div>

                <div class="flex gap-2.5 justify-center pt-2">
                    <button type="button"
                            wire:click="annulerSuppressionSelection"
                            class="flex-1 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                        Annuler
                    </button>
                    <button type="button"
                            wire:click="supprimerSelection"
                            wire:loading.attr="disabled"
                            class="flex-1 py-2.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-all shadow-md shadow-red-600/25 flex items-center justify-center gap-1.5 disabled:opacity-50">
                        <svg wire:loading wire:target="supprimerSelection" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span>Oui, tout supprimer</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Iframe invisible pour impression rapide du ticket sans quitter la page --}}
    <iframe id="vente-ticket-frame" style="position: absolute; width: 0; height: 0; border: 0; visibility: hidden;"></iframe>

    @push('scripts')
    <script>
        window.imprimerTicketDirect = function(url) {
            const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
            if (isMobile) {
                window.open(url, '_blank');
                return;
            }
            const frame = document.getElementById('vente-ticket-frame');
            if (frame) {
                frame.src = url + (url.includes('?') ? '&' : '?') + 't=' + Date.now();
            } else {
                window.open(url, '_blank', 'width=380,height=600');
            }
        };
    </script>
    @endpush
</div>