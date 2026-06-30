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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
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
            <input wire:model.live="filtreMonth"
                   type="month"
                   class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm
                          focus:outline-none focus:border-primary bg-gray-50"/>
            <select wire:model.live="filtrePaiement"
                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-primary bg-gray-50">
                <option value="">Tous paiements</option>
                <option value="cash">Cash</option>
                <option value="wave">Wave</option>
                <option value="om">Orange Money</option>
                <option value="carte">Carte bancaire</option>
            </select>
            <select wire:model.live="filtreSource"
                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-primary bg-gray-50">
                <option value="">Toutes sources</option>
                <option value="boutique">Boutique</option>
                <option value="en_ligne">Site web</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
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
                        <tr class="hover:bg-gray-50/50 transition-colors">
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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
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
                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('factures.show', $voirVente->id) }}"
                           class="flex-1 py-2.5 bg-primary hover:bg-primary/90 text-white
                                  text-sm font-medium rounded-xl transition-colors text-center">
                            🧾 Voir la facture
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>