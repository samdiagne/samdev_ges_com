<div class="max-w-4xl mx-auto">
    <form class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Colonne principale --}}
            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-secondary/10 rounded-lg flex items-center
                                    justify-center text-secondary text-xs">1</span>
                        Lignes du devis
                    </h3>

                    {{-- Recherche + filtres catégories --}}
                    <div class="space-y-3 mb-4">

                        {{-- Barre de recherche --}}
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                            </svg>
                            <input wire:model.live.debounce.300ms="searchProduct"
                                type="text" placeholder="Rechercher un produit..."
                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl
                                        text-sm focus:outline-none focus:border-primary bg-gray-50"/>
                        </div>

                        {{-- Filtres catégories --}}
                        <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
                            <button type="button"
                                    wire:click="$set('filterCategorie', '')"
                                    @class([
                                        'flex-shrink-0 px-3 py-1.5 rounded-xl text-xs
                                        font-semibold transition-all',
                                        'bg-secondary text-white shadow-lg shadow-secondary/25'
                                            => $filterCategorie === '',
                                        'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                            => $filterCategorie !== '',
                                    ])>
                                Tous
                            </button>
                            @foreach($categories as $cat)
                                <button type="button"
                                        wire:click="$set('filterCategorie', '{{ $cat->id }}')"
                                        @class([
                                            'flex-shrink-0 px-3 py-1.5 rounded-xl text-xs
                                            font-semibold transition-all',
                                            'bg-secondary text-white shadow-lg shadow-secondary/25'
                                                => $filterCategorie == $cat->id,
                                            'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                                => $filterCategorie != $cat->id,
                                        ])>
                                    {{ $cat->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Grille produits --}}
                    @if(count($allProducts) > 0)
                        @php
                            $selectedProductIds = array_filter(array_column($items, 'product_id'));
                        @endphp
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4
                                    max-h-72 overflow-y-auto pr-1">
                            @foreach($allProducts as $product)
                                @php $isSelected = in_array($product['id'], $selectedProductIds); @endphp
                                <button type="button"
                                        wire:click="toggleProduct({{ $product['id'] }})"
                                        @class([
                                            'rounded-xl text-left transition-all duration-150
                                            overflow-hidden group',
                                            'bg-green-50 border-2 border-green-400 ring-1 ring-green-300/40 shadow-sm'
                                                => $isSelected,
                                            'bg-gray-50 border border-gray-100 hover:bg-secondary/5 hover:border-secondary/30'
                                                => !$isSelected,
                                        ])>

                                    {{-- Image --}}
                                    <div class="aspect-video flex items-center
                                                justify-center p-2 relative
                                                {{ $isSelected ? 'bg-green-100/50' : 'bg-white' }}">
                                        @if($product['image'])
                                            <img src="{{ Storage::url($product['image']) }}"
                                                class="w-full h-full object-contain
                                                        group-hover:scale-105 transition-transform"/>
                                        @else
                                            <svg class="w-8 h-8 text-gray-300" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0
                                                        00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        @endif

                                        {{-- Checkmark sélectionné --}}
                                        @if($isSelected)
                                            <div class="absolute top-1 left-1 w-5 h-5
                                                        bg-green-500 rounded-full flex items-center
                                                        justify-center shadow">
                                                <svg class="w-3 h-3 text-white" fill="none"
                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                        @endif

                                        {{-- Badge stock --}}
                                        @if($product['stock_quantity'] <= 3)
                                            <div class="absolute top-1 right-1 bg-orange-500
                                                        text-white text-xs font-bold px-1.5 py-0.5
                                                        rounded-lg leading-none">
                                                {{ $product['stock_quantity'] }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Infos --}}
                                    <div class="p-2.5">
                                        <p class="text-xs truncate
                                                  {{ $isSelected ? 'text-green-600' : 'text-gray-400' }}">
                                            {{ $product['category'] ?? '' }}
                                        </p>
                                        <p class="text-xs font-semibold truncate transition-colors
                                                  {{ $isSelected ? 'text-green-700' : 'text-dark group-hover:text-secondary' }}">
                                            {{ $product['name'] }}
                                        </p>
                                        <p class="text-xs font-bold mt-1
                                                  {{ $isSelected ? 'text-green-600' : 'text-secondary' }}">
                                            {{ number_format($product['selling_price'], 0, ',', ' ') }}
                                            <span class="font-normal text-gray-400">FCFA</span>
                                        </p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-6 text-gray-400
                                    mb-4">
                            <p class="text-sm">Aucun produit trouvé</p>
                        </div>
                    @endif

                    {{-- Bouton ligne libre --}}
                    <button type="button"
                            wire:click="$toggle('showFreeLineForm')"
                            class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium
                                text-secondary border border-secondary/30 rounded-xl
                                hover:bg-secondary/5 transition-colors mb-4">
                        ✏️ Ajouter une ligne libre
                    </button>

                    {{-- Formulaire ligne libre --}}
                    @if($showFreeLineForm)
                        <div class="bg-secondary/5 rounded-xl p-4 mb-4 space-y-3">
                            <p class="text-xs font-semibold text-secondary">Ligne libre</p>
                            <input wire:model="freeLineDescription" type="text"
                                   placeholder="Description du produit/service"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl
                                          text-sm bg-white focus:outline-none focus:border-secondary"/>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="text-xs text-gray-400 mb-1 block">Prix HT</label>
                                    <input wire:model="freeLinePrice" type="number" min="0"
                                           placeholder="0"
                                           class="w-full px-3 py-2 border border-gray-200
                                                  rounded-xl text-sm bg-white focus:outline-none
                                                  focus:border-secondary"/>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-400 mb-1 block">Quantité</label>
                                    <input wire:model="freeLineQty" type="number" min="1"
                                           class="w-full px-3 py-2 border border-gray-200
                                                  rounded-xl text-sm bg-white focus:outline-none
                                                  focus:border-secondary"/>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-400 mb-1 block">TVA</label>
                                    <button type="button"
                                            wire:click="$toggle('freeLineTva')"
                                            @class([
                                                'w-full py-2 rounded-xl text-xs font-medium border-2 transition-all',
                                                'border-primary bg-primary text-white' => $freeLineTva,
                                                'border-gray-200 bg-gray-50 text-gray-600' => !$freeLineTva,
                                            ])>
                                        {{ $freeLineTva ? 'TVA ' . $tvaRate . '%' : 'Sans TVA' }}
                                    </button>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" wire:click="addFreeLine"
                                        class="px-4 py-2 bg-secondary hover:bg-secondary/90
                                               text-white text-xs font-medium rounded-xl
                                               transition-colors">
                                    Ajouter
                                </button>
                                <button type="button"
                                        wire:click="$set('showFreeLineForm', false)"
                                        class="px-4 py-2 text-xs font-medium text-gray-600
                                               hover:bg-gray-100 rounded-xl transition-colors">
                                    Annuler
                                </button>
                            </div>
                        </div>
                    @endif

                    @error('items')
                        <p class="text-red-500 text-xs mb-3">{{ $message }}</p>
                    @enderror

                    {{-- Liste des lignes --}}
                    @if(count($items) > 0)
                        <div class="space-y-2">
                            @foreach($items as $index => $item)
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl
                                            {{ $item['is_free_line'] ? 'border border-secondary/20' : '' }}">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-dark truncate">
                                            {{ $item['description'] }}
                                            @if($item['is_free_line'])
                                                <span class="text-xs text-secondary ml-1">✏️</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ number_format($item['unit_price_ttc'], 0, ',', ' ') }}
                                            FCFA TTC/u
                                            @if($item['tva_rate'] > 0)
                                                · TVA {{ $item['tva_rate'] }}%
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <button type="button"
                                                wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})"
                                                class="w-6 h-6 flex items-center justify-center
                                                       bg-white rounded-lg border border-gray-200
                                                       text-gray-500 hover:bg-gray-100 text-sm">
                                            −
                                        </button>
                                        <span class="w-8 text-center text-sm font-bold">
                                            {{ $item['quantity'] }}
                                        </span>
                                        <button type="button"
                                                wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})"
                                                class="w-6 h-6 flex items-center justify-center
                                                       bg-white rounded-lg border border-gray-200
                                                       text-gray-500 hover:bg-gray-100 text-sm">
                                            +
                                        </button>
                                    </div>
                                    <p class="text-sm font-bold text-dark flex-shrink-0 w-28 text-right">
                                        {{ number_format($item['total_ttc'], 0, ',', ' ') }}
                                        <span class="text-xs font-normal text-gray-400">FCFA</span>
                                    </p>
                                    <button type="button" wire:click="removeItem({{ $index }})"
                                            class="w-7 h-7 flex items-center justify-center
                                                   text-gray-300 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        {{-- Totaux --}}
                        <div class="mt-4 p-4 bg-secondary/5 rounded-xl space-y-2">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Sous-total HT</span>
                                <span>{{ number_format($subtotalHt, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>TVA ({{ $tvaRate }}%)</span>
                                <span>{{ number_format($tvaAmount, 0, ',', ' ') }} FCFA</span>
                            </div>
                            @if($discount > 0)
                                <div class="flex justify-between text-xs text-secondary">
                                    <span>Remise</span>
                                    <span>-{{ number_format($discount, 0, ',', ' ') }} FCFA</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-base font-bold text-dark
                                        border-t border-secondary/20 pt-2">
                                <span>Total</span>
                                <span class="text-secondary">
                                    {{ number_format($totalTtc, 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400">
                            <p class="text-sm">Aucune ligne</p>
                            <p class="text-xs mt-1">Recherchez un produit ou ajoutez une ligne libre</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Colonne latérale --}}
            <div class="space-y-6">

                {{-- Client --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-3 flex items-center gap-2">
                        <span class="w-6 h-6 bg-primary/10 rounded-lg flex items-center
                                     justify-center text-primary text-xs">2</span>
                        Client
                    </h3>

                    @if($customerId && $customerName)
                        <div class="flex items-center justify-between bg-primary/5 rounded-xl px-3 py-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-primary rounded-full flex items-center
                                            justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($customerName, 0, 2)) }}
                                </div>
                                <p class="text-sm font-semibold text-dark">{{ $customerName }}</p>
                            </div>
                            <button type="button" wire:click="clearCustomer"
                                    class="text-gray-400 hover:text-red-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @elseif($useQuickClient)
                        <div class="space-y-2">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-medium text-secondary">✏️ Client rapide</p>
                                <button type="button" wire:click="clearCustomer"
                                        class="text-xs text-gray-400 hover:text-red-500">
                                    Annuler
                                </button>
                            </div>
                            <input wire:model="quickClientName" type="text"
                                   placeholder="Nom du client *"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary transition-colors"/>
                            <input wire:model="quickClientPhone" type="text"
                                   placeholder="Téléphone / WhatsApp"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary transition-colors"/>
                        </div>
                    @else
                        <div class="relative">
                            <input wire:model.live.debounce.300ms="searchCustomer"
                                   type="text" placeholder="Rechercher un client..."
                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary transition-colors"/>
                            @if(count($customerResults) > 0)
                                <div class="absolute top-full left-0 right-0 mt-1 bg-white
                                            rounded-xl shadow-xl border border-gray-100 z-30 overflow-hidden">
                                    @foreach($customerResults as $customer)
                                        <button type="button"
                                                wire:click="selectCustomer({{ $customer['id'] }}, '{{ addslashes($customer['name']) }}')"
                                                class="w-full flex items-center gap-2 px-3 py-2.5
                                                       hover:bg-primary/5 transition-colors text-left
                                                       border-b border-gray-50 last:border-0">
                                            <div class="w-7 h-7 bg-gray-200 rounded-full flex items-center
                                                        justify-center text-xs font-bold text-gray-600">
                                                {{ strtoupper(substr($customer['name'], 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-dark">
                                                    {{ $customer['name'] }}
                                                </p>
                                                <p class="text-xs text-gray-400">{{ $customer['phone'] }}</p>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <button type="button" wire:click="$set('useQuickClient', true)"
                                class="w-full mt-2 py-1.5 text-xs font-medium text-secondary
                                       border border-secondary/30 rounded-xl hover:bg-secondary/5
                                       transition-colors">
                            ✏️ Saisir un nouveau client
                        </button>
                    @endif
                </div>

                {{-- Paramètres devis --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-secondary/10 rounded-lg flex items-center
                                     justify-center text-secondary text-xs">3</span>
                        Paramètres
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Valable jusqu'au
                            </label>
                            <input wire:model="valid_until" type="date"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary transition-colors"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Remise globale
                            </label>
                            <div class="relative">
                                <input wire:model.live="discount" type="number" min="0" step="100"
                                       class="w-full px-3 py-2.5 pr-16 border border-gray-200
                                              rounded-xl text-sm bg-gray-50 focus:outline-none
                                              focus:border-primary transition-colors"/>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2
                                             text-xs text-gray-400">FCFA</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">Notes</label>
                            <textarea wire:model="notes" rows="3"
                                      placeholder="Conditions, remarques..."
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
            <a href="{{ route('devis.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-600
                      hover:bg-gray-100 rounded-xl transition-colors">
                ← Retour
            </a>
            <button type="button" wire:click="save"
                    wire:loading.attr="disabled"
                    class="px-6 py-2.5 bg-secondary hover:bg-secondary/90 text-white
                           text-sm font-medium rounded-xl transition-all shadow-lg
                           shadow-secondary/25 flex items-center gap-2
                           disabled:opacity-60 disabled:cursor-not-allowed">
                <svg wire:loading wire:target="save" class="animate-spin w-4 h-4"
                     fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <svg wire:loading.remove wire:target="save" class="w-4 h-4"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2
                             2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Enregistrer le devis
            </button>
        </div>
    </form>
</div>