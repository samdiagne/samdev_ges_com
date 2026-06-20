@extends('layouts.shop')

@section('title', $parametres->nom . ' — Restaurant & Livraison à Dakar')
@section('description', 'Découvrez nos plats faits maison à Dakar. Commande en ligne, livraison rapide, qualité garantie.')

@section('content')

{{-- ── HERO ───────────────────────────────── --}}
@php
    $heroPlats = $misEnAvant->take(3);
    $platVedette = $heroPlats->first();
    $carouselImages = $misEnAvant->filter(fn($p) => $p->image_principale)->pluck('image_principale')->values();
    $avisStats = App\Models\Avis::where('est_approuve', true)->selectRaw('COUNT(*) as total, ROUND(AVG(note), 1) as moyenne')->first();
    $avisMoyenne = $avisStats->moyenne ?? null;
    $avisTotal   = $avisStats->total ?? 0;
@endphp
<section class="relative overflow-hidden -mt-1 bg-[#FFF9F4]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-0 items-center
                    min-h-[420px] sm:min-h-[480px] lg:min-h-[560px]">

            {{-- ── TEXTE (gauche) ── --}}
            <div class="text-center lg:text-left py-10 sm:py-14 lg:py-20 relative z-10">

                {{-- Badge --}}
                <div class="inline-flex items-center gap-1.5 sm:gap-2
                            px-3 py-1.5 sm:px-4 sm:py-2 rounded-full
                            text-[10px] sm:text-xs font-bold tracking-wide uppercase
                            border shadow-sm mb-5 sm:mb-6"
                     style="background: rgba(242,123,32,0.08); border-color: rgba(242,123,32,0.2); color: #F27B20;">
                    <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-green-500 animate-pulse"></span>
                    Ouvert maintenant
                </div>

                {{-- Titre --}}
                <h1 class="text-[1.85rem] sm:text-[2.5rem] md:text-[3rem] lg:text-[3.5rem] xl:text-[4rem]
                           font-extrabold leading-[1.1] mb-4 sm:mb-5 tracking-tight text-dark">
                    Commandez<br/>
                    Vos Plats<br/>
                    <span style="color: #F27B20;" class="italic">Préférés.</span>
                </h1>

                {{-- Sous-titre --}}
                <p class="text-gray-500 text-sm sm:text-base lg:text-lg mb-6 sm:mb-8 leading-relaxed
                          max-w-sm sm:max-w-md mx-auto lg:mx-0">
                    Saveurs authentiques, ingrédients frais du jour.
                    Commande simple, livraison rapide à Dakar.
                </p>

                {{-- CTAs --}}
                <div class="flex flex-col sm:flex-row flex-wrap justify-center lg:justify-start gap-3 mb-8">
                    <a href="{{ route('shop.catalogue') }}"
                       class="group inline-flex items-center justify-center gap-2.5
                              px-7 py-3.5 sm:px-8 sm:py-4 rounded-full
                              font-bold text-sm sm:text-base text-white transition-all duration-300
                              shadow-lg hover:shadow-xl hover:scale-[1.03] active:scale-100"
                       style="background: linear-gradient(135deg, #F27B20, #E86A10);">
                        Explorer le Menu
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    @if($parametres->whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $parametres->whatsapp) }}"
                       target="_blank"
                       class="inline-flex items-center justify-center gap-2
                              px-6 py-3.5 sm:px-7 sm:py-4 rounded-full
                              font-bold text-sm border-2 border-gray-200 text-dark
                              hover:border-green-500 hover:text-green-600
                              transition-all duration-300">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        WhatsApp
                    </a>
                    @endif
                </div>

                {{-- Trust badges --}}
                <div class="flex flex-wrap justify-center lg:justify-start gap-5 sm:gap-6">
                    @foreach([
                        ['icon' => '🚚', 'val' => 'Rapide', 'label' => 'Livraison'],
                        ['icon' => '🍽️', 'val' => 'Frais', 'label' => 'Chaque jour'],
                        ['icon' => '⭐', 'val' => $avisMoyenne ? $avisMoyenne . '/5' : '★★★★★', 'label' => $avisTotal > 0 ? $avisTotal . ' avis' : 'Avis clients'],
                    ] as $badge)
                        <div class="flex items-center gap-2">
                            <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white
                                         flex items-center justify-center text-sm sm:text-base
                                         shadow-sm border border-gray-100">
                                {{ $badge['icon'] }}
                            </span>
                            <div>
                                <p class="text-dark text-xs sm:text-sm font-bold leading-tight">{{ $badge['val'] }}</p>
                                <p class="text-gray-400 text-[10px] sm:text-xs">{{ $badge['label'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── CARROUSEL FONDU (droite) ── --}}
            <div class="relative flex items-center justify-center
                        py-6 sm:py-10 lg:py-0 lg:min-h-[560px]">

                {{-- Forme colorée derrière --}}
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                            w-[280px] h-[280px] sm:w-[360px] sm:h-[360px] lg:w-[440px] lg:h-[440px]
                            rounded-full pointer-events-none"
                     style="background: linear-gradient(145deg, #F27B20 0%, #FFB347 60%, #FCD34D 100%);
                            opacity: 0.15;"></div>

                {{-- Forme blob organique --}}
                <div class="absolute top-1/2 left-1/2 -translate-x-[45%] -translate-y-[48%]
                            w-[260px] h-[260px] sm:w-[340px] sm:h-[340px] lg:w-[420px] lg:h-[420px]
                            pointer-events-none hero-morph"
                     style="background: linear-gradient(135deg, #F27B20 0%, #FB923C 100%);
                            border-radius: 60% 40% 55% 45% / 50% 60% 40% 50%;
                            opacity: 0.12;"></div>

                @if($carouselImages->count() > 1)
                    {{-- Carrousel fondu Alpine --}}
                    <div class="relative z-20"
                         x-data="{
                            current: 0,
                            images: {{ $carouselImages->map(fn($img) => Storage::url($img))->toJson() }},
                            total: {{ $carouselImages->count() }},
                            init() {
                                setInterval(() => {
                                    this.current = (this.current + 1) % this.total;
                                }, 4000);
                            }
                         }">
                        <div class="relative w-52 h-52 sm:w-64 sm:h-64 md:w-72 md:h-72
                                    lg:w-[360px] lg:h-[360px] xl:w-[400px] xl:h-[400px]">
                            <template x-for="(img, index) in images" :key="index">
                                <img :src="img"
                                     alt="Plat vedette"
                                     class="absolute inset-0 w-full h-full object-contain
                                            drop-shadow-[0_20px_50px_rgba(242,123,32,0.2)]
                                            transition-opacity duration-1000 ease-in-out"
                                     :class="current === index ? 'opacity-100' : 'opacity-0'"/>
                            </template>
                        </div>

                        {{-- Indicateurs --}}
                        <div class="flex justify-center gap-2 mt-4">
                            <template x-for="(img, index) in images" :key="'dot-'+index">
                                <button @click="current = index"
                                        class="w-2 h-2 rounded-full transition-all duration-300"
                                        :class="current === index
                                            ? 'bg-[#F27B20] w-6'
                                            : 'bg-gray-300 hover:bg-gray-400'">
                                </button>
                            </template>
                        </div>
                    </div>
                @elseif($platVedette?->image_principale)
                    {{-- Image unique --}}
                    <div class="relative z-20">
                        <img src="{{ Storage::url($platVedette->image_principale) }}"
                             alt="{{ $platVedette->nom }}"
                             class="w-52 h-52 sm:w-64 sm:h-64 md:w-72 md:h-72 lg:w-[360px] lg:h-[360px] xl:w-[400px] xl:h-[400px]
                                    object-contain
                                    drop-shadow-[0_20px_50px_rgba(242,123,32,0.2)]"/>
                    </div>
                @else
                    <div class="relative z-20 w-52 h-52 sm:w-64 sm:h-64 lg:w-80 lg:h-80
                                rounded-full flex items-center justify-center"
                         style="background: linear-gradient(135deg, rgba(242,123,32,0.08), rgba(242,123,32,0.03));">
                        <div class="text-center">
                            <span class="text-5xl sm:text-6xl">🍽️</span>
                            <p class="text-gray-400 text-xs font-medium mt-2">Plat vedette</p>
                        </div>
                    </div>
                @endif

                {{-- Badge flottant --}}
                <div class="absolute bottom-6 right-2 sm:bottom-10 sm:right-6 lg:bottom-20 lg:right-0 z-40 hero-float hidden sm:block">
                    <div class="bg-white rounded-xl shadow-lg px-3 py-2 flex items-center gap-2 border border-gray-100">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm"
                             style="background: #FEF3C7;">⭐</div>
                        <div>
                            <p class="text-[11px] font-bold text-dark leading-tight">{{ $avisMoyenne ? $avisMoyenne . '/5' : '★★★★★' }}</p>
                            <p class="text-[9px] text-gray-400">{{ $avisTotal > 0 ? $avisTotal . ' avis' : 'Nos clients' }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Transition --}}
    <div class="absolute bottom-0 left-0 right-0 h-16 pointer-events-none"
         style="background: linear-gradient(to top, #F9FAFB, transparent);"></div>
