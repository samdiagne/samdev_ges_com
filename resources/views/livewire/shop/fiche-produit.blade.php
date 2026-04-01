<div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">

        {{-- ── Galerie images ── --}}
        <div class="space-y-4">
            {{-- Image principale --}}
            @php
                $images = $product->images ?? collect();
                $allImages = $product->main_image
                    ? collect([$product->main_image])
                        ->concat($images->pluck('image_path'))
                    : $images->pluck('image_path');
            @endphp
            <div x-data="{ zoomed: false }"
                 class="aspect-square bg-white rounded-3xl border border-gray-100
                        shadow-sm overflow-hidden flex items-center justify-center p-8 relative">

                @if($allImages->isNotEmpty())
                    <img src="{{ Storage::url($allImages[$activeImage]) }}"
                         alt="{{ $product->name }}{{ $product->brand ? ' — ' . $product->brand : '' }} — {{ $settings->name }}"
                         loading="eager"
                         @click="zoomed = true"
                         class="w-full h-full object-contain transition-all duration-300 cursor-zoom-in"/>

                    {{-- Flèches carousel --}}
                    @if($allImages->count() > 1)
                        @if($activeImage > 0)
                            <button wire:click="setImage({{ $activeImage - 1 }})"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9
                                           bg-white/90 hover:bg-white rounded-full shadow-md
                                           flex items-center justify-center text-dark text-lg
                                           font-bold transition-all hover:shadow-lg z-10">
                                ‹
                            </button>
                        @endif
                        @if($activeImage < $allImages->count() - 1)
                            <button wire:click="setImage({{ $activeImage + 1 }})"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9
                                           bg-white/90 hover:bg-white rounded-full shadow-md
                                           flex items-center justify-center text-dark text-lg
                                           font-bold transition-all hover:shadow-lg z-10">
                                ›
                            </button>
                        @endif
                    @endif

                    {{-- Indicateur position --}}
                    @if($allImages->count() > 1)
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5">
                            @foreach($allImages as $i => $_)
                                <span @class([
                                    'w-1.5 h-1.5 rounded-full transition-all',
                                    'bg-primary w-4' => $activeImage === $i,
                                    'bg-gray-300'    => $activeImage !== $i,
                                ])></span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Modal zoom --}}
                    <div x-show="zoomed"
                         x-cloak
                         @click="zoomed = false"
                         @keydown.escape.window="zoomed = false"
                         class="fixed inset-0 z-50 bg-black/85 flex items-center
                                justify-center p-4 cursor-zoom-out"
                         x-transition:enter="transition duration-200 ease-out"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition duration-150 ease-in"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95">
                        <img src="{{ Storage::url($allImages[$activeImage]) }}"
                             alt="{{ $product->name }}"
                             class="max-w-full max-h-[90vh] object-contain rounded-2xl shadow-2xl"
                             @click.stop/>
                        <button @click="zoomed = false"
                                class="absolute top-4 right-4 w-10 h-10 bg-white/20
                                       hover:bg-white/40 rounded-full flex items-center
                                       justify-center text-white text-2xl font-light
                                       transition-colors leading-none">
                            ×
                        </button>
                    </div>
                @else
                    <svg class="w-32 h-32 text-gray-200" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="1"
                              d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0
                                 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                @endif
            </div>

            {{-- Miniatures --}}
            @if($allImages->count() > 1)
                <div class="flex gap-3 overflow-x-auto pb-2">
                    @foreach($allImages as $index => $img)
                        <button wire:click="setImage({{ $index }})"
                                @class([
                                    'w-16 h-16 flex-shrink-0 rounded-xl border-2
                                     overflow-hidden bg-white transition-all',
                                    'border-primary shadow-lg shadow-primary/20'
                                        => $activeImage === $index,
                                    'border-gray-100 hover:border-gray-300'
                                        => $activeImage !== $index,
                                ])>
                            <img src="{{ Storage::url($img) }}"
                                 class="w-full h-full object-contain p-1"/>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Infos produit ── --}}
        <div class="space-y-6">

            {{-- Catégorie + titre --}}
            <div>
                <p class="text-sm font-medium text-primary mb-2">
                    {{ $product->category->name ?? '' }}
                </p>
                <h1 class="text-2xl lg:text-3xl font-bold text-dark leading-tight">
                    {{ $product->name }}
                </h1>
                @if($product->brand)
                    <p class="text-gray-400 text-sm mt-2">
                        Par <span class="font-medium text-dark">
                            {{ $product->brand }}
                        </span>
                    </p>
                @endif
            </div>

            {{-- Prix --}}
            <div class="bg-gray-50 rounded-2xl p-5">
                <div class="flex items-center gap-3 flex-wrap">
                    <p class="text-3xl font-bold text-dark">
                        {{ number_format($product->selling_price, 0, ',', ' ') }}
                        <span class="text-lg font-normal text-gray-400">FCFA</span>
                    </p>
                    @if($product->isOnSale())
                        <span class="bg-red-500 text-white text-sm font-bold px-2.5 py-1 rounded-xl">
                            -{{ $product->discountPercent() }}%
                        </span>
                    @endif
                </div>
                @if($product->isOnSale())
                    <p class="text-sm text-gray-400 line-through mt-1">
                        {{ number_format($product->old_price, 0, ',', ' ') }} FCFA
                    </p>
                    <p class="text-xs text-red-600 font-medium mt-0.5">
                        Vous économisez {{ number_format($product->old_price - $product->selling_price, 0, ',', ' ') }} FCFA
                    </p>
                @endif
                {{-- Stock --}}
                <div class="flex items-center gap-2 mt-3">
                    @if($product->stock_quantity <= 0)
                        <span class="inline-flex items-center gap-1.5 text-sm
                                     font-medium text-red-600">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            Rupture de stock
                        </span>
                    @elseif($product->isLowStock())
                        <span class="inline-flex items-center gap-1.5 text-sm
                                     font-medium text-orange-600">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            Stock limité — {{ $product->stock_quantity }} restant(s)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-sm
                                     font-medium text-green-600">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            En stock ({{ $product->stock_quantity }} disponible(s))
                        </span>
                    @endif
                </div>
            </div>

            {{-- Description courte --}}
            @if($product->description)
                <div class="text-gray-600 text-sm leading-relaxed">
                    {!! nl2br(e(Str::limit($product->description, 300))) !!}
                </div>
            @endif

            {{-- Sélecteur quantité + Ajouter panier --}}
            @if($product->stock_quantity > 0)
                <div class="space-y-4">
                    {{-- Quantité --}}
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-dark">Quantité :</span>
                        <div class="flex items-center gap-3 bg-gray-100 rounded-xl p-1">
                            <button wire:click="decrementQty"
                                    @class([
                                        'w-8 h-8 rounded-lg flex items-center
                                         justify-center transition-colors font-bold',
                                        'bg-white shadow text-dark' => $quantity > 1,
                                        'text-gray-300 cursor-not-allowed' => $quantity <= 1,
                                    ])>
                                −
                            </button>
                            <span class="w-8 text-center font-bold text-dark">
                                {{ $quantity }}
                            </span>
                            <button wire:click="incrementQty"
                                    @class([
                                        'w-8 h-8 rounded-lg flex items-center
                                         justify-center transition-colors font-bold',
                                        'bg-white shadow text-dark'
                                            => $quantity < $product->stock_quantity,
                                        'text-gray-300 cursor-not-allowed'
                                            => $quantity >= $product->stock_quantity,
                                    ])>
                                +
                            </button>
                        </div>
                    </div>

                    {{-- Boutons action --}}
                    <div class="flex gap-3">
                        <button wire:click="addToCart"
                                @class([
                                    'flex-1 py-3.5 rounded-2xl font-semibold text-sm
                                     transition-all flex items-center justify-center gap-2',
                                    'bg-green-500 text-white' => $added,
                                    'bg-primary hover:bg-primary/90 text-white
                                     shadow-lg shadow-primary/25' => !$added,
                                ])>
                            @if($added)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Ajouté !
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7
                                             13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                                             1.707.707 1.707H17m0 0a2 2 0 100 4 2 2
                                             0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Ajouter au panier
                            @endif
                        </button>

                        <button onclick="Livewire.dispatch('open-cart')"
                                class="px-5 py-3.5 rounded-2xl font-semibold text-sm
                                       border-2 border-gray-200 text-gray-600
                                       hover:border-primary hover:text-primary
                                       transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                                         1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Panier
                        </button>
                    </div>

                    {{-- Sous-total --}}
                    @if($quantity > 1)
                        <p class="text-sm text-gray-500 text-center">
                            Sous-total :
                            <span class="font-bold text-dark">
                                {{ number_format($product->selling_price * $quantity, 0, ',', ' ') }}
                                FCFA
                            </span>
                        </p>
                    @endif
                </div>
            @else
                {{-- Rupture --}}
                <div class="bg-red-50 border border-red-100 rounded-2xl p-4 text-center">
                    <p class="text-red-600 font-medium text-sm mb-3">
                        Ce produit est actuellement indisponible
                    </p>
                    @php $settings = App\Models\CompanySetting::getSettings(); @endphp
                    @if($settings->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->whatsapp) }}?text=Bonjour, je suis intéressé par {{ urlencode($product->name) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 bg-green-500 text-white
                                  px-4 py-2 rounded-xl text-sm font-medium">
                            Me notifier sur WhatsApp
                        </a>
                    @endif
                </div>
            @endif

            {{-- Infos garantie --}}
            <div class="grid grid-cols-3 gap-3">
                @foreach([
                    ['icon' => '🚚', 'text' => 'Livraison rapide'],
                    ['icon' => '✅', 'text' => 'Qualité garantie'],
                    ['icon' => '🔒', 'text' => 'Paiement sécurisé'],
                ] as $badge)
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <p class="text-xl mb-1">{{ $badge['icon'] }}</p>
                        <p class="text-xs font-medium text-gray-600">{{ $badge['text'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Specs SKU --}}
            @if($product->sku)
                <p class="text-xs text-gray-400">
                    Référence : <span class="font-mono">{{ $product->sku }}</span>
                </p>
            @endif
        </div>
    </div>

    {{-- ── Description complète ── --}}
    @if($product->description)
        <div class="mt-12 bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
            <h2 class="text-lg font-bold text-dark mb-4">Description</h2>
            <div class="text-gray-600 text-sm leading-relaxed prose max-w-none">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>
    @endif

    {{-- Reset added state --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('reset-added', () => {
                setTimeout(() => {
                    @this.set('added', false);
                }, 2000);
            });
        });
    </script>
</div>