<div>
    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Achats</p>
            <p class="text-2xl font-bold text-dark mt-1">{{ $stats['count'] }}</p>
            <p class="text-xs text-gray-400 mt-1">commandes</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total</p>
            <p class="text-xl font-bold text-primary mt-1">
                {{ number_format($stats['total'], 0, ',', ' ') }}
                <span class="text-sm font-normal">FCFA</span>
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Payé</p>
            <p class="text-xl font-bold text-green-600 mt-1">
                {{ number_format($stats['paid'], 0, ',', ' ') }}
                <span class="text-sm font-normal">FCFA</span>
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Restant dû</p>
            <p class="text-xl font-bold text-red-500 mt-1">
                {{ number_format($stats['due'], 0, ',', ' ') }}
                <span class="text-sm font-normal">FCFA</span>
            </p>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-dark">Achats fournisseurs</h3>
            <p class="text-sm text-gray-400">{{ $purchases->total() }} achat(s)</p>
        </div>
        <a href="{{ route('caisse.achats.create') }}"
           class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90
                  text-white font-medium px-4 py-2.5 rounded-xl transition-all
                  shadow-lg shadow-primary/25 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvel achat
        </a>
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
                       placeholder="Référence, fournisseur..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl
                              text-sm focus:outline-none focus:border-primary bg-gray-50"/>
            </div>
            <input wire:model.live="filterMonth" type="month"
                   class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                          focus:outline-none focus:border-primary bg-gray-50"/>
            <select wire:model.live="filterStatus"
                    class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-primary bg-gray-50">
                <option value="">Tous statuts</option>
                <option value="ordered">Commandé</option>
                <option value="received">Reçu</option>
                <option value="partial">Partiel</option>
                <option value="cancelled">Annulé</option>
            </select>
            <select wire:model.live="filterSupplier"
                    class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-primary bg-gray-50">
                <option value="">Tous fournisseurs</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200
                    text-green-800 px-4 py-3 rounded-xl text-sm font-medium">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Référence</th>
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Fournisseur</th>
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 hidden md:table-cell">
                            Date
                        </th>
                        <th class="text-center text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Statut</th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Total</th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 hidden lg:table-cell">
                            Restant dû
                        </th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 sticky right-0 bg-gray-50 border-l border-gray-100">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($purchases as $purchase)
                        @php
                            $statusColors = [
                                'ordered'   => 'bg-blue-100 text-blue-700',
                                'received'  => 'bg-green-100 text-green-700',
                                'partial'   => 'bg-orange-100 text-orange-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                            ];
                            $statusLabels = [
                                'ordered'   => '📦 Commandé',
                                'received'  => '✅ Reçu',
                                'partial'   => '⏳ Partiel',
                                'cancelled' => '❌ Annulé',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-sm font-bold text-dark">
                                    {{ $purchase->reference }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $purchase->items->count() }} produit(s)
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-dark">
                                    {{ $purchase->supplier->name }}
                                </p>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <p class="text-sm text-dark">
                                    {{ $purchase->purchase_date->format('d/m/Y') }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                             text-xs font-medium
                                             {{ $statusColors[$purchase->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $statusLabels[$purchase->status] ?? $purchase->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="text-sm font-bold text-dark">
                                    {{ number_format($purchase->total_amount, 0, ',', ' ') }}
                                    <span class="text-xs font-normal text-gray-400">FCFA</span>
                                </p>
                            </td>
                            <td class="px-4 py-3 text-right hidden lg:table-cell">
                                @if($purchase->amount_due > 0)
                                    <p class="text-sm font-bold text-red-500">
                                        {{ number_format($purchase->amount_due, 0, ',', ' ') }}
                                        <span class="text-xs font-normal">FCFA</span>
                                    </p>
                                @else
                                    <span class="text-xs text-green-600 font-medium">✅ Soldé</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 sticky right-0 bg-white border-l border-gray-100">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="viewPurchase({{ $purchase->id }})"
                                            class="w-8 h-8 flex items-center justify-center
                                                   rounded-lg text-gray-400 hover:text-primary
                                                   hover:bg-primary/10 transition-colors">
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
                                    <a href="{{ route('caisse.achats.edit', $purchase->id) }}"
                                       class="w-8 h-8 flex items-center justify-center
                                              rounded-lg text-gray-400 hover:text-primary
                                              hover:bg-primary/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2
                                                     2 0 002-2v-5m-1.414-9.414a2 2 0 112.828
                                                     2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button wire:click="confirmDelete({{ $purchase->id }})"
                                            class="w-8 h-8 flex items-center justify-center
                                                   rounded-lg text-gray-400 hover:text-red-500
                                                   hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2
                                                     2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1
                                                     1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <p class="text-sm text-gray-400">Aucun achat trouvé</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchases->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>

    {{-- Modal détail --}}
    @if($showDetailModal && $viewingPurchase)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="$set('showDetailModal', false)"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg
                        max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100
                            sticky top-0 bg-white rounded-t-2xl z-10">
                    <div>
                        <h3 class="text-base font-bold text-dark">
                            {{ $viewingPurchase->reference }}
                        </h3>
                        <p class="text-xs text-gray-400">
                            {{ $viewingPurchase->purchase_date->format('d/m/Y') }}
                            · {{ $viewingPurchase->supplier->name }}
                        </p>
                    </div>
                    <button wire:click="$set('showDetailModal', false)"
                            class="w-8 h-8 flex items-center justify-center rounded-lg
                                   text-gray-400 hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Articles --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                            Produits ({{ $viewingPurchase->items->count() }})
                        </p>
                        <div class="space-y-2">
                            @foreach($viewingPurchase->items as $item)
                                <div class="flex items-center justify-between p-3
                                            bg-gray-50 rounded-xl">
                                    <div>
                                        <p class="text-sm font-medium text-dark">
                                            {{ $item->product_name }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ number_format($item->unit_price, 0, ',', ' ') }}
                                            FCFA × {{ $item->quantity }}
                                        </p>
                                    </div>
                                    <p class="text-sm font-bold text-dark">
                                        {{ number_format($item->total_price, 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- Totaux --}}
                    <div class="border-t border-gray-100 pt-4 space-y-2">
                        <div class="flex justify-between font-bold text-dark">
                            <span>Total</span>
                            <span class="text-primary">
                                {{ number_format($viewingPurchase->total_amount, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Montant payé</span>
                            <span>
                                {{ number_format($viewingPurchase->amount_paid, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                        @if($viewingPurchase->amount_due > 0)
                            <div class="flex justify-between text-sm text-red-600 font-medium">
                                <span>Restant dû</span>
                                <span>
                                    {{ number_format($viewingPurchase->amount_due, 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                        @endif
                    </div>
                    @if($viewingPurchase->notes)
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-400 mb-1">Notes</p>
                            <p class="text-sm text-dark">{{ $viewingPurchase->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Modal suppression --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="$set('showDeleteModal', false)"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
                <h3 class="text-base font-bold text-dark mb-2">Supprimer l'achat</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Cette action est irréversible. Le stock ne sera pas ajusté automatiquement.
                </p>
                <div class="flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 py-2 text-sm font-medium text-gray-600
                                   hover:bg-gray-100 rounded-xl transition-colors">
                        Annuler
                    </button>
                    <button wire:click="delete"
                            class="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white
                                   text-sm font-medium rounded-xl transition-colors">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>