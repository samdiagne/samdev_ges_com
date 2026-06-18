<a href="{{ route('shop.plat', $plat->slug) }}"
   class="group bg-white rounded-xl sm:rounded-2xl border border-gray-100 overflow-hidden
          hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 relative">

    {{-- IMAGE --}}
    <div class="relative aspect-square bg-gray-50 overflow-hidden">

        {{-- Image --}}
        @if($plat->image_principale)
            <img src="{{ Storage::url($plat->image_principale) }}"
                 alt="{{ $plat->nom }} à Dakar"
                 loading="lazy"
                 class="w-full h-full object-contain p-3 sm:p-5
                        group-hover:scale-110 transition duration-500"/>
        @endif

        {{-- Overlay hover --}}
        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition"></div>

        {{-- BADGES --}}
        <div class="absolute top-1.5 left-1.5 sm:top-2 sm:left-2 flex flex-col gap-1">

            @if($plat->sur_commande)
                <span class="bg-blue-500 text-white text-[9px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md sm:rounded-lg font-bold whitespace-nowrap">
                    Sur commande
                </span>
            @elseif($plat->stock <= 0)
                <span class="bg-red-500 text-white text-[9px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md sm:rounded-lg font-bold whitespace-nowrap">
                    Rupture
                </span>
            @elseif($plat->estStockFaible())
                <span class="bg-orange-500 text-white text-[9px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md sm:rounded-lg font-bold whitespace-nowrap">
                    Stock limité
                </span>
            @endif

            {{-- Promo --}}
            @if($plat->estEnPromotion())
                <span class="bg-red-600 text-white text-[9px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md sm:rounded-lg font-bold whitespace-nowrap">
                    -{{ $plat->pourcentageRemise() }}%
                </span>
            @endif
        </div>

        {{-- Featured --}}
        @if($plat->est_mis_en_avant)
            <div class="absolute top-1.5 right-1.5 sm:top-2 sm:right-2 text-[9px] sm:text-xs font-bold
                        px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md sm:rounded-lg text-white whitespace-nowrap"
                 style="background-color: #2DB667;">
                ⭐ Vedette
            </div>
        @endif

        {{-- QUICK ADD (hover desktop) --}}
        @if($plat->sur_commande || $plat->stock > 0)
        <div class="absolute bottom-2 sm:bottom-3 left-1/2 -translate-x-1/2 opacity-0
                    group-hover:opacity-100 transition duration-300 w-[90%] hidden sm:block"
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
                            body: JSON.stringify({ plat_id: {{ $plat->id }}, quantite: 1 })
                        })
                        .then(() => {
                            loading = false;
                            done = true;
                            setTimeout(() => done = false, 2000);
                            Livewire.dispatch('panier-mis-a-jour');
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
    <div class="p-3 sm:p-4">

        {{-- Catégorie --}}
        <p class="text-[10px] sm:text-xs text-primary font-medium mb-0.5 sm:mb-1">
            {{ $plat->categorie->nom ?? '' }}
        </p>

        {{-- Nom --}}
        <h3 class="text-xs sm:text-sm font-semibold text-dark line-clamp-2 mb-1.5 sm:mb-2
                   group-hover:text-primary transition leading-snug">
            {{ $plat->nom }}
        </h3>

        {{-- Rating --}}
        <div class="flex items-center gap-0.5 sm:gap-1 text-yellow-400 text-[10px] sm:text-xs mb-1.5 sm:mb-2">
            ⭐⭐⭐⭐☆
            <span class="text-gray-400">(12)</span>
        </div>

        {{-- PRIX --}}
        <div>
            <p class="text-sm sm:text-lg font-bold text-dark">
                {{ number_format($plat->prix_vente, 0, ',', ' ') }}
                <span class="text-[10px] sm:text-xs font-normal text-gray-400">FCFA</span>
            </p>

            {{-- Ancien prix --}}
            @if($plat->estEnPromotion())
                <p class="text-[10px] sm:text-xs text-gray-400 line-through">
                    {{ number_format($plat->ancien_prix, 0, ',', ' ') }} FCFA
                </p>
            @endif
        </div>

        {{-- BOUTON --}}
        @if($plat->sur_commande || $plat->stock > 0)
            <div class="mt-2 sm:mt-3"
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
                                body: JSON.stringify({ plat_id: {{ $plat->id }}, quantite: 1 })
                            })
                            .then(() => {
                                loading = false;
                                done = true;
                                setTimeout(() => done = false, 2000);
                                Livewire.dispatch('panier-mis-a-jour');
                                Livewire.dispatch('open-cart');
                            })
                            .catch(() => { loading = false; })
                        "
                        :disabled="loading || done"
                        class="w-full py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-[11px] sm:text-sm font-semibold transition-all
                               duration-200 flex items-center justify-center gap-1.5 sm:gap-2 border-2
                               border-primary text-primary hover:bg-primary hover:text-white
                               disabled:opacity-70 disabled:cursor-not-allowed"
                        :class="done ? 'bg-green-500 border-green-500 text-white hover:bg-green-500 hover:text-white' : ''">
                    <span x-show="!loading && !done">🛒 <span class="hidden sm:inline">Ajouter au </span>panier</span>
                    <span x-show="loading">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                    </span>
                    <span x-show="done">✓ Ajouté</span>
                </button>
            </div>
        @else
            <div class="mt-2 sm:mt-3 w-full py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-[11px] sm:text-sm font-semibold
                        text-center bg-red-50 text-red-400">
                Rupture de stock
            </div>
        @endif

    </div>
</a>