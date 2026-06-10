<div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">

        {{-- ── Galerie images ── --}}
        <div class="space-y-4">
            <div x-data="{ zoomed: false }"
                 class="aspect-square bg-white rounded-3xl border border-gray-100
                        shadow-sm overflow-hidden flex items-center justify-center p-8 relative">

                @if(count($allImages) > 0)
                    <img src="{{ Storage::url($allImages[$activeImage]) }}"
                         alt="{{ $plat->nom }}"
                         loading="eager"
                         @click="zoomed = true"
                         class="w-full h-full object-contain transition-all duration-300 cursor-zoom-in"/>

                    @if(count($allImages) > 1)
                        @if($activeImage > 0)
                            <button wire:click="setImage({{ $activeImage - 1 }})"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9
                                           bg-white/90 hover:bg-white rounded-full shadow-md
                                           flex items-center justify-center text-dark text-lg
                                           font-bold transition-all hover:shadow-lg z-10">‹</button>
                        @endif
                        @if($activeImage < count($allImages) - 1)
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
                    @if($plat->estEnPromotion())
                        <div class="absolute top-4 left-4 bg-red-500 text-white text-sm
                                    font-bold px-3 py-1.5 rounded-xl shadow-lg">
                            -{{ $plat->pourcentageRemise() }}%
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
                             alt="{{ $plat->nom }}"
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
            @if(count($allImages) > 1)
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

        {{-- ── Détails du plat ── --}}
        <div class="space-y-5">

            {{-- Catégorie + titre --}}
            <div>
                @if($plat->categorie)
                    <p class="text-xs font-semibold text-primary uppercase tracking-widest mb-2">
                        {{ $plat->categorie->nom }}
                    </p>
                @endif
                <h1 class="text-2xl lg:text-3xl font-bold text-dark leading-tight mb-3">
                    {{ $plat->nom }}
                </h1>
            </div>

            {{-- Prix --}}
            <div class="bg-gray-50 rounded-2xl p-5">
                <div class="flex items-center gap-3 flex-wrap mb-3">
                    <p class="text-3xl font-bold text-dark">
                        {{ number_format($plat->prix_vente, 0, ',', ' ') }}
                        <span class="text-lg font-normal text-gray-400">FCFA</span>
                    </p>
                    @if($plat->estEnPromotion())
                        <span class="bg-red-500 text-white text-sm font-bold px-2.5 py-1 rounded-xl">
                            -{{ $plat->pourcentageRemise() }}%
                        </span>
                    @endif
                </div>
                @if($plat->estEnPromotion())
                    <p class="text-sm text-gray-400 line-through">
                        {{ number_format($plat->ancien_prix, 0, ',', ' ') }} FCFA
                    </p>
                    <p class="text-xs text-red-600 font-medium mt-0.5">
                        Vous économisez {{ number_format($plat->ancien_prix - $plat->prix_vente, 0, ',', ' ') }} FCFA
                    </p>
                @endif
                {{-- Stock --}}
                <div class="flex items-center gap-2 mt-2">
                    @if($plat->stock <= 0)
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-red-600">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            Rupture de stock
                        </span>
                    @elseif($plat->estStockFaible())
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-orange-600">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            Stock limité — {{ $plat->stock }} restant(s)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-green-600">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            En stock
                        </span>
                    @endif
                </div>
            </div>

            {{-- Ingrédients (aperçu) --}}
            @php $ingredients = $plat->ingredients ?? []; @endphp
            @if(count($ingredients) > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach(array_slice($ingredients, 0, 8) as $ingredient)
                        <span class="bg-white border border-gray-100 rounded-xl px-3 py-1.5 text-xs font-medium text-dark">
                            {{ $ingredient }}
                        </span>
                    @endforeach
                    @if(count($ingredients) > 8)
                        <span class="text-xs text-gray-400 px-2 py-1.5">
                            +{{ count($ingredients) - 8 }} autres
                        </span>
                    @endif
                </div>
            @endif

            {{-- Description courte --}}
            @if($plat->description_courte)
                <p class="text-gray-600 text-sm leading-relaxed">
                    {{ $plat->description_courte }}
                </p>
            @endif

            {{-- Sélecteur quantité + Ajouter panier --}}
            @if($plat->stock > 0)
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
                                        'bg-white shadow text-dark' => $quantity < $plat->stock,
                                        'text-gray-300 cursor-not-allowed' => $quantity >= $plat->stock,
                                    ])>+</button>
                        </div>
                        @if($quantity > 1)
                            <span class="text-sm text-gray-500">
                                = <span class="font-bold text-dark">
                                    {{ number_format($plat->prix_vente * $quantity, 0, ',', ' ') }} FCFA
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
                    @if($parametres?->whatsapp)
                        @php
                            $wa  = preg_replace('/[^0-9]/', '', $parametres->whatsapp);
                            $msg = urlencode("Bonjour *{$parametres->nom}* \nJe suis intéressé par *{$plat->nom}* à {$plat->prix_vente} FCFA.\nPouvez-vous me donner plus d'informations ?");
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
                        Ce plat est actuellement indisponible
                    </p>
                    @if($parametres?->whatsapp)
                        @php
                            $wa  = preg_replace('/[^0-9]/', '', $parametres->whatsapp);
                            $msg = urlencode("Bonjour *{$parametres->nom}* \nJe suis intéressé par *{$plat->nom}* mais il est en rupture. Pouvez-vous me prévenir dès qu'il sera disponible ?");
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
                    ['icon' => 'truck', 'title' => 'Livraison rapide',   'sub' => 'Dakar & environs'],
                    ['icon' => 'check', 'title' => 'Qualité garantie',   'sub' => 'Fraîcheur assurée'],
                    ['icon' => 'lock',  'title' => 'Paiement sécurisé', 'sub' => 'Wave · OM · Cash'],
                ] as $badge)
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <p class="text-xl mb-1">
                            @if($badge['icon'] === 'truck') <span>&#x1F69A;</span>
                            @elseif($badge['icon'] === 'check') <span>&#x2705;</span>
                            @else <span>&#x1F512;</span>
                            @endif
                        </p>
                        <p class="text-xs font-semibold text-dark">{{ $badge['title'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $badge['sub'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Onglets : Description / Ingrédients ── --}}
    @php $ingredients = $plat->ingredients ?? []; @endphp
    @if($plat->description || count($ingredients) > 0)
        <div x-data="{ tab: '{{ count($ingredients) > 0 ? 'ingredients' : 'description' }}' }"
             class="mt-12">

            {{-- Tab nav --}}
            <div class="flex gap-1 border-b border-gray-200 mb-6">
                @if($plat->description)
                    <button @click="tab = 'description'"
                            :class="tab === 'description'
                                ? 'border-b-2 border-primary text-primary font-semibold'
                                : 'text-gray-500 hover:text-dark'"
                            class="px-5 py-3 text-sm transition-colors -mb-px">
                        Description
                    </button>
                @endif
                @if(count($ingredients) > 0)
                    <button @click="tab = 'ingredients'"
                            :class="tab === 'ingredients'
                                ? 'border-b-2 border-primary text-primary font-semibold'
                                : 'text-gray-500 hover:text-dark'"
                            class="px-5 py-3 text-sm transition-colors -mb-px flex items-center gap-2">
                        Ingrédients
                        <span class="bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ count($ingredients) }}
                        </span>
                    </button>
                @endif
            </div>

            {{-- Description --}}
            @if($plat->description)
                <div x-show="tab === 'description'" x-cloak>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                        <div class="text-gray-600 text-sm leading-relaxed prose max-w-none">
                            {!! nl2br(e($plat->description)) !!}
                        </div>
                    </div>
                </div>
            @endif

            {{-- Ingrédients --}}
            @if(count($ingredients) > 0)
                <div x-show="tab === 'ingredients'" x-cloak>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <div class="flex flex-wrap gap-2">
                            @foreach($ingredients as $ingredient)
                                <span class="inline-flex items-center gap-1.5 bg-gray-50 border border-gray-100
                                             rounded-xl px-4 py-2 text-sm text-dark font-medium">
                                    <svg class="w-3.5 h-3.5 text-primary/60" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $ingredient }}
                                </span>
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