</section>

{{-- Hero styles --}}
<style>
    @keyframes heroFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .hero-float { animation: heroFloat 4s ease-in-out infinite; }
    @keyframes heroMorph {
        0%   { border-radius: 60% 40% 55% 45% / 50% 60% 40% 50%; }
        50%  { border-radius: 45% 55% 40% 60% / 60% 40% 55% 45%; }
        100% { border-radius: 60% 40% 55% 45% / 50% 60% 40% 50%; }
    }
    .hero-morph { animation: heroMorph 8s ease-in-out infinite; }
    @media (prefers-reduced-motion: reduce) {
        .hero-float, .hero-morph { animation: none; }
    }
</style>

{{-- ── CARROUSEL PRODUITS ───────────────────── --}}
@if($platCarousel->count() > 0)
<section class="py-8 sm:py-12 overflow-hidden bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 sm:mb-8" data-reveal="fade">
        <h2 class="text-lg sm:text-2xl font-bold text-dark text-center">
            Découvrez nos produits
        </h2>
        <p class="text-gray-400 text-xs sm:text-sm mt-1 text-center">
            Une sélection variée pour tous les goûts
        </p>
    </div>

    <div class="relative">
        {{-- Dégradés latéraux --}}
        <div class="absolute left-0 top-0 bottom-0 w-12 sm:w-24 z-10
                    pointer-events-none"
             style="background: linear-gradient(to right, white, transparent);"></div>
        <div class="absolute right-0 top-0 bottom-0 w-12 sm:w-24 z-10
                    pointer-events-none"
             style="background: linear-gradient(to left, white, transparent);"></div>

        <div class="flex gap-3 sm:gap-4 animate-scroll"
             style="width: max-content;">
            @foreach([$platCarousel, $platCarousel, $platCarousel, $platCarousel] as $batch)
                @foreach($batch as $plat)
                    <a href="{{ route('shop.plat', $plat->slug) }}"
                       class="group flex-shrink-0 w-40 sm:w-52 bg-white rounded-2xl border
                              border-gray-100 overflow-hidden hover:shadow-lg
                              hover:-translate-y-1 transition-all duration-300">
                        {{-- Image --}}
                        <div class="aspect-square bg-gray-50 overflow-hidden">
                            @if($plat->image_principale)
                                <img src="{{ Storage::url($plat->image_principale) }}"
                                     alt="{{ $plat->nom }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500"/>
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="text-3xl">🍽️</span>
                                </div>
                            @endif
                        </div>
                        {{-- Infos --}}
                        <div class="p-2.5 sm:p-3">
                            <p class="text-[10px] sm:text-xs text-primary font-medium truncate">
                                {{ $plat->categorie->nom ?? '' }}
                            </p>
                            <p class="text-xs sm:text-sm font-semibold text-dark truncate mt-0.5">
                                {{ $plat->nom }}
                            </p>
                            <p class="text-xs sm:text-sm font-bold text-dark mt-1">
                                {{ number_format($plat->prix_vente, 0, ',', ' ') }}
                                <span class="text-[10px] font-normal text-gray-400">FCFA</span>
                            </p>
                        </div>
                    </a>
                @endforeach
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── PLATS VEDETTES ───────────────────── --}}
@if($misEnAvant->count() > 0)
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <div class="flex items-center justify-between mb-5 sm:mb-8" data-reveal="up">
        <div>
            <h2 class="text-lg sm:text-2xl font-bold text-dark">
                ⭐ Plats vedettes
            </h2>
            <p class="text-gray-400 text-xs sm:text-sm mt-0.5 sm:mt-1">
                Nos meilleures sélections
            </p>
        </div>
        <a href="{{ route('shop.catalogue') }}"
           class="text-xs sm:text-sm font-medium text-primary hover:underline flex-shrink-0 ml-4">
            Voir tout →
        </a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6" data-reveal="up" data-reveal-delay="100">
        @foreach($misEnAvant as $plat)
            @include('shop.partials.plat-card', compact('plat'))
        @endforeach
    </div>
