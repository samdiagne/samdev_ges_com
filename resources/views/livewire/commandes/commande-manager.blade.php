<div>
    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label' => 'Total',      'value' => $stats['total'],      'color' => 'text-dark'],
            ['label' => 'En attente', 'value' => $stats['en_attente'], 'color' => 'text-yellow-600'],
            ['label' => 'Confirmées', 'value' => $stats['confirmee'],  'color' => 'text-blue-600'],
            ['label' => 'Livrées',    'value' => $stats['livree'],     'color' => 'text-green-600'],
        ] as $stat)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider">
                    {{ $stat['label'] }}
                </p>
                <p class="text-2xl font-bold {{ $stat['color'] }} mt-1">
                    {{ $stat['value'] }}
                </p>
            </div>
        @endforeach
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 bg-green-50 border border-green-200 text-green-800
                    px-4 py-3 rounded-xl text-sm font-medium">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 4000)"
             class="mb-4 bg-red-50 border border-red-200 text-red-800
                    px-4 py-3 rounded-xl text-sm font-medium">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- Filtres --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
        <div class="flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-48">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text" placeholder="Référence, client, téléphone..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl
                              text-sm focus:outline-none focus:border-primary bg-gray-50"/>
            </div>
            <select wire:model.live="filtreStatut"
                    class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-primary bg-gray-50">
                <option value="">Tous statuts</option>
                <option value="en_attente">En attente</option>
                <option value="confirmee">Confirmée</option>
                <option value="en_preparation">En préparation</option>
                <option value="expediee">Expédiée</option>
                <option value="livree">Livrée</option>
                <option value="annulee">Annulée</option>
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
                                   uppercase tracking-wider px-4 py-3">Client</th>
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3
                                   hidden lg:table-cell">Mode</th>
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3
                                   hidden lg:table-cell">Paiement</th>
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3
                                   hidden lg:table-cell">Livraison</th>
                        <th class="text-center text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Statut</th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Total</th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 sticky right-0 bg-gray-50 border-l border-gray-100">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($commandes as $commande)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-sm font-bold text-dark">
                                    {{ $commande->reference }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $commande->created_at->format('d/m/Y H:i') }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-dark">
                                    {{ $commande->nom_client }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $commande->telephone_client }}
                                </p>
                            </td>
                            {{-- Mode de paiement --}}
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <p class="text-xs text-gray-600">{{ $commande->mode_paiement }}</p>
                            </td>

                            {{-- Statut paiement modifiable --}}
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <select wire:change="updatePaymentStatus({{ $commande->id }}, $event.target.value)"
                                        class="text-xs font-medium px-2 py-1 rounded-lg
                                               border-0 cursor-pointer focus:outline-none
                                               {{ $commande->statut_paiement === 'paye'
                                                  ? 'bg-green-100 text-green-700'
                                                  : ($commande->statut_paiement === 'echoue'
                                                     ? 'bg-red-100 text-red-700'
                                                     : 'bg-yellow-100 text-yellow-700') }}">
                                    <option value="en_attente" {{ $commande->statut_paiement === 'en_attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="paye"       {{ $commande->statut_paiement === 'paye'       ? 'selected' : '' }}>Payé</option>
                                    <option value="echoue"     {{ $commande->statut_paiement === 'echoue'     ? 'selected' : '' }}>Échoué</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <p class="text-xs text-gray-600">
                                    {{ $commande->adresse_livraison
                                       ? '🚚 Livraison'
                                       : '🏪 Retrait' }}
                                </p>
                                @if($commande->ville)
                                    <p class="text-xs text-gray-400">
                                        {{ $commande->ville }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <select wire:change="updateStatus({{ $commande->id }}, $event.target.value)"
                                        class="text-xs font-medium px-2 py-1 rounded-lg
                                               border-0 cursor-pointer focus:outline-none
                                               {{ match($commande->statut) {
                                                    'en_attente' => 'bg-yellow-100 text-yellow-700',
                                                    'confirmee' => 'bg-blue-100 text-blue-700',
                                                    'en_preparation' => 'bg-indigo-100 text-indigo-700',
                                                    'expediee' => 'bg-purple-100 text-purple-700',
                                                    'livree' => 'bg-green-100 text-green-700',
                                                    'annulee' => 'bg-red-100 text-red-700',
                                                    default => 'bg-gray-100 text-gray-600',
                                               } }}">
                                    @foreach([
                                        'en_attente'     => 'En attente',
                                        'confirmee'      => 'Confirmée',
                                        'en_preparation' => 'En préparation',
                                        'expediee'       => 'Expédiée',
                                        'livree'         => 'Livrée',
                                        'annulee'        => 'Annulée',
                                    ] as $val => $lbl)
                                        <option value="{{ $val }}"
                                                {{ $commande->statut === $val
                                                   ? 'selected' : '' }}>
                                            {{ $lbl }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="text-sm font-bold text-dark">
                                    {{ number_format($commande->total, 0, ',', ' ') }}
                                    <span class="text-xs font-normal text-gray-400">
                                        FCFA
                                    </span>
                                </p>
                            </td>
                            <td class="px-4 py-3 text-right sticky right-0 bg-white border-l border-gray-100">
                                <button wire:click="view({{ $commande->id }})"
                                        class="w-8 h-8 flex items-center justify-center
                                               rounded-lg text-gray-400 hover:text-primary
                                               hover:bg-primary/10 transition-colors ml-auto">
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8"
                                class="px-4 py-12 text-center text-sm text-gray-400">
                                Aucune commande trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($commandes->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $commandes->links() }}
            </div>
        @endif
    </div>

    {{-- Modal détail commande --}}
    @if($voir)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="$set('voirId', null)"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg
                        max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4
                            border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-bold text-dark">
                            {{ $voir->reference }}
                        </h3>
                        <p class="text-xs text-gray-400">
                            {{ $voir->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                    <button wire:click="$set('voirId', null)"
                            class="w-8 h-8 flex items-center justify-center
                                   rounded-lg text-gray-400 hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-4 space-y-5">
                    {{-- Client --}}
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase
                                  tracking-wider mb-2">Client</p>
                        <p class="text-sm font-semibold text-dark">
                            {{ $voir->nom_client }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ $voir->telephone_client }}
                        </p>
                        @if($voir->email_client)
                            <p class="text-sm text-gray-500">
                                {{ $voir->email_client }}
                            </p>
                        @endif
                    </div>

                    {{-- Livraison --}}
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase
                                  tracking-wider mb-2">Livraison</p>
                        <p class="text-sm text-dark">
                            {{ $voir->adresse_livraison
                               ? '🚚 Livraison à domicile'
                               : '🏪 Retrait en boutique' }}
                        </p>
                        @if($voir->adresse_livraison)
                            <p class="text-sm text-gray-500">
                                {{ $voir->adresse_livraison }},
                                {{ $voir->ville }}
                            </p>
                        @endif
                    </div>

                    {{-- Articles --}}
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase
                                  tracking-wider mb-2">Articles</p>
                        <div class="space-y-2">
                            @foreach($voir->lignes as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">
                                        {{ $item->nom_plat }}
                                        <span class="text-gray-400">
                                            x{{ $item->quantite }}
                                        </span>
                                    </span>
                                    <span class="font-medium text-dark">
                                        {{ number_format($item->total, 0, ',', ' ') }}
                                        FCFA
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Sous-total</span>
                            <span>
                                {{ number_format($voir->sous_total, 0, ',', ' ') }}
                                FCFA
                            </span>
                        </div>
                        @if($voir->frais_livraison > 0)
                            <div class="flex justify-between text-sm text-gray-500">
                                <span>Livraison</span>
                                <span>
                                    {{ number_format($voir->frais_livraison, 0, ',', ' ') }}
                                    FCFA
                                </span>
                            </div>
                        @endif
                        <div class="flex justify-between font-bold text-dark
                                    border-t border-gray-200 pt-2">
                            <span>Total</span>
                            <span class="text-primary">
                                {{ number_format($voir->total, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                    </div>

                    {{-- Notes --}}
                    @if($voir->notes)
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase
                                      tracking-wider mb-2">Notes</p>
                            <p class="text-sm text-gray-600 bg-yellow-50 rounded-xl p-3">
                                {{ $voir->notes }}
                            </p>
                        </div>
                    @endif

                    {{-- Actions vente / facture --}}
                    <div class="border-t border-gray-100 pt-4 space-y-3">

                        @if($voir->vente_id)
                            {{-- Vente déjà créée --}}
                            <div class="bg-green-50 border border-green-200 rounded-xl
                                        p-3 text-xs text-green-700 font-medium">
                                ✅ Convertie en vente {{ $voir->vente->reference }}
                            </div>
                            @if($voir->vente->facture)
                                <a href="{{ route('factures.download', $voir->vente) }}"
                                   target="_blank"
                                   class="flex items-center justify-center gap-2 w-full
                                          py-2.5 bg-primary text-white text-sm font-semibold
                                          rounded-xl hover:bg-primary/90 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Télécharger la facture
                                </a>
                            @endif

                        @elseif($voir->statut === 'livree' && $voir->statut_paiement === 'paye')
                            {{-- Éligible à la conversion --}}
                            <div class="bg-blue-50 border border-blue-200 rounded-xl
                                        p-3 text-xs text-blue-700">
                                🎯 Commande livrée et payée — prête à être convertie en vente.
                            </div>
                            <button wire:click="convertToSale({{ $voir->id }})"
                                    wire:loading.attr="disabled"
                                    class="flex items-center justify-center gap-2 w-full
                                           py-2.5 bg-green-600 text-white text-sm font-semibold
                                           rounded-xl hover:bg-green-500 transition-all
                                           disabled:opacity-60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
                                </svg>
                                <span wire:loading.remove wire:target="convertToSale">
                                    Convertir en vente & générer facture
                                </span>
                                <span wire:loading wire:target="convertToSale">
                                    Conversion en cours...
                                </span>
                            </button>

                        @else
                            <p class="text-xs text-gray-400 text-center">
                                La facture sera générée automatiquement quand la commande
                                sera <strong>livrée</strong> et <strong>payée</strong>.
                            </p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    @endif
</div>