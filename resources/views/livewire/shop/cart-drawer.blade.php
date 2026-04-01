<div>
    {{-- Overlay --}}
    <div x-show="$wire.open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="$wire.close()"
         class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40"
         style="display:none;"></div>

    {{-- Tiroir --}}
    <div x-show="$wire.open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-full"
         class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-50
                flex flex-col"
         style="display:none;">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-primary/10 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                                 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-dark text-base">Mon panier</h2>
                    <p class="text-xs text-gray-400">{{ $count }} article{{ $count > 1 ? 's' : '' }}</p>
                </div>
            </div>
            <button wire:click="close"
                    class="w-9 h-9 flex items-center justify-center rounded-xl
                           hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Corps --}}
        <div class="flex-1 overflow-y-auto px-6 py-4">

            @if(empty($cart))
                {{-- Panier vide --}}
                <div class="flex flex-col items-center justify-center h-full text-center py-16">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                                     1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-dark mb-1">Votre panier est vide</p>
                    <p class="text-sm text-gray-400 mb-6">Ajoutez des articles pour commencer</p>
                    <button wire:click="close"
                            onclick="window.location='{{ route('shop.catalogue') }}'"
                            class="px-6 py-2.5 bg-primary text-white text-sm font-semibold
                                   rounded-xl hover:bg-primary/90 transition-colors">
                        Voir le catalogue
                    </button>
                </div>
            @else
                {{-- Liste des articles --}}
                <div class="space-y-3">
                    @foreach($cart as $key => $item)
                        <div class="flex gap-3 p-3 bg-gray-50 rounded-2xl group">
                            {{-- Image --}}
                            <div class="w-16 h-16 bg-white rounded-xl overflow-hidden shrink-0 border border-gray-100">
                                @if($item['image'])
                                    <img src="{{ Storage::url($item['image']) }}"
                                         alt="{{ $item['name'] }}"
                                         class="w-full h-full object-contain p-1"/>
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0
                                                     012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Détails --}}
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('shop.produit', $item['slug']) }}"
                                   wire:click="close"
                                   class="text-sm font-semibold text-dark hover:text-primary
                                          transition-colors line-clamp-2 leading-tight mb-1 block">
                                    {{ $item['name'] }}
                                </a>
                                <p class="text-sm font-bold text-primary">
                                    {{ number_format($item['price'], 0, ',', ' ') }} FCFA
                                </p>

                                {{-- Contrôles quantité --}}
                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex items-center gap-1">
                                        <button wire:click="decrement('{{ $key }}')"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg
                                                       bg-white border border-gray-200 hover:border-primary
                                                       hover:text-primary transition-colors text-gray-500">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <span class="w-8 text-center text-sm font-bold text-dark">
                                            {{ $item['quantity'] }}
                                        </span>
                                        <button wire:click="increment('{{ $key }}')"
                                                @if($item['quantity'] >= $item['stock']) disabled @endif
                                                class="w-7 h-7 flex items-center justify-center rounded-lg
                                                       bg-white border border-gray-200 hover:border-primary
                                                       hover:text-primary transition-colors text-gray-500
                                                       disabled:opacity-40 disabled:cursor-not-allowed">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16M4 12h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-xs font-semibold text-gray-500">
                                        = {{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                            </div>

                            {{-- Supprimer --}}
                            <button wire:click="remove('{{ $key }}')"
                                    class="w-7 h-7 shrink-0 flex items-center justify-center rounded-lg
                                           text-gray-300 hover:text-red-500 hover:bg-red-50
                                           transition-colors self-start mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5
                                             4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                {{-- Vider le panier --}}
                <div class="mt-4 text-right">
                    <button wire:click="clear"
                            wire:confirm="Vider tout le panier ?"
                            class="text-xs text-gray-400 hover:text-red-500 transition-colors
                                   flex items-center gap-1 ml-auto">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5
                                     4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Vider le panier
                    </button>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        @if(!empty($cart))
            @php
                $settings = App\Models\CompanySetting::getSettings();
                $wa = preg_replace('/[^0-9]/', '', $settings->whatsapp ?? $settings->phone ?? '');
                $msg  = "Bonjour *{$settings->name}* 👋\n\n";
                $msg .= "Je souhaite passer une commande :\n\n";
                foreach ($cart as $item) {
                    $msg .= "• *{$item['name']}* x{$item['quantity']} — "
                          . number_format($item['price'] * $item['quantity'], 0, ',', ' ')
                          . " FCFA\n";
                }
                $msg .= "\n💰 *Total : " . number_format($total, 0, ',', ' ') . " FCFA*\n\n";
                $msg .= "Comment puis-je procéder au règlement ?";
            @endphp

            <div class="border-t border-gray-100 px-6 py-4 space-y-3 bg-white">
                {{-- Résumé --}}
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Sous-total</span>
                    <span class="text-lg font-extrabold text-dark">
                        {{ number_format($total, 0, ',', ' ') }} FCFA
                    </span>
                </div>
                <p class="text-xs text-gray-400">
                    Frais de livraison calculés à l'étape suivante.
                </p>

                {{-- Commander (checkout) --}}
                <a href="{{ route('shop.checkout') }}"
                   class="flex items-center justify-center gap-2 w-full py-3.5 bg-primary
                          hover:bg-primary/90 text-white font-bold text-sm rounded-2xl
                          transition-all shadow-lg shadow-primary/25 hover:scale-[1.01]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Commander — {{ number_format($total, 0, ',', ' ') }} FCFA
                </a>

                {{-- Séparateur --}}
                @if($wa)
                    <div class="flex items-center gap-3">
                        <div class="flex-1 border-t border-gray-100"></div>
                        <span class="text-xs text-gray-400 font-medium">ou</span>
                        <div class="flex-1 border-t border-gray-100"></div>
                    </div>

                    {{-- Commander via WhatsApp --}}
                    <a href="https://wa.me/{{ $wa }}?text={{ urlencode($msg) }}"
                       target="_blank"
                       class="flex items-center justify-center gap-2 w-full py-3 bg-green-500
                              hover:bg-green-400 text-white font-semibold rounded-2xl
                              transition-all shadow-lg shadow-green-500/25 text-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Commander via WhatsApp
                    </a>
                @endif

                {{-- Badges confiance --}}
                <div class="flex items-center justify-center gap-4 pt-1">
                    <span class="text-xs text-gray-400">🔒 Paiement sécurisé</span>
                    <span class="text-xs text-gray-400">✅ Qualité garantie</span>
                </div>

                <button wire:click="close"
                        class="w-full py-2 text-sm font-semibold text-gray-400
                               hover:text-primary transition-colors">
                    Continuer mes achats
                </button>
            </div>
        @endif

    </div>
</div>
