<a href="{{ route('shop.produit', $product->slug) }}"
   class="group bg-white rounded-2xl border border-gray-100 overflow-hidden
          hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 relative">

    {{-- IMAGE --}}
    <div class="relative aspect-square bg-gray-50 overflow-hidden">

        {{-- Image --}}
        @if($product->main_image)
            <img src="{{ Storage::url($product->main_image) }}"
                 alt="{{ $product->name }}{{ $product->brand ? ' — ' . $product->brand : '' }} à Dakar"
                 loading="lazy"
                 class="w-full h-full object-contain p-5
                        group-hover:scale-110 transition duration-500"/>
        @endif

        {{-- Overlay hover --}}
        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition"></div>

        {{-- BADGES --}}
        <div class="absolute top-2 left-2 flex flex-col gap-1">

            @if($product->stock_quantity <= 0)
                <span class="bg-red-500 text-white text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-lg font-bold whitespace-nowrap">
                    Rupture
                </span>
            @elseif($product->isLowStock())
                <span class="bg-orange-500 text-white text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-lg font-bold whitespace-nowrap">
                    Stock limité
                </span>
            @endif

            {{-- Promo --}}
            @if($product->isOnSale())
                <span class="bg-red-600 text-white text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-lg font-bold whitespace-nowrap">
                    -{{ $product->discountPercent() }}%
                </span>
            @endif
        </div>

        {{-- Featured --}}
        @if($product->is_featured)
            <div class="absolute top-2 right-2 text-[10px] sm:text-xs font-bold
                        px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-lg text-white whitespace-nowrap"
                 style="background-color: #F5A800;">
                ⭐ Vedette
            </div>
        @endif

        {{-- QUICK ADD (hover desktop) --}}
        @if($product->stock_quantity > 0)
        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 opacity-0
                    group-hover:opacity-100 transition duration-300 w-[90%]"
             x-data="{ loading: false, done: false }"
             @click.stop>
            <button @click="
                        loading = true;
                        fetch('{{ route('shop.panier.add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ product_id: {{ $product->id }}, quantity: 1 })
                        })
                        .then(() => {
                            loading = false;
                            done = true;
                            setTimeout(() => done = false, 2000);
                            Livewire.dispatch('cart-updated');
                            Livewire.dispatch('open-cart');
                        })
                        .catch(() => { loading = false; })
                    "
                    :disabled="loading || done"
                    class="w-full text-white text-xs py-2 rounded-xl font-semibold
                           shadow-lg transition disabled:opacity-70"
                    :class="done ? 'bg-green-500' : 'bg-primary hover:scale-105'">
                <span x-show="!loading && !done">🛒 Ajouter au panier</span>
                <span x-show="loading">
                    <svg class="w-3.5 h-3.5 animate-spin inline" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                </span>
                <span x-show="done">✓ Ajouté !</span>
            </button>
        </div>
        @endif

    </div>

    {{-- INFOS --}}
    <div class="p-4">

        {{-- Catégorie --}}
        <p class="text-xs text-primary font-medium mb-1">
            {{ $product->category->name ?? '' }}
        </p>

        {{-- Nom --}}
        <h3 class="text-sm font-semibold text-dark line-clamp-2 mb-2
                   group-hover:text-primary transition">
            {{ $product->name }}
        </h3>

        {{-- Marque --}}
        @if($product->brand)
            <p class="text-xs text-gray-400 mb-2">{{ $product->brand }}</p>
        @endif

        {{-- Rating fake (à remplacer plus tard) --}}
        <div class="flex items-center gap-1 text-yellow-400 text-xs mb-2">
            ⭐⭐⭐⭐☆
            <span class="text-gray-400">(12)</span>
        </div>

        {{-- PRIX --}}
        <div class="flex items-center justify-between">

            <div>
                <p class="text-lg font-bold text-dark">
                    {{ number_format($product->selling_price, 0, ',', ' ') }}
                    <span class="text-xs font-normal text-gray-400">FCFA</span>
                </p>

                {{-- Ancien prix --}}
                @if($product->isOnSale())
                    <p class="text-xs text-gray-400 line-through">
                        {{ number_format($product->old_price, 0, ',', ' ') }} FCFA
                    </p>
                @endif
            </div>

        </div>

        {{-- BOUTON NORMAL (mobile surtout) --}}
        @if($product->stock_quantity > 0)
            <div class="mt-3"
                 x-data="{ loading: false, done: false }"
                 @click.stop>
                <button @click="
                            loading = true;
                            fetch('{{ route('shop.panier.add') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ product_id: {{ $product->id }}, quantity: 1 })
                            })
                            .then(() => {
                                loading = false;
                                done = true;
                                setTimeout(() => done = false, 2000);
                                Livewire.dispatch('cart-updated');
                                Livewire.dispatch('open-cart');
                            })
                            .catch(() => { loading = false; })
                        "
                        :disabled="loading || done"
                        class="w-full py-2 rounded-xl text-sm font-semibold transition-all
                               duration-200 flex items-center justify-center gap-2 border-2
                               border-primary text-primary hover:bg-primary hover:text-white
                               disabled:opacity-70 disabled:cursor-not-allowed"
                        :class="done ? 'bg-green-500 border-green-500 text-white hover:bg-green-500 hover:text-white' : ''">
                    <span x-show="!loading && !done">🛒 Ajouter au panier</span>
                    <span x-show="loading">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                    </span>
                    <span x-show="done">✓ Ajouté</span>
                </button>
            </div>
        @else
            <div class="mt-3 w-full py-2 rounded-xl text-sm font-semibold
                        text-center bg-gray-100 text-gray-400">
                Rupture de stock
            </div>
        @endif

    </div>
</a>