</section>
@endif

{{-- ── NOUVELLES ARRIVÉES (commenté) ──────────────────── --}}
{{--
@if($nouveautes->count() > 0)
<section class="bg-white py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-5 sm:mb-8" data-reveal="up">
            <div>
                <h2 class="text-lg sm:text-2xl font-bold text-dark">🆕 Nouvelles arrivées</h2>
                <p class="text-gray-400 text-xs sm:text-sm mt-0.5 sm:mt-1">
                    Les derniers plats disponibles
                </p>
            </div>
            <a href="{{ route('shop.catalogue', ['sort' => 'latest']) }}"
               class="text-xs sm:text-sm font-medium text-primary hover:underline flex-shrink-0 ml-4">
                Voir tout →
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6" data-reveal="up" data-reveal-delay="100">
            @foreach($nouveautes as $plat)
                @include('shop.partials.plat-card', compact('plat'))
            @endforeach
        </div>
    </div>
</section>
@endif
--}}

{{-- ── SECTIONS PAR CATÉGORIE ─────────────── --}}
@foreach($sectionsCategories as $index => $section)
<section class="{{ $index % 2 === 0 ? 'bg-[#FFF9F4]' : 'bg-white' }} py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-5 sm:mb-8" data-reveal="up">
            <div>
                <h2 class="text-lg sm:text-2xl font-bold text-dark">
                    {{ $section['categorie']->nom }}
                </h2>
                @if($section['categorie']->description)
                    <p class="text-gray-400 text-xs sm:text-sm mt-0.5 sm:mt-1">
                        {{ $section['categorie']->description }}
                    </p>
                @endif
            </div>
            <a href="{{ route('shop.catalogue', ['categorie' => $section['categorie']->slug]) }}"
               class="text-xs sm:text-sm font-medium text-primary hover:underline flex-shrink-0 ml-4">
                Voir tout →
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6" data-reveal="up" data-reveal-delay="100">
            @foreach($section['plats'] as $plat)
                @include('shop.partials.plat-card', compact('plat'))
            @endforeach
        </div>
    </div>
