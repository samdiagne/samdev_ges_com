<a href="{{ route('shop.produit', $produit->slug) }}"
   class="group bg-white rounded-xl sm:rounded-2xl border border-gray-100 overflow-hidden
          hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 relative">

    {{-- IMAGE --}}
    <div class="relative aspect-square bg-gray-50 overflow-hidden">

        {{-- Image --}}
        @if($produit->image_principale)
            <img src="{{ Storage::url($produit->image_principale) }}"
                 alt="{{ $produit->nom }} à Dakar"
                 loading="lazy"
                 class="w-full h-full object-cover
                        group-hover:scale-110 transition duration-500"/>
        @endif

        {{-- Overlay hover --}}
        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition"></div>

        {{-- BADGES --}}
        <div class="absolute top-1.5 left-1.5 sm:top-2 sm:left-2 flex flex-col gap-1">

            @if($produit->sur_commande)
                <span class="bg-blue-500 text-white text-[9px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md sm:rounded-lg font-bold whitespace-nowrap">
                    Sur commande
                </span>
            @elseif($produit->stock <= 0)
                <span class="bg-red-500 text-white text-[9px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md sm:rounded-lg font-bold whitespace-nowrap">
                    Rupture
                </span>
            @elseif($produit->estStockFaible())
                <span class="bg-orange-500 text-white text-[9px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md sm:rounded-lg font-bold whitespace-nowrap">
                    Stock limité
                </span>
            @endif

            {{-- Promo --}}
            @if($produit->estEnPromotion())
                <span class="bg-red-600 text-white text-[9px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md sm:rounded-lg font-bold whitespace-nowrap">
                    -{{ $produit->pourcentageRemise() }}%
                </span>
            @endif
        </div>

        {{-- Featured --}}
        @if($produit->est_mis_en_avant)
            <div class="absolute top-1.5 right-1.5 sm:top-2 sm:right-2 text-[9px] sm:text-xs font-bold
                        px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md sm:rounded-lg text-white whitespace-nowrap"
                 style="background-color: #2DB667;">
                ⭐ Vedette
            </div>
        @endif

        {{-- HOVER CTA (desktop) --}}
        <div class="absolute bottom-2 sm:bottom-3 left-1/2 -translate-x-1/2 opacity-0
                    group-hover:opacity-100 transition duration-300 w-[90%] hidden sm:block">
            <span class="block w-full text-white text-xs py-2 rounded-xl font-semibold
                         shadow-lg bg-primary text-center">
                Plus de détails
            </span>
        </div>

    </div>

    {{-- INFOS --}}
    <div class="p-3 sm:p-4">

        {{-- Catégorie --}}
        <p class="text-[10px] sm:text-xs text-primary font-medium mb-0.5 sm:mb-1">
            {{ $produit->categorie->nom ?? '' }}
        </p>

        {{-- Nom --}}
        <h3 class="text-xs sm:text-sm font-semibold text-dark line-clamp-2 mb-1.5 sm:mb-2
                   group-hover:text-primary transition leading-snug">
            {{ $produit->nom }}
        </h3>

        {{-- Rating --}}
        @php
            $nbAvis  = $produit->avisApprouves->count();
            $moyenne = $nbAvis > 0 ? round($produit->avisApprouves->avg('note'), 1) : null;
        @endphp
        @if($nbAvis > 0)
            <div class="flex items-center gap-0.5 sm:gap-1 text-yellow-400 text-[10px] sm:text-xs mb-1.5 sm:mb-2">
                @for($i = 1; $i <= 5; $i++){{ $i <= round($moyenne) ? '⭐' : '☆' }}@endfor
                <span class="text-gray-400">({{ $nbAvis }})</span>
            </div>
        @endif

        {{-- PRIX --}}
        <div>
            <p class="text-sm sm:text-lg font-bold text-dark">
                {{ number_format($produit->prix_vente, 0, ',', ' ') }}
                <span class="text-[10px] sm:text-xs font-normal text-gray-400">FCFA</span>
            </p>

            {{-- Ancien prix --}}
            @if($produit->estEnPromotion())
                <p class="text-[10px] sm:text-xs text-gray-400 line-through">
                    {{ number_format($produit->ancien_prix, 0, ',', ' ') }} FCFA
                </p>
            @endif
        </div>

        {{-- BOUTON --}}
        @if($produit->sur_commande || $produit->stock > 0)
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
                                body: JSON.stringify({ produit_id: {{ $produit->id }}, quantite: 1 })
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
                    <span x-show="!loading && !done">{{ $produit->sur_commande ? 'Commandez maintenant' : '🛒 Ajouter au panier' }}</span>
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