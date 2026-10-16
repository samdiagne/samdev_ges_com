@extends('layouts.shop')
@section('title', 'Mon Panier')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <h1 class="text-xl sm:text-2xl font-bold text-dark mb-6 sm:mb-8">Mon Panier</h1>

    @if(count($panier) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">

            {{-- ── Articles ── --}}
            <div class="lg:col-span-2 space-y-3 sm:space-y-4">
                @foreach($panier as $key => $item)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5">
                        <div class="flex items-start gap-3 sm:gap-5">

                            {{-- Image --}}
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-50 rounded-xl flex items-center
                                        justify-center flex-shrink-0 overflow-hidden">
                                @if($item['image'])
                                    <img src="{{ Storage::url($item['image']) }}"
                                         class="w-full h-full object-contain p-2"/>
                                @else
                                    <svg class="w-7 h-7 text-gray-300" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="1.5"
                                              d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0
                                                 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>

                            {{-- Infos + contrôles --}}
                            <div class="flex-1 min-w-0">
                                {{-- Nom + Supprimer --}}
                                <div class="flex items-start justify-between gap-2">
                                    <a href="{{ route('shop.produit', $item['slug']) }}"
                                       class="text-sm font-semibold text-dark hover:text-primary
                                              transition-colors line-clamp-2 flex-1">
                                        {{ $item['nom'] }}
                                    </a>
                                    <form action="{{ route('shop.panier.remove') }}" method="POST" class="flex-shrink-0">
                                        @csrf
                                        <input type="hidden" name="produit_id" value="{{ $item['produit_id'] }}"/>
                                        <button type="submit"
                                                class="w-7 h-7 flex items-center justify-center
                                                       text-gray-300 hover:text-red-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                {{-- Prix unit --}}
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ number_format($item['prix'], 0, ',', ' ') }} FCFA / unité
                                </p>

                                {{-- Quantité + Sous-total --}}
                                <div class="flex items-center justify-between mt-2.5">
                                    <form action="{{ route('shop.panier.update') }}"
                                          method="POST" class="flex items-center">
                                        @csrf
                                        <input type="hidden" name="produit_id" value="{{ $item['produit_id'] }}"/>
                                        <div class="flex items-center gap-1.5 bg-gray-100 rounded-xl p-1">
                                            <button type="submit" name="quantite" value="{{ $item['quantite'] - 1 }}"
                                                    class="w-7 h-7 bg-white rounded-lg flex items-center justify-center
                                                           font-bold shadow text-dark hover:bg-gray-50 transition-colors text-sm">
                                                −
                                            </button>
                                            <span class="w-6 text-center text-sm font-bold">
                                                {{ $item['quantite'] }}
                                            </span>
                                            <button type="submit" name="quantite" value="{{ $item['quantite'] + 1 }}"
                                                    class="w-7 h-7 bg-white rounded-lg flex items-center justify-center
                                                           font-bold shadow text-dark hover:bg-gray-50 transition-colors text-sm">
                                                +
                                            </button>
                                        </div>
                                    </form>

                                    <p class="text-sm font-bold text-dark">
                                        {{ number_format($item['prix'] * $item['quantite'], 0, ',', ' ') }}
                                        <span class="text-xs font-normal text-gray-400">FCFA</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Continuer shopping --}}
                <a href="{{ route('shop.catalogue') }}"
                   class="inline-flex items-center gap-2 text-sm text-gray-500
                          hover:text-primary transition-colors mt-2">
                    ← Continuer mes achats
                </a>
            </div>

            {{-- ── Résumé commande ── --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100
                            shadow-sm p-6 sticky top-24">
                    <h2 class="text-base font-bold text-dark mb-5">
                        Résumé de la commande
                    </h2>

                    <div class="space-y-3 mb-5">
                        @foreach($panier as $item)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 truncate max-w-36">
                                    {{ $item['nom'] }}
                                    <span class="text-gray-400">
                                        ×{{ $item['quantite'] }}
                                    </span>
                                </span>
                                <span class="font-medium flex-shrink-0 ml-2">
                                    {{ number_format(
                                        $item['prix'] * $item['quantite'],
                                        0, ',', ' ') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 pt-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-dark">Total</span>
                            <span class="text-xl font-bold text-primary">
                                {{ number_format($total, 0, ',', ' ') }}
                                <span class="text-sm font-normal text-gray-400">
                                    FCFA
                                </span>
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('shop.checkout') }}"
                       class="w-full flex items-center justify-center gap-2
                              py-3.5 rounded-2xl font-bold text-sm text-white
                              transition-all shadow-lg shadow-primary/25
                              hover:scale-[1.02]"
                       style="background-color: #F27B20;">
                        Passer la commande →
                    </a>

                    {{-- Bouton WhatsApp direct (Désactivé pour privilégier le tunnel de commande)
                    <div class="flex items-center gap-3 my-4">
                        <div class="flex-1 border-t border-gray-100"></div>
                        <span class="text-xs text-gray-400 font-medium">ou</span>
                        <div class="flex-1 border-t border-gray-100"></div>
                    </div>

                    @php
                        $parametres = App\Models\Parametre::first();
                        $wa = preg_replace('/[^0-9]/', '', $parametres->whatsapp ?? $parametres->telephone ?? '');
                    @endphp

                    @if($wa)
                        @if(count($panier) > 0)
                            @php
                                $msg  = "Bonjour *{$parametres->nom}* 👋\n\n";
                                $msg .= "Je souhaite passer une commande :\n\n";
                                foreach ($panier as $item) {
                                    $msg .= "• *{$item['nom']}* x{$item['quantite']} — "
                                        . number_format($item['prix'] * $item['quantite'], 0, ',', ' ')
                                        . " FCFA\n";
                                }
                                $msg .= "\n💰 *Total : " . number_format($total, 0, ',', ' ') . " FCFA*\n\n";
                                $msg .= "Comment puis-je procéder au règlement ?";
                            @endphp

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
                    @endif
                    --}}
                            {{-- Panier vide → liste de sujets --}}
                            <div x-data="{ open: false, sujet: '' }">

                                <button @click="open = !open"
                                        class="flex items-center justify-center gap-2 w-full py-3
                                            bg-green-500 hover:bg-green-400 text-white font-semibold
                                            rounded-2xl transition-all shadow-lg shadow-green-500/25
                                            text-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                    Contacter via WhatsApp
                                </button>

                                {{-- Liste sujets --}}
                                <div x-show="open" x-transition
                                    class="mt-3 bg-green-50 border border-green-100 rounded-2xl
                                            p-3 space-y-2">
                                    <p class="text-xs font-semibold text-green-700 mb-2">
                                        Choisissez un sujet :
                                    </p>

                                    @php
                                        $wa = preg_replace('/[^0-9]/', '', $parametres->whatsapp ?? $parametres->telephone ?? '');
                                        $sujets = [
                                            [
                                                'label' => '🛒 Passer une commande',
                                                'msg'   => "Bonjour *{$parametres->nom}* 👋\nJe souhaite passer une commande. Pouvez-vous m'aider ?",
                                            ],
                                            [
                                                'label' => '📦 Disponibilité d\'un Produit',
                                                'msg'   => "Bonjour *{$parametres->nom}* 👋\nJe voudrais savoir si un Produit est disponible.",
                                            ],
                                            [
                                                'label' => '💰 Demande de prix',
                                                'msg'   => "Bonjour *{$parametres->nom}* 👋\nJe souhaite avoir des informations sur vos tarifs.",
                                            ],
                                            [
                                                'label' => '🚚 Infos livraison',
                                                'msg'   => "Bonjour *{$parametres->nom}* 👋\nJe voudrais des informations sur vos délais et frais de livraison.",
                                            ],
                                            [
                                                'label' => '🔧 Service après-vente',
                                                'msg'   => "Bonjour *{$parametres->nom}* 👋\nJ'ai besoin d'assistance concernant ma commande.",
                                            ],
                                            [
                                                'label' => '❓ Autre question',
                                                'msg'   => "Bonjour *{$parametres->nom}* 👋\nJ'ai une question à vous poser.",
                                            ],
                                        ];
                                    @endphp

                                    @foreach($sujets as $sujet)
                                        <a href="https://wa.me/{{ $wa }}?text={{ urlencode($sujet['msg']) }}"
                                        target="_blank"
                                        class="flex items-center gap-2 w-full px-3 py-2.5 bg-white
                                                hover:bg-green-500 hover:text-white text-sm text-gray-700
                                                font-medium rounded-xl transition-all border border-green-100
                                                hover:border-green-500 group">
                                            <span>{{ $sujet['label'] }}</span>
                                            <svg class="w-3.5 h-3.5 ml-auto text-green-400
                                                        group-hover:text-white transition-colors"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Badges sécurité --}}
                    <div class="flex items-center justify-center gap-4 mt-4">
                        <span class="text-xs text-gray-400 flex items-center gap-1">
                            🔒 Paiement sécurisé
                        </span>
                        <span class="text-xs text-gray-400 flex items-center gap-1">
                            ✅ Qualité garantie
                        </span>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- Panier vide --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm
                    p-16 text-center max-w-md mx-auto">
            <div class="w-20 h-20 bg-gray-100 rounded-3xl flex items-center
                        justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-300" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="1.5"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7
                             13l-2.293 2.293c-.63.63-.184 1.707.707
                             1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8
                             2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h2 class="text-lg font-bold text-dark mb-2">Votre panier est vide</h2>
            <p class="text-sm text-gray-400 mb-6">
                Découvrez nos Produits et ajoutez-les à votre panier.
            </p>
            <a href="{{ route('shop.catalogue') }}"
               class="inline-flex items-center gap-2 bg-primary text-white
                      px-6 py-3 rounded-2xl font-semibold text-sm
                      hover:bg-primary/90 transition-all">
                Voir le catalogue
            </a>
        </div>
    @endif
</div>
@endsection