</section>
@endforeach

{{-- ── AVIS DÉFILANTS ─────────────────────── --}}
@php
    $avis = App\Models\Avis::with('plat')
        ->where('est_approuve', true)
        ->latest()
        ->limit(10)
        ->get();
@endphp

@if($avis->count() > 0)
<section class="py-8 sm:py-12 overflow-hidden bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 sm:mb-8" data-reveal="fade">
        <h2 class="text-lg sm:text-2xl font-bold text-dark text-center">
            Ce que disent nos clients
        </h2>
        <p class="text-gray-400 text-xs sm:text-sm mt-1 text-center">
            Des milliers de clients satisfaits à Dakar
        </p>
    </div>

    {{-- Carrousel infini --}}
    <div class="relative">
        {{-- Gradient fade gauche --}}
        <div class="absolute left-0 top-0 bottom-0 w-8 sm:w-24 z-10
                    bg-gradient-to-r from-gray-50 to-transparent pointer-events-none">
        </div>
        {{-- Gradient fade droite --}}
        <div class="absolute right-0 top-0 bottom-0 w-8 sm:w-24 z-10
                    bg-gradient-to-l from-gray-50 to-transparent pointer-events-none">
        </div>

        {{-- Track défilant --}}
        <div class="flex gap-3 sm:gap-4 animate-scroll px-4 sm:px-0"
             style="width: max-content;">

            {{-- On duplique pour l'effet infini --}}
            @foreach([$avis, $avis] as $batch)
                @foreach($batch as $review)
                    <div class="w-60 sm:w-72 flex-shrink-0 bg-white rounded-xl sm:rounded-2xl border
                                border-gray-100 shadow-sm p-4 sm:p-5">

                        {{-- Étoiles --}}
                        <div class="flex items-center gap-0.5 sm:gap-1 mb-2 sm:mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 {{ $i <= $review->note
                                    ? 'text-yellow-400' : 'text-gray-200' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902
                                             0l1.07 3.292a1 1 0 00.95.69h3.462c.969
                                             0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0
                                             00-.364 1.118l1.07 3.292c.3.921-.755
                                             1.688-1.54 1.118l-2.8-2.034a1 1 0
                                             00-1.175 0l-2.8 2.034c-.784.57-1.838
                                             -.197-1.539-1.118l1.07-3.292a1 1 0
                                             00-.364-1.118L2.98 8.72c-.783-.57
                                             -.38-1.81.588-1.81h3.461a1 1 0
                                             00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>

                        {{-- Commentaire --}}
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed mb-3 sm:mb-4 line-clamp-3">
                            "{{ $review->commentaire }}"
                        </p>

                        {{-- Auteur --}}
                        <div class="flex items-center gap-2.5 sm:gap-3">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-primary flex items-center
                                        justify-center text-white font-bold text-[10px] sm:text-xs
                                        flex-shrink-0">
                                {{ strtoupper(substr($review->nom_auteur, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm font-semibold text-dark truncate">
                                    {{ $review->nom_auteur }}
                                </p>
                                <p class="text-[10px] sm:text-xs text-gray-400 truncate">
                                    {{ $review->plat->nom }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── FAQ ─────────────────────────────────── --}}
<section class="py-10 sm:py-16 lg:py-20" style="background: #F9FAFB;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- En-tête --}}
        <div class="text-center mb-8 sm:mb-12" data-reveal="up">
            <div class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1 sm:py-1.5 rounded-full text-[10px] sm:text-xs font-semibold
                        uppercase tracking-widest mb-3 sm:mb-4"
                 style="background: rgba(242,123,32,0.08); color: #F27B20;">
                ❓ Questions fréquentes
            </div>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-dark mb-2 sm:mb-3">
                Tout ce que vous devez savoir
            </h2>
            <p class="text-gray-500 text-sm sm:text-base max-w-xl mx-auto">
                Des réponses claires à vos questions les plus fréquentes.
            </p>
        </div>

        {{-- Accordéon --}}
        <div x-data="{ open: null }" class="space-y-3" data-reveal="up" data-reveal-delay="150">

            @php
            $faqs = [
                ['q' => 'Comment passer une commande ?',
                 'r' => 'Ajoutez les articles souhaités dans votre panier depuis le catalogue, puis cliquez sur "Commander". Renseignez vos coordonnées, choisissez votre mode de paiement et confirmez. Vous recevrez une confirmation avec le numéro de votre commande.',
                 'icon' => '🛒'],
                ['q' => 'Quels modes de paiement acceptez-vous ?',
                 'r' => 'Nous acceptons Wave, Orange Money, Mix by Yass, Visa et Mastercard. Le choix du mode de paiement se fait directement sur la page de paiement sécurisée PayDunya après confirmation de votre commande.',
                 'icon' => '💳'],
                ['q' => 'Quelles sont les zones et délais de livraison ?',
                 'r' => 'Nous livrons dans tout Dakar et ses environs sous 24 à 48h après confirmation. Pour les banlieues (Pikine, Guédiawaye, Rufisque, Thiès) et autres régions, comptez 2 à 5 jours ouvrables.',
                 'icon' => '🚚'],
                ['q' => 'Puis-je récupérer ma commande sur place ?',
                 'r' => 'Oui, le retrait au restaurant est gratuit et disponible dès que votre commande est prête. Vous serez contacté par téléphone dès que c\'est prêt.',
                 'icon' => '🏪'],
                ['q' => 'Les plats sont-ils frais et faits maison ?',
                 'r' => 'Absolument. Tous nos plats sont préparés le jour même avec des ingrédients frais provenant de fournisseurs locaux sélectionnés. Nous garantissons qualité et fraîcheur à chaque commande.',
                 'icon' => '✅'],
                ['q' => 'Puis-je modifier ou annuler ma commande ?',
                 'r' => 'Les modifications sont possibles tant que votre commande est au statut "En attente". Contactez-nous rapidement par WhatsApp ou par téléphone. Une fois confirmée et en préparation, il n\'est plus possible de la modifier.',
                 'icon' => '✏️'],
            ];
            @endphp

            @foreach($faqs as $i => $faq)
                <div class="bg-white rounded-2xl border overflow-hidden transition-all duration-200"
                     :class="open === {{ $i }} ? 'border-primary/30 shadow-md shadow-primary/5' : 'border-gray-100 shadow-sm'">

                    {{-- Question --}}
                    <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                            class="w-full flex items-center gap-2.5 sm:gap-4 px-4 py-3.5 sm:px-6 sm:py-5 text-left group">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl flex items-center justify-center text-sm sm:text-lg shrink-0 transition-colors"
                             :class="open === {{ $i }} ? 'bg-primary/10' : 'bg-gray-50 group-hover:bg-primary/5'">
                            {{ $faq['icon'] }}
                        </div>
                        <span class="flex-1 text-xs sm:text-sm font-semibold transition-colors leading-snug"
                              :class="open === {{ $i }} ? 'text-primary' : 'text-dark group-hover:text-primary'">
                            {{ $faq['q'] }}
                        </span>
                        <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center shrink-0 transition-all duration-200"
                             :class="open === {{ $i }} ? 'bg-primary rotate-45' : 'bg-gray-100 group-hover:bg-gray-200'">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 transition-colors"
                                 :class="open === {{ $i }} ? 'text-white' : 'text-gray-500'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2.5" d="M12 4v16M4 12h16"/>
                            </svg>
                        </div>
                    </button>

                    {{-- Réponse --}}
                    <div x-show="open === {{ $i }}"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1">
                        <div class="px-4 pb-4 pl-[2.75rem] sm:px-6 sm:pb-5 sm:pl-[4.25rem]">
                            <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">
                                {{ $faq['r'] }}
                            </p>
                        </div>
                    </div>

                </div>
            @endforeach

        </div>

        {{-- Lien vers FAQ complète --}}
        <div class="text-center mt-8">
            <a href="{{ route('shop.faq') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold transition-colors"
               style="color: #F27B20;">
                Voir toutes les questions fréquentes
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

    </div>
</section>

{{-- ── BANNIÈRE CTA ─────────────────────────── --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12" data-reveal="up">
    <div class="rounded-2xl sm:rounded-3xl p-6 sm:p-8 lg:p-12 text-center"
         style="background: linear-gradient(135deg, #B8510D 0%, #F27B20 100%);">
        <h2 class="text-lg sm:text-2xl lg:text-3xl font-bold text-white mb-2 sm:mb-3">
            Vous ne trouvez pas ce que vous cherchez ?
        </h2>
        <p class="text-white/70 text-sm sm:text-base mb-4 sm:mb-6">
            Contactez-nous directement sur WhatsApp, nous trouverons la solution.
        </p>
            @if($parametres->whatsapp)
            @php
                $wa = preg_replace('/[^0-9]/', '', $parametres->whatsapp);
                $sujets = [
                    ['label' => '🛒 Passer une commande',       'msg' => "Bonjour *{$parametres->nom}* 👋\nJe souhaite passer une commande. Pouvez-vous m'aider ?"],
                    ['label' => '📦 Disponibilité d\'un plat',   'msg' => "Bonjour *{$parametres->nom}* 👋\nJe voudrais savoir si un plat est disponible."],
                    ['label' => '💰 Demande de prix',            'msg' => "Bonjour *{$parametres->nom}* 👋\nJe souhaite avoir des informations sur vos tarifs."],
                    ['label' => '🚚 Infos livraison',            'msg' => "Bonjour *{$parametres->nom}* 👋\nJe voudrais des informations sur vos délais et frais de livraison."],
                    ['label' => '🔧 Réclamation',                'msg' => "Bonjour *{$parametres->nom}* 👋\nJ'ai besoin d'assistance concernant ma commande."],
                    ['label' => '❓ Autre question',              'msg' => "Bonjour *{$parametres->nom}* 👋\nJ'ai une question à vous poser."],
                ];
            @endphp

            <div x-data="{ open: false }" class="flex flex-col items-center">

                {{-- Bouton principal --}}
                <button @click="open = !open"
                        class="inline-flex items-center gap-3 bg-green-500 hover:bg-green-400
                            text-white font-semibold px-8 py-4 rounded-2xl transition-all
                            shadow-xl text-sm">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Contacter sur WhatsApp
                    <svg :class="open ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform duration-200"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Liste sujets --}}
                <div x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    @click.outside="open = false"
                    class="mt-3 bg-white/10 backdrop-blur-sm border border-white/20
                            rounded-2xl p-3 w-full max-w-sm space-y-2">

                    <p class="text-xs font-semibold text-white/70 mb-1 px-1">
                        Choisissez un sujet :
                    </p>

                    @foreach($sujets as $sujet)
                        <a href="https://wa.me/{{ $wa }}?text={{ urlencode($sujet['msg']) }}"
                        target="_blank"
                        class="flex items-center justify-between gap-2 w-full px-4 py-2.5
                                bg-white/10 hover:bg-white/20 text-white text-sm font-medium
                                rounded-xl transition-all group">
                            <span>{{ $sujet['label'] }}</span>
                            <svg class="w-3.5 h-3.5 text-white/50 group-hover:text-white
                                        group-hover:translate-x-1 transition-all flex-shrink-0"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>
    @endif
    </div>
</section>

@endsection