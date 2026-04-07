<div class="max-w-6xl mx-auto">
    @if(session('form_error'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200
                    text-red-700 px-4 py-3 rounded-xl text-sm font-medium">
            {{ session('form_error') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Colonne principale --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Recherche produits --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-primary/10 rounded-lg flex items-center
                                     justify-center text-primary text-xs">1</span>
                        Produits commandés
                    </h3>

                        {{-- Barre de recherche --}}
                    <div class="relative mb-4">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                        <input wire:model.live.debounce.250ms="searchProduct"
                               type="text"
                               placeholder="Filtrer les produits par nom ou SKU..."
                               class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl
                                      text-sm focus:outline-none focus:border-primary bg-gray-50"/>
                        @if($searchProduct)
                            <button type="button" wire:click="$set('searchProduct', '')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400
                                           hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        @endif
                    </div>

                    {{-- Grille de produits --}}
                    @php $selectedIds = array_column($items, 'product_id'); @endphp
                    @if(count($products) > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2 mb-5 max-h-72 overflow-y-auto pr-1">
                            @foreach($products as $product)
                                @php $isSelected = in_array($product['id'], $selectedIds); @endphp
                                <button type="button"
                                        wire:click="toggleItem({{ $product['id'] }})"
                                        @class([
                                            'rounded-xl border text-left transition-all duration-150
                                             overflow-hidden group hover:-translate-y-0.5',
                                            'bg-primary/5 border-primary ring-2 ring-primary/20 shadow-sm shadow-primary/10'
                                                => $isSelected,
                                            'bg-white border-gray-100 hover:shadow-md hover:border-primary/30'
                                                => !$isSelected,
                                        ])>
                                    {{-- Image --}}
                                    <div @class([
                                            'aspect-square flex items-center justify-center
                                             p-2 relative overflow-hidden',
                                            'bg-primary/10' => $isSelected,
                                            'bg-gray-50'    => !$isSelected,
                                        ])>
                                        @if($product['image'])
                                            <img src="{{ Storage::url($product['image']) }}"
                                                 class="w-full h-full object-contain
                                                        group-hover:scale-105 transition-transform duration-300"/>
                                        @else
                                            <svg class="w-8 h-8 text-gray-300" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="1.5"
                                                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                            </svg>
                                        @endif

                                        {{-- Checkmark si sélectionné --}}
                                        @if($isSelected)
                                            <div class="absolute top-1 left-1 w-5 h-5
                                                        bg-primary rounded-full flex items-center
                                                        justify-center shadow">
                                                <svg class="w-3 h-3 text-white" fill="none"
                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                        @endif

                                        {{-- Badge stock --}}
                                        <div @class([
                                                'absolute top-1 right-1 text-white text-xs font-bold
                                                 px-1.5 py-0.5 rounded-md',
                                                'bg-red-500'    => $product['stock_quantity'] <= 0,
                                                'bg-orange-400' => $product['stock_quantity'] > 0 && $product['stock_quantity'] <= 3,
                                                'hidden'        => $product['stock_quantity'] > 3,
                                            ])>
                                            {{ $product['stock_quantity'] <= 0 ? '0' : $product['stock_quantity'] }}
                                        </div>
                                    </div>

                                    {{-- Infos --}}
                                    <div class="p-2">
                                        @if($product['category'])
                                            <p class="text-xs truncate mb-0.5
                                                       {{ $isSelected ? 'text-primary/70' : 'text-gray-400' }}">
                                                {{ $product['category'] }}
                                            </p>
                                        @endif
                                        <p class="text-xs font-semibold truncate
                                                   {{ $isSelected ? 'text-primary' : 'text-dark group-hover:text-primary' }}
                                                   transition-colors">
                                            {{ $product['name'] }}
                                        </p>
                                        <p class="text-xs font-bold mt-0.5
                                                   {{ $isSelected ? 'text-primary' : 'text-gray-500' }}">
                                            {{ number_format($product['purchase_price'], 0, ',', ' ') }}
                                            <span class="font-normal text-gray-400">FCFA</span>
                                        </p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-6 text-gray-400 mb-4">
                            <svg class="w-10 h-10 mb-2 text-gray-300" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                            </svg>
                            <p class="text-sm">Aucun produit trouvé</p>
                        </div>
                    @endif

                    {{-- Séparateur --}}
                    <div class="border-t border-gray-100 pt-4 mb-3">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-bold text-dark uppercase tracking-wide">
                                Produits sélectionnés
                                <span class="font-normal text-gray-400 normal-case tracking-normal ml-1">
                                    ({{ count($items) }})
                                </span>
                            </p>
                            @error('items')
                                <p class="text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Liste des items sélectionnés --}}
                    @if(count($items) > 0)
                        <div class="space-y-2">
                            @foreach($items as $index => $item)
                                <div class="bg-gray-50 rounded-xl p-3 flex items-center gap-3">

                                    {{-- Nom --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-dark truncate">
                                            {{ $item['product_name'] }}
                                        </p>
                                        <p class="text-xs text-primary font-medium">
                                            Total : {{ number_format($item['total_price'], 0, ',', ' ') }} FCFA
                                        </p>
                                    </div>

                                    {{-- Quantité --}}
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <button type="button"
                                                wire:click="updateItem({{ $index }}, 'quantity', {{ $item['quantity'] - 1 }})"
                                                class="w-7 h-7 flex items-center justify-center bg-white
                                                       rounded-lg border border-gray-200 text-gray-500
                                                       hover:bg-gray-100 text-sm font-bold">−</button>
                                        <input type="number"
                                               wire:change="updateItem({{ $index }}, 'quantity', $event.target.value)"
                                               value="{{ $item['quantity'] }}"
                                               min="1"
                                               class="w-11 text-center text-sm font-bold border border-gray-200
                                                      rounded-lg py-1 focus:outline-none focus:border-primary bg-white"/>
                                        <button type="button"
                                                wire:click="updateItem({{ $index }}, 'quantity', {{ $item['quantity'] + 1 }})"
                                                class="w-7 h-7 flex items-center justify-center bg-white
                                                       rounded-lg border border-gray-200 text-gray-500
                                                       hover:bg-gray-100 text-sm font-bold">+</button>
                                    </div>

                                    {{-- Prix unitaire --}}
                                    <div class="relative w-32 flex-shrink-0">
                                        <input type="number"
                                               wire:change="updateItem({{ $index }}, 'unit_price', $event.target.value)"
                                               value="{{ $item['unit_price'] }}"
                                               min="0" step="100"
                                               placeholder="Prix"
                                               class="w-full text-right text-sm font-medium border border-gray-200
                                                      rounded-lg px-2 pr-10 py-1.5 focus:outline-none
                                                      focus:border-primary bg-white"/>
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2
                                                     text-xs text-gray-400">F</span>
                                    </div>

                                    {{-- Supprimer --}}
                                    <button type="button"
                                            wire:click="removeItem({{ $index }})"
                                            class="w-7 h-7 flex-shrink-0 flex items-center justify-center
                                                   text-gray-300 hover:text-red-500 hover:bg-red-50
                                                   rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        {{-- Total --}}
                        <div class="mt-4 p-3 bg-primary/5 rounded-xl flex items-center justify-between">
                            <p class="text-sm font-bold text-dark">Total commande</p>
                            <p class="text-lg font-bold text-primary">
                                {{ number_format($totalAmount, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                    @else
                        {{-- Ligne placeholder --}}
                        <div class="border-2 border-dashed border-gray-200 rounded-xl px-4 py-4
                                    flex items-center gap-3 text-gray-400">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center
                                        justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-300" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2" d="M12 4v16M4 12h16"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-400">Aucun produit sélectionné</p>
                                <p class="text-xs text-gray-300 mt-0.5">
                                    Cliquez sur un produit dans la grille pour l'ajouter
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Colonne latérale --}}
            <div class="space-y-6">

                {{-- Infos achat --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-secondary/10 rounded-lg flex items-center
                                     justify-center text-secondary text-xs">2</span>
                        Informations
                    </h3>
                    <div class="space-y-4">

                        {{-- Fournisseur --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5 flex items-center gap-2">
                                Fournisseur
                                <span class="text-xs font-normal text-gray-400">— optionnel</span>
                            </label>
                            <select wire:model="supplier_id"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                           text-sm bg-gray-50 focus:outline-none focus:border-primary
                                           transition-colors">
                                <option value="">Aucun fournisseur</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date achat --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Date d'achat <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="purchase_date" type="date"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary transition-colors"/>
                        </div>

                        {{-- Date livraison prévue --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Livraison prévue
                            </label>
                            <input wire:model="expected_date" type="date"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary transition-colors"/>
                        </div>

                        {{-- Statut --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Statut
                            </label>
                            <select wire:model="status"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                           text-sm bg-gray-50 focus:outline-none
                                           focus:border-primary transition-colors">
                                <option value="received">✅ Reçu</option>
                                <option value="ordered">📦 Commandé</option>
                                <option value="partial">⏳ Partiel</option>
                                <option value="cancelled">❌ Annulé</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Paiement --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-green-100 rounded-lg flex items-center
                                     justify-center text-green-600 text-xs">3</span>
                        Paiement
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Mode de paiement
                            </label>
                            <select wire:model="payment_method"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                           text-sm bg-gray-50 focus:outline-none
                                           focus:border-primary transition-colors">
                                <option value="cash">Cash</option>
                                <option value="wave">Wave</option>
                                <option value="om">Orange Money</option>
                                <option value="virement">🏦 Virement</option>
                                <option value="credit">📝 Crédit</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Montant payé
                            </label>
                            <div class="relative">
                                <input wire:model.live="amount_paid"
                                       type="number" min="0" step="100"
                                       class="w-full px-3 py-2.5 pr-16 border border-gray-200
                                              rounded-xl text-sm bg-gray-50 focus:outline-none
                                              focus:border-primary focus:bg-white transition-colors"/>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2
                                             text-xs text-gray-400">FCFA</span>
                            </div>
                        </div>
                        @if($amountDue > 0)
                            <div class="p-3 bg-red-50 rounded-xl">
                                <p class="text-xs text-red-600">Restant dû</p>
                                <p class="text-lg font-bold text-red-600">
                                    {{ number_format($amountDue, 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                        @elseif($totalAmount > 0)
                            <div class="p-3 bg-green-50 rounded-xl">
                                <p class="text-sm font-medium text-green-600">✅ Entièrement payé</p>
                            </div>
                        @endif

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
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between bg-white rounded-2xl
                    border border-gray-100 shadow-sm px-6 py-4">
            <a href="{{ route('caisse.achats.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-600
                      hover:bg-gray-100 rounded-xl transition-colors">
                ← Retour
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white
                           text-sm font-medium rounded-xl transition-all shadow-lg
                           shadow-primary/25 flex items-center gap-2">
                <svg wire:loading class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                {{ $isEditing ? 'Enregistrer les modifications' : 'Enregistrer l\'achat' }}
            </button>
        </div>
    </form>
</div>