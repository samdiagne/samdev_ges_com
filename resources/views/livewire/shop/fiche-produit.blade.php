<div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">

        {{-- ── Galerie images ── --}}
        <div class="space-y-4">
            @php
                $images = $product->images ?? collect();
                $allImages = $product->main_image
                    ? collect([$product->main_image])->concat($images->pluck('image_path'))
                    : $images->pluck('image_path');
            @endphp
            <div x-data="{ zoomed: false }"
                 class="aspect-square bg-white rounded-3xl border border-gray-100
                        shadow-sm overflow-hidden flex items-center justify-center p-8 relative">

                @if($allImages->isNotEmpty())
                    <img src="{{ Storage::url($allImages[$activeImage]) }}"
                         alt="{{ $product->name }}{{ $product->brand ? ' — ' . $product->brand : '' }}"
                         loading="eager"
                         @click="zoomed = true"
                         class="w-full h-full object-contain transition-all duration-300 cursor-zoom-in"/>

                    @if($allImages->count() > 1)
                        @if($activeImage > 0)
                            <button wire:click="setImage({{ $activeImage - 1 }})"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9
                                           bg-white/90 hover:bg-white rounded-full shadow-md
                                           flex items-center justify-center text-dark text-lg
                                           font-bold transition-all hover:shadow-lg z-10">‹</button>
                        @endif
                        @if($activeImage < $allImages->count() - 1)
                            <button wire:click="setImage({{ $activeImage + 1 }})"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9
                                           bg-white/90 hover:bg-white rounded-full shadow-md
                                           flex items-center justify-center text-dark text-lg
                                           font-bold transition-all hover:shadow-lg z-10">›</button>
                        @endif
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5">
                            @foreach($allImages as $i => $_)
                                <span @class([
                                    'h-1.5 rounded-full transition-all',
                                    'bg-primary w-4' => $activeImage === $i,
                                    'bg-gray-300 w-1.5' => $activeImage !== $i,
                                ])></span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Badge promo --}}
                    @if($product->isOnSale())
                        <div class="absolute top-4 left-4 bg-red-500 text-white text-sm
                                    font-bold px-3 py-1.5 rounded-xl shadow-lg">
                            -{{ $product->discountPercent() }}%
                        </div>
                    @endif

                    {{-- Modal zoom --}}
                    <div x-show="zoomed" x-cloak @click="zoomed = false"
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
                                       transition-colors leading-none">×</button>
                    </div>
                @else
                    <svg class="w-32 h-32 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
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
                                    'border-primary shadow-lg shadow-primary/20' => $activeImage === $index,
                                    'border-gray-100 hover:border-gray-300' => $activeImage !== $index,
                                ])>
                            <img src="{{ Storage::url($img) }}" class="w-full h-full object-contain p-1"/>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Infos produit ── --}}
        <div class="space-y-5">

            {{-- Catégorie + titre + badges --}}
            <div>
                @if($product->category)
                    <p class="text-xs font-semibold text-primary uppercase tracking-widest mb-2">
                        {{ $product->category->name }}
                    </p>
                @endif
                <h1 class="text-2xl lg:text-3xl font-bold text-dark leading-tight mb-3">
                    {{ $product->name }}
                </h1>

                {{-- Badges marque / modèle / SKU --}}
                <div class="flex flex-wrap items-center gap-2">
                    @if($product->brand)
                        <span class="inline-flex items-center gap-1.5 bg-primary/8 text-primary
                                     text-xs font-semibold px-3 py-1.5 rounded-full border border-primary/15">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            {{ $product->brand }}
                        </span>
                    @endif
                    @if($product->model)
                        <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600
                                     text-xs font-medium px-3 py-1.5 rounded-full">
                            {{ $product->model }}
                        </span>
                    @endif
                    @if($product->sku)
                        <span class="inline-flex items-center gap-1 text-gray-400
                                     text-xs font-mono px-2 py-1">
                            Réf: {{ $product->sku }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Prix --}}
            <div class="bg-gray-50 rounded-2xl p-5">
                <div class="flex items-center gap-3 flex-wrap mb-3">
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
                    <p class="text-sm text-gray-400 line-through">
                        {{ number_format($product->old_price, 0, ',', ' ') }} FCFA
                    </p>
                    <p class="text-xs text-red-600 font-medium mt-0.5">
                        Vous économisez {{ number_format($product->old_price - $product->selling_price, 0, ',', ' ') }} FCFA
                    </p>
                @endif
                {{-- Stock --}}
                <div class="flex items-center gap-2 mt-2">
                    @if($product->stock_quantity <= 0)
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-red-600">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            Rupture de stock
                        </span>
                    @elseif($product->isLowStock())
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-orange-600">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            Stock limité — {{ $product->stock_quantity }} restant(s)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-green-600">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            En stock
                        </span>
                    @endif
                </div>
            </div>

            {{-- Highlights specs (3 premières) --}}
            @php $specs = $product->specifications ?? []; @endphp
            @if(count($specs) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach(array_slice($specs, 0, 6) as $spec)
                        <div class="bg-white border border-gray-100 rounded-xl px-3 py-2.5 text-center">
                            <p class="text-xs text-gray-400 mb-0.5">{{ $spec['label'] }}</p>
                            <p class="text-xs font-bold text-dark leading-tight">{{ $spec['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Description courte --}}
            @if($product->short_description)
                <p class="text-gray-600 text-sm leading-relaxed">
                    {{ $product->short_description }}
                </p>
            @endif

            {{-- Sélecteur quantité + Ajouter panier --}}
            @if($product->stock_quantity > 0)
                <div class="space-y-3">
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-dark">Quantité :</span>
                        <div class="flex items-center gap-3 bg-gray-100 rounded-xl p-1">
                            <button wire:click="decrementQty"
                                    @class([
                                        'w-8 h-8 rounded-lg flex items-center justify-center
                                         transition-colors font-bold',
                                        'bg-white shadow text-dark' => $quantity > 1,
                                        'text-gray-300 cursor-not-allowed' => $quantity <= 1,
                                    ])>−</button>
                            <span class="w-8 text-center font-bold text-dark">{{ $quantity }}</span>
                            <button wire:click="incrementQty"
                                    @class([
                                        'w-8 h-8 rounded-lg flex items-center justify-center
                                         transition-colors font-bold',
                                        'bg-white shadow text-dark' => $quantity < $product->stock_quantity,
                                        'text-gray-300 cursor-not-allowed' => $quantity >= $product->stock_quantity,
                                    ])>+</button>
                        </div>
                        @if($quantity > 1)
                            <span class="text-sm text-gray-500">
                                = <span class="font-bold text-dark">
                                    {{ number_format($product->selling_price * $quantity, 0, ',', ' ') }} FCFA
                                </span>
                            </span>
                        @endif
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="addToCart"
                                @class([
                                    'flex-1 py-3.5 rounded-2xl font-semibold text-sm
                                     transition-all flex items-center justify-center gap-2',
                                    'bg-green-500 text-white' => $added,
                                    'bg-primary hover:bg-primary/90 text-white shadow-lg shadow-primary/25' => !$added,
                                ])>
                            @if($added)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Ajouté !
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
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
                                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Panier
                        </button>
                    </div>

                    {{-- WhatsApp CTA --}}
                    @if($settings->whatsapp)
                        @php
                            $wa  = preg_replace('/[^0-9]/', '', $settings->whatsapp);
                            $msg = urlencode("Bonjour *{$settings->name}* 👋\nJe suis intéressé par *{$product->name}* à {$product->selling_price} FCFA.\nPouvez-vous me donner plus d'informations ?");
                        @endphp
                        <a href="https://wa.me/{{ $wa }}?text={{ $msg }}"
                           target="_blank"
                           class="flex items-center justify-center gap-2.5 w-full py-3 rounded-2xl
                                  border-2 border-green-200 text-green-700 bg-green-50
                                  hover:bg-green-100 hover:border-green-300 transition-all
                                  text-sm font-semibold">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Renseigner sur WhatsApp
                        </a>
                    @endif
                </div>
            @else
                {{-- Rupture --}}
                <div class="bg-red-50 border border-red-100 rounded-2xl p-4 text-center">
                    <p class="text-red-600 font-medium text-sm mb-3">
                        Ce produit est actuellement indisponible
                    </p>
                    @if($settings->whatsapp)
                        @php
                            $wa  = preg_replace('/[^0-9]/', '', $settings->whatsapp);
                            $msg = urlencode("Bonjour *{$settings->name}* 👋\nJe suis intéressé par *{$product->name}* mais il est en rupture. Pouvez-vous me prévenir dès qu'il sera disponible ?");
                        @endphp
                        <a href="https://wa.me/{{ $wa }}?text={{ $msg }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600
                                  text-white px-5 py-2.5 rounded-xl text-sm font-semibold
                                  transition-colors shadow-lg shadow-green-500/25">
                            Me notifier sur WhatsApp
                        </a>
                    @endif
                </div>
            @endif

            {{-- Badges de confiance --}}
            <div class="grid grid-cols-3 gap-3">
                @foreach([
                    ['icon' => '🚚', 'title' => 'Livraison rapide',   'sub' => 'Dakar & environs'],
                    ['icon' => '✅', 'title' => 'Qualité garantie',   'sub' => '12 mois minimum'],
                    ['icon' => '🔒', 'title' => 'Paiement sécurisé', 'sub' => 'Wave · OM · Cash'],
                ] as $badge)
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <p class="text-xl mb-1">{{ $badge['icon'] }}</p>
                        <p class="text-xs font-semibold text-dark">{{ $badge['title'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $badge['sub'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Onglets : Description / Caractéristiques ── --}}
    @php $specs = $product->specifications ?? []; @endphp
    @if($product->description || count($specs) > 0)
        <div x-data="{ tab: '{{ count($specs) > 0 ? 'specs' : 'description' }}' }"
             class="mt-12">

            {{-- Tab nav --}}
            <div class="flex gap-1 border-b border-gray-200 mb-6">
                @if($product->description)
                    <button @click="tab = 'description'"
                            :class="tab === 'description'
                                ? 'border-b-2 border-primary text-primary font-semibold'
                                : 'text-gray-500 hover:text-dark'"
                            class="px-5 py-3 text-sm transition-colors -mb-px">
                        Description
                    </button>
                @endif
                @if(count($specs) > 0)
                    <button @click="tab = 'specs'"
                            :class="tab === 'specs'
                                ? 'border-b-2 border-primary text-primary font-semibold'
                                : 'text-gray-500 hover:text-dark'"
                            class="px-5 py-3 text-sm transition-colors -mb-px flex items-center gap-2">
                        Caractéristiques
                        <span class="bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ count($specs) }}
                        </span>
                    </button>
                @endif
            </div>

            {{-- Description --}}
            @if($product->description)
                <div x-show="tab === 'description'" x-cloak>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                        <div class="text-gray-600 text-sm leading-relaxed prose max-w-none">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                </div>
            @endif

            {{-- Caractéristiques --}}
            @if(count($specs) > 0)
                <div x-show="tab === 'specs'" x-cloak>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="grid grid-cols-1 sm:grid-cols-2">
                            @foreach($specs as $i => $spec)
                                <div @class([
                                        'flex items-start gap-4 px-6 py-4 border-b border-gray-50',
                                        'sm:border-r' => $i % 2 === 0,
                                        'bg-gray-50/50' => $i % 4 >= 2,
                                    ])>
                                    <div class="w-8 h-8 rounded-lg bg-primary/8 flex items-center
                                                justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-primary/60" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-gray-400 uppercase
                                                   tracking-wider mb-0.5">
                                            {{ $spec['label'] }}
                                        </p>
                                        <p class="text-sm font-semibold text-dark">
                                            {{ $spec['value'] ?: '—' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Reset added state --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('reset-added', () => {
                setTimeout(() => { @this.set('added', false); }, 2000);
            });
        });
    </script>
</div>
