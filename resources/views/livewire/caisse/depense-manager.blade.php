<div>
    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">
                Total dépenses
            </p>
            <p class="text-2xl font-bold text-red-500 mt-1">
                {{ number_format($stats['total'], 0, ',', ' ') }}
                <span class="text-sm font-normal">FCFA</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">{{ $stats['count'] }} transaction(s)</p>
        </div>

        {{-- Répartition par catégorie --}}
        <div class="sm:col-span-1 lg:col-span-2 bg-white rounded-2xl border
                    border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-3">
                Répartition
            </p>
            @if($stats['par_categorie']->isEmpty())
                <p class="text-sm text-gray-400">Aucune dépense ce mois</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach($stats['par_categorie'] as $cat)
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl
                                    bg-gray-50 border border-gray-100">
                            <span class="text-sm">
                                @switch($cat->icon)
                                    @case('bolt') ⚡ @break
                                    @case('wifi') 📶 @break
                                    @case('droplet') 💧 @break
                                    @case('building') 🏢 @break
                                    @case('truck') 🚚 @break
                                    @case('users') 👥 @break
                                    @case('package') 📦 @break
                                    @default 💸
                                @endswitch
                            </span>
                            <span class="text-xs font-medium text-dark">{{ $cat->nom }}</span>
                            <span class="text-xs font-bold text-primary">
                                {{ number_format($cat->depenses_sum_montant, 0, ',', ' ') }}
                                FCFA
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <div>
            <h3 class="text-lg font-bold text-dark">Dépenses</h3>
            <p class="text-sm text-gray-400">{{ $depenses->total() }} dépense(s)</p>
        </div>
        <button wire:click="openCreateModal"
                class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90
                       text-white font-medium px-4 py-2.5 rounded-xl transition-all
                       shadow-lg shadow-primary/25 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle dépense
        </button>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Description..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl
                              text-sm focus:outline-none focus:border-primary bg-gray-50"/>
            </div>
            <input wire:model.live="filtreMonth" type="month"
                   class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                          focus:outline-none focus:border-primary bg-gray-50"/>
            <select wire:model.live="filtreCategorie"
                    class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-primary bg-gray-50">
                <option value="">Toutes catégories</option>
                @foreach($depenseCategories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nom }}</option>
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
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 4000)"
             class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200
                    text-red-800 px-4 py-3 rounded-xl text-sm font-medium">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0
                         001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">Description</th>
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 hidden md:table-cell">
                            Catégorie
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
                                   uppercase tracking-wider px-4 py-3">Montant</th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 sticky right-0 bg-gray-50 border-l border-gray-100">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($depenses as $depense)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-sm font-semibold text-dark">
                                    {{ $depense->description }}
                                </p>
                                @if($depense->notes)
                                    <p class="text-xs text-gray-400 truncate max-w-xs">
                                        {{ $depense->notes }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1
                                             rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    @switch($depense->categorie->icon ?? '')
                                        @case('bolt') ⚡ @break
                                        @case('wifi') 📶 @break
                                        @case('droplet') 💧 @break
                                        @case('building') 🏢 @break
                                        @case('truck') 🚚 @break
                                        @case('users') 👥 @break
                                        @case('package') 📦 @break
                                        @default 💸
                                    @endswitch
                                    {{ $depense->categorie->nom ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <p class="text-sm text-dark">
                                    {{ $depense->date_depense->format('d/m/Y') }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    par {{ $depense->user->name }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-center hidden md:table-cell">
                                @php
                                    $pmColors = [
                                        'cash'     => 'bg-green-100 text-green-700',
                                        'wave'     => 'bg-blue-100 text-blue-700',
                                        'om'       => 'bg-orange-100 text-orange-700',
                                        'virement' => 'bg-purple-100 text-purple-700',
                                    ];
                                    $pmLabels = [
                                        'cash'     => 'Cash',
                                        'wave'     => 'Wave',
                                        'om'       => 'OM',
                                        'virement' => 'Virement',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                             text-xs font-medium
                                             {{ $pmColors[$depense->mode_paiement] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $pmLabels[$depense->mode_paiement] ?? $depense->mode_paiement }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="text-sm font-bold text-red-500">
                                    {{ number_format($depense->montant, 0, ',', ' ') }}
                                    <span class="text-xs font-normal text-gray-400">FCFA</span>
                                </p>
                            </td>
                            <td class="px-4 py-3 sticky right-0 bg-white border-l border-gray-100">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEditModal({{ $depense->id }})"
                                            class="w-8 h-8 flex items-center justify-center
                                                   rounded-lg text-gray-400 hover:text-primary
                                                   hover:bg-primary/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2
                                                     0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828
                                                     15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    @if($depense->created_at->diffInHours(now()) <= 24)
                                    <button wire:click="confirmDelete({{ $depense->id }})"
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
                                    @else
                                    <span title="Suppression impossible après 24h"
                                          class="w-8 h-8 flex items-center justify-center
                                                 rounded-lg text-gray-300 cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M12 15v2m0 0v2m0-2h2m-2 0H10m2-6V7a4 4 0
                                                     10-8 0v4H3a1 1 0 00-1 1v7a2 2 0 002 2h12a2 2
                                                     0 002-2v-7a1 1 0 00-1-1h-1z"/>
                                        </svg>
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center
                                                justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2
                                                     0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2
                                                     2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">
                                        Aucune dépense ce mois
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($depenses->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $depenses->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════
         MODAL Créer / Modifier
    ══════════════════════════════════════ --}}
    @if($afficherModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="closeModal"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md
                        max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100
                            sticky top-0 bg-white rounded-t-2xl z-10">
                    <h3 class="text-base font-bold text-dark">
                        {{ $isEditing ? 'Modifier la dépense' : 'Nouvelle dépense' }}
                    </h3>
                    <button wire:click="closeModal"
                            class="w-8 h-8 flex items-center justify-center rounded-lg
                                   text-gray-400 hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="px-6 py-4 space-y-4">

                    {{-- Catégorie --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Catégorie <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="depense_categorie_id"
                                class="w-full px-3 py-2.5 border rounded-xl text-sm
                                       focus:outline-none transition-colors
                                       {{ $errors->has('depense_categorie_id')
                                          ? 'border-red-400 bg-red-50'
                                          : 'border-gray-200 bg-gray-50 focus:border-primary' }}">
                            <option value="0">Sélectionner...</option>
                            @foreach($depenseCategories as $cat)
                                <option value="{{ $cat->id }}">
                                    {{ $cat->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('depense_categorie_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="description" type="text"
                               placeholder="Ex: Facture électricité SENELEC mars 2025"
                               class="w-full px-3 py-2.5 border rounded-xl text-sm
                                      focus:outline-none transition-colors
                                      {{ $errors->has('description')
                                         ? 'border-red-400 bg-red-50'
                                         : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Montant + Date --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Montant <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input wire:model="montant" type="number"
                                       min="1" placeholder="0"
                                       @if($montantVerrouille) readonly @endif
                                       class="w-full px-3 py-2.5 pr-14 border rounded-xl text-sm
                                              focus:outline-none transition-colors
                                              {{ $montantVerrouille
                                                 ? 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed'
                                                 : ($errors->has('montant')
                                                    ? 'border-red-400 bg-red-50'
                                                    : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white') }}"/>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2
                                             text-xs text-gray-400">
                                    @if($montantVerrouille)
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M12 15v2m0 0v2m0-2h2m-2 0H10m2-6V7a4 4 0
                                                     10-8 0v4H3a1 1 0 00-1 1v7a2 2 0 002 2h12a2 2
                                                     0 002-2v-7a1 1 0 00-1-1h-1z"/>
                                        </svg>
                                    @else
                                        FCFA
                                    @endif
                                </span>
                            </div>
                            @if($montantVerrouille)
                                <p class="text-amber-600 text-xs mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58
                                                 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53
                                                 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0
                                                 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0
                                                 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Montant verrouillé après 24h
                                </p>
                            @else
                                @error('montant')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="date_depense" type="date"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary transition-colors"/>
                            @error('date_depense')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Mode paiement --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Mode de paiement
                        </label>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach([
                                ['value' => 'cash',     'label' => 'Cash'],
                                ['value' => 'wave',     'label' => 'Wave'],
                                ['value' => 'om',       'label' => 'OM'],
                                ['value' => 'virement', 'label' => 'Virement'],
                            ] as $method)
                                <button type="button"
                                        wire:click="$set('mode_paiement', '{{ $method['value'] }}')"
                                        @class([
                                            'py-2 px-1 rounded-xl text-xs font-semibold transition-all border-2',
                                            'border-primary bg-primary text-white'
                                                => $mode_paiement === $method['value'],
                                            'border-gray-200 bg-gray-50 text-gray-600 hover:border-primary/50'
                                                => $mode_paiement !== $method['value'],
                                        ])>
                                    {{ $method['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Justificatif --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Justificatif (photo)
                        </label>
                        @if($justificatifActuel && !$justificatif)
                            <div class="mb-2">
                                <img src="{{ Storage::url($justificatifActuel) }}"
                                     class="w-16 h-16 object-cover rounded-lg
                                            border border-gray-200"/>
                            </div>
                        @endif
                        @if($justificatif)
                            <div class="mb-2">
                                <img src="{{ $justificatif->temporaryUrl() }}"
                                     class="w-16 h-16 object-cover rounded-lg
                                            border border-gray-200"/>
                            </div>
                        @endif
                        <input wire:model="receipt" type="file" accept="image/*"
                               class="w-full text-sm text-gray-500 file:mr-3 file:py-2
                                      file:px-3 file:rounded-lg file:border-0 file:text-xs
                                      file:font-medium file:bg-primary/10 file:text-primary
                                      hover:file:bg-primary/20 cursor-pointer"/>
                        @error('receipt')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">Notes</label>
                        <textarea wire:model="notes" rows="2"
                                  placeholder="Informations complémentaires..."
                                  class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                         text-sm bg-gray-50 focus:outline-none
                                         focus:border-primary transition-colors resize-none">
                        </textarea>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-gray-600
                                       hover:bg-gray-100 rounded-xl transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-primary hover:bg-primary/90 text-white
                                       text-sm font-medium rounded-xl transition-all
                                       shadow-lg shadow-primary/25 flex items-center gap-2">
                            <svg wire:loading class="animate-spin w-4 h-4" fill="none"
                                 viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            {{ $isEditing ? 'Enregistrer' : 'Ajouter' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal suppression --}}
    @if($afficherModalSuppression)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="$set('afficherModalSuppression', false)"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
                <h3 class="text-base font-bold text-dark mb-2">Supprimer la dépense</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Cette action est irréversible. Les dépenses de plus de 24h ne peuvent pas être supprimées.
                </p>
                <div class="flex gap-3">
                    <button wire:click="$set('afficherModalSuppression', false)"
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