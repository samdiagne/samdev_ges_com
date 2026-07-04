<div>
    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-dark mt-1">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-400 mt-1">devis ce mois</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Brouillons</p>
            <p class="text-2xl font-bold text-gray-500 mt-1">{{ $stats['draft'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Envoyes</p>
            <p class="text-2xl font-bold text-blue-500 mt-1">{{ $stats['sent'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Acceptes</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['accepted'] }}</p>
            <p class="text-xs text-gray-400 mt-1">
                {{ number_format($stats['ca'], 0, ',', ' ') }} FCFA
            </p>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-dark">Devis</h3>
            <p class="text-sm text-gray-400">{{ $quotes->total() }} devis</p>
        </div>
        <a href="{{ route('devis.create') }}"
           class="inline-flex items-center gap-2 bg-secondary hover:bg-secondary/90
                  text-white font-medium px-4 py-2.5 rounded-xl transition-all
                  shadow-lg shadow-secondary/25 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau devis
        </a>
    </div>

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
                       type="text" placeholder="Reference, client..."
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
                <option value="draft">Brouillon</option>
                <option value="sent">Envoye</option>
                <option value="accepted">Accepte</option>
                <option value="refused">Refuse</option>
                <option value="expired">Expire</option>
            </select>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200
                    text-green-800 px-4 py-3 rounded-xl text-sm font-medium">
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
                                   uppercase tracking-wider px-4 py-3">Reference</th>
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Client</th>
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 hidden lg:table-cell">
                            Date
                        </th>
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 hidden lg:table-cell">
                            Validite
                        </th>
                        <th class="text-center text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Statut</th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Total</th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 sticky right-0 bg-gray-50 border-l border-gray-100">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($quotes as $quote)
                        @php
                            $statusColors = [
                                'draft'    => 'bg-gray-100 text-gray-600',
                                'sent'     => 'bg-blue-100 text-blue-700',
                                'accepted' => 'bg-green-100 text-green-700',
                                'refused'  => 'bg-red-100 text-red-700',
                                'expired'  => 'bg-orange-100 text-orange-700',
                            ];
                            $statusLabels = [
                                'draft'    => 'Brouillon',
                                'sent'     => 'Envoye',
                                'accepted' => 'Accepte',
                                'refused'  => 'Refuse',
                                'expired'  => 'Expire',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-sm font-bold text-dark">{{ $quote->reference }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $quote->items->count() }} ligne(s)
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-dark">{{ $quote->nom_client }}</p>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <p class="text-sm text-dark">
                                    {{ $quote->date_devis->format('d/m/Y') }}
                                </p>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                @if($quote->valide_jusqua)
                                    <p class="text-sm {{ $quote->isExpired() ? 'text-red-500' : 'text-dark' }}">
                                        {{ $quote->valide_jusqua->format('d/m/Y') }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400">&mdash;</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                             text-xs font-medium
                                             {{ $statusColors[$quote->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $statusLabels[$quote->status] ?? $quote->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="text-sm font-bold text-dark">
                                    {{ number_format($quote->total, 0, ',', ' ') }}
                                    <span class="text-xs font-normal text-gray-400">FCFA</span>
                                </p>
                            </td>
                            <td class="px-4 py-3 sticky right-0 bg-white border-l border-gray-100">
                                @php
                                    $clientPhone = $quote->client?->telephone ?? $quote->client_telephone ?? '';
                                    $waPhone = preg_replace('/[^0-9]/', '', $clientPhone);
                                    if ($waPhone && !str_starts_with($waPhone, '221')) {
                                        $waPhone = '221' . ltrim($waPhone, '0');
                                    }
                                    $clientEmail = $quote->client?->email ?? '';
                                    $waText = urlencode(
                                        "Bonjour " . $quote->nom_client . ",\n\n" .
                                        "Veuillez trouver ci-joint votre devis *" . $quote->reference . "* " .
                                        "d'un montant de *" . number_format($quote->total, 0, ',', ' ') . " FCFA*" .
                                        ($quote->valide_jusqua ? ", valable jusqu'au " . $quote->valide_jusqua->format('d/m/Y') : '') .
                                        ".\n\nMerci pour votre confiance."
                                    );
                                    $mailSubject = urlencode("Devis " . $quote->reference);
                                    $mailBody = urlencode(
                                        "Bonjour " . $quote->nom_client . ",\n\n" .
                                        "Veuillez trouver ci-joint votre devis " . $quote->reference .
                                        " d'un montant de " . number_format($quote->total, 0, ',', ' ') . " FCFA" .
                                        ($quote->valide_jusqua ? ", valable jusqu'au " . $quote->valide_jusqua->format('d/m/Y') : '') .
                                        ".\n\nCordialement."
                                    );
                                @endphp
                                <div class="flex items-center justify-end gap-1">

                                    {{-- Telecharger PDF --}}
                                    <a href="{{ route('devis.download', $quote->id) }}"
                                       target="_blank"
                                       class="w-8 h-8 flex items-center justify-center rounded-lg
                                              text-gray-400 hover:text-primary hover:bg-primary/10
                                              transition-colors" title="Telecharger PDF">
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

                                    {{-- Envoyer WhatsApp --}}
                                    @if($waPhone)
                                        <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}"
                                           target="_blank"
                                           wire:click="markAsSent({{ $quote->id }})"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg
                                                  text-gray-400 hover:text-green-600 hover:bg-green-50
                                                  transition-colors" title="Envoyer par WhatsApp">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                            </svg>
                                        </a>
                                    @endif

                                    {{-- Envoyer par email --}}
                                    @if($clientEmail)
                                        <a href="mailto:{{ $clientEmail }}?subject={{ $mailSubject }}&body={{ $mailBody }}"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg
                                                  text-gray-400 hover:text-blue-600 hover:bg-blue-50
                                                  transition-colors" title="Envoyer par email">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2
                                                         2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2
                                                         2 0 002 2z"/>
                                            </svg>
                                        </a>
                                    @endif

                                    {{-- Modifier --}}
                                    @if(!in_array($quote->status, ['accepted', 'refused']))
                                        <a href="{{ route('devis.edit', $quote->id) }}"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg
                                                  text-gray-400 hover:text-primary hover:bg-primary/10
                                                  transition-colors" title="Modifier">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2
                                                         0 002-2v-5m-1.414-9.414a2 2 0 112.828
                                                         2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        {{-- Convertir en vente --}}
                                        <button wire:click="openConvertModal({{ $quote->id }})"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg
                                                       text-gray-400 hover:text-green-600
                                                       hover:bg-green-50 transition-colors"
                                                title="Convertir en vente">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- Supprimer --}}
                                    @if($quote->status === 'draft')
                                        <button wire:click="confirmDelete({{ $quote->id }})"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg
                                                       text-gray-400 hover:text-red-500
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
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <p class="text-sm text-gray-400">Aucun devis trouve</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($quotes->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $quotes->links() }}</div>
        @endif
    </div>

    {{-- Modal Convertir en vente --}}
    @if($showConvertModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="$set('showConvertModal', false)"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6
                        max-h-[90vh] overflow-y-auto">
                <h3 class="text-base font-bold text-dark mb-2">
                    Convertir en vente
                </h3>
                <p class="text-sm text-gray-500 mb-4">
                    Le devis sera marque comme accepte et une vente sera creee.
                </p>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Mode de paiement
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach([
                                ['value' => 'cash', 'label' => 'Cash'],
                                ['value' => 'wave', 'label' => 'Wave'],
                                ['value' => 'om',   'label' => 'OM'],
                                ['value' => 'card', 'label' => 'Carte bancaire'],
                            ] as $method)
                                <button type="button"
                                        wire:click="$set('paymentMethod', '{{ $method['value'] }}')"
                                        @class([
                                            'py-2 px-3 rounded-xl text-xs font-semibold
                                             transition-all border-2',
                                            'border-primary bg-primary text-white'
                                                => $paymentMethod === $method['value'],
                                            'border-gray-200 bg-gray-50 text-gray-600'
                                                => $paymentMethod !== $method['value'],
                                        ])>
                                    {{ $method['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Montant recu
                        </label>
                        <div class="relative">
                            <input wire:model.live="amountPaid"
                                   type="number" min="0" step="100"
                                   class="w-full px-3 py-2.5 pr-16 border border-gray-200
                                          rounded-xl text-sm focus:outline-none
                                          focus:border-primary"/>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2
                                         text-xs text-gray-400">FCFA</span>
                        </div>
                        @error('amountPaid')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button wire:click="$set('showConvertModal', false)"
                            class="flex-1 py-2 text-sm font-medium text-gray-600
                                   hover:bg-gray-100 rounded-xl transition-colors">
                        Annuler
                    </button>
                    <button wire:click="convertToSale"
                            class="flex-1 py-2 bg-green-600 hover:bg-green-700 text-white
                                   text-sm font-medium rounded-xl transition-colors">
                        Confirmer la vente
                    </button>
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
                <h3 class="text-base font-bold text-dark mb-2">Supprimer le devis</h3>
                <p class="text-sm text-gray-500 mb-6">Cette action est irreversible.</p>
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
