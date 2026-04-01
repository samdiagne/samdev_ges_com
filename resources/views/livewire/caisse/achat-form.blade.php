<div class="max-w-4xl mx-auto">
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

                    {{-- Recherche --}}
                    <div class="relative mb-4">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                        <input wire:model.live.debounce.200ms="searchProduct"
                               type="text"
                               placeholder="Rechercher un produit à ajouter..."
                               class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl
                                      text-sm focus:outline-none focus:border-primary bg-gray-50"/>

                        @if(count($searchResults) > 0)
                            <div class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl
                                        shadow-xl border border-gray-100 z-30 overflow-hidden">
                                @foreach($searchResults as $result)
                                    <button type="button"
                                            wire:click="addItem({{ $result['id'] }})"
                                            class="w-full flex items-center justify-between
                                                   px-4 py-3 hover:bg-primary/5 transition-colors
                                                   text-left border-b border-gray-50 last:border-0">
                                        <div>
                                            <p class="text-sm font-semibold text-dark">
                                                {{ $result['name'] }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ $result['sku'] ?? '—' }}
                                                · Stock: {{ $result['stock_quantity'] }}
                                            </p>
                                        </div>
                                        <p class="text-sm font-bold text-primary">
                                            {{ number_format($result['purchase_price'], 0, ',', ' ') }}
                                            FCFA
                                        </p>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @error('items')
                        <p class="text-red-500 text-xs mb-3">{{ $message }}</p>
                    @enderror

                    {{-- Liste des items --}}
                    @if(count($items) > 0)
                        <div class="space-y-2">
                            @foreach($items as $index => $item)
                                <div class="bg-gray-50 rounded-xl p-3 space-y-2">

                                    {{-- Ligne 1 : Nom + Supprimer --}}
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-dark truncate">
                                                {{ $item['product_name'] }}
                                            </p>
                                            <p class="text-xs text-primary font-medium mt-0.5">
                                                Total : {{ number_format($item['total_price'], 0, ',', ' ') }} FCFA
                                            </p>
                                        </div>
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

                                    {{-- Ligne 2 : Qté + Prix unit --}}
                                    <div class="flex items-center gap-3">

                                        {{-- Quantité --}}
                                        <div class="flex items-center gap-1">
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
                                        <div class="flex-1 relative">
                                            <input type="number"
                                                   wire:change="updateItem({{ $index }}, 'unit_price', $event.target.value)"
                                                   value="{{ $item['unit_price'] }}"
                                                   min="0" step="100"
                                                   placeholder="Prix unit."
                                                   class="w-full text-right text-sm font-medium border border-gray-200
                                                          rounded-lg px-2 pr-12 py-1.5 focus:outline-none
                                                          focus:border-primary bg-white"/>
                                            <span class="absolute right-2 top-1/2 -translate-y-1/2
                                                         text-xs text-gray-400">FCFA</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Total --}}
                        <div class="mt-4 p-3 bg-primary/5 rounded-xl flex items-center
                                    justify-between">
                            <p class="text-sm font-bold text-dark">Total commande</p>
                            <p class="text-lg font-bold text-primary">
                                {{ number_format($totalAmount, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400">
                            <p class="text-sm">Aucun produit ajouté</p>
                            <p class="text-xs mt-1">Recherchez des produits ci-dessus</p>
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