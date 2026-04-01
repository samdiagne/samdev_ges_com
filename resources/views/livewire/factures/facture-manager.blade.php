<div>
    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Factures</p>
            <p class="text-2xl font-bold text-dark mt-1">{{ $stats['count'] }}</p>
            <p class="text-xs text-gray-400 mt-1">ce mois</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">CA Facturé</p>
            <p class="text-xl font-bold text-primary mt-1">
                {{ number_format($stats['total'], 0, ',', ' ') }}
                <span class="text-sm font-normal">FCFA</span>
            </p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3">
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
            <input wire:model.live="filterMonth" type="month"
                   class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                          focus:outline-none focus:border-primary bg-gray-50"/>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200
                    text-green-800 px-4 py-3 rounded-xl text-sm font-medium">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">N° Facture</th>
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
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Total</th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 sticky right-0 bg-gray-50 border-l border-gray-100">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($invoices as $invoice)
                        @php
                            $pmLabels = [
                                'cash' => 'Cash',
                                'wave' => 'Wave',
                                'om'   => 'OM',
                                'card' => 'Carte',
                            ];
                            $pmColors = [
                                'cash' => 'bg-green-100 text-green-700',
                                'wave' => 'bg-blue-100 text-blue-700',
                                'om'   => 'bg-orange-100 text-orange-700',
                                'card' => 'bg-purple-100 text-purple-700',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-sm font-bold text-dark">
                                    {{ $invoice->invoice_number }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $invoice->sale->reference }}
                                </p>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <p class="text-sm text-dark">
                                    {{ $invoice->sale->customer?->name ?? 'Client anonyme' }}
                                </p>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <p class="text-sm text-dark">
                                    {{ $invoice->issued_at->format('d/m/Y') }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $invoice->issued_at->format('H:i') }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-center hidden md:table-cell">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                             text-xs font-medium
                                             {{ $pmColors[$invoice->sale->payment_method] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $pmLabels[$invoice->sale->payment_method] ?? $invoice->sale->payment_method }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="text-sm font-bold text-dark">
                                    {{ number_format($invoice->total_ttc, 0, ',', ' ') }}
                                    <span class="text-xs font-normal text-gray-400">FCFA</span>
                                </p>
                            </td>
                            <td class="px-4 py-3 sticky right-0 bg-white border-l border-gray-100">
                                <div class="flex items-center justify-end gap-1">
                                    {{-- Voir --}}
                                    <a href="{{ route('factures.show', $invoice->sale_id) }}"
                                       class="w-8 h-8 flex items-center justify-center rounded-lg
                                              text-gray-400 hover:text-primary hover:bg-primary/10
                                              transition-colors" title="Voir">
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
                                    </a>
                                    {{-- Télécharger --}}
                                    <a href="{{ route('factures.download', $invoice->sale_id) }}"
                                       class="w-8 h-8 flex items-center justify-center rounded-lg
                                              text-gray-400 hover:text-secondary hover:bg-secondary/10
                                              transition-colors" title="Télécharger PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0
                                                     01-2-2V5a2 2 0 012-2h5.586a1 1 0
                                                     01.707.293l5.414 5.414a1 1 0
                                                     01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center
                                                justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2
                                                     0 012-2h5.586a1 1 0 01.707.293l5.414
                                                     5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">
                                        Aucune facture ce mois
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>