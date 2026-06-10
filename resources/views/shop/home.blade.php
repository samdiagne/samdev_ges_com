@extends('layouts.shop')

@section('title', $parametres->nom . ' — Restaurant & Livraison à Dakar')
@section('description', 'Découvrez nos plats faits maison à Dakar. Commande en ligne, livraison rapide, qualité garantie.')

@section('content')

{{-- ── HERO ───────────────────────────────── --}}
<section class="relative overflow-hidden -mt-1"
         style="background: linear-gradient(135deg, #B8510D 0%, #F27B20 55%, #FF9F43 100%);">

    {{-- Cercles décoratifs d'arrière-plan --}}
    <div class="absolute -top-20 -right-20 w-96 h-96 rounded-full pointer-events-none"
         style="background: radial-gradient(circle, rgba(242,123,32,0.18), transparent 70%);"></div>
    <div class="absolute -bottom-10 -left-16 w-72 h-72 rounded-full pointer-events-none"
         style="background: radial-gradient(circle, rgba(255,255,255,0.08), transparent 70%);"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 lg:py-16 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">

            {{-- TEXTE --}}
            <div class="text-center lg:text-left">

                <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm
                            text-white px-4 py-1.5 rounded-full text-xs font-semibold mb-5
                            tracking-wide uppercase border border-white/20">
                    🔥 Nouvelles arrivées disponibles
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] mb-5">
                    Le Goût à<br/>
                    <span style="color: #2DB667;">Votre Porte</span>
                </h1>

                <p class="text-white/75 text-base sm:text-lg mb-8 leading-relaxed max-w-md mx-auto lg:mx-0">
                    Plats faits maison, saveurs authentiques —
                    qualité garantie, commande en ligne, livraison rapide à Dakar.
                </p>

                {{-- CTA --}}
                <div class="flex flex-wrap justify-center lg:justify-start gap-3 mb-8">
                    <a href="{{ route('shop.catalogue') }}"
                       class="inline-flex items-center gap-2 px-7 py-3.5 rounded-2xl
                              font-bold text-sm transition-all shadow-xl
                              hover:scale-105 hover:brightness-110 active:scale-100"
                       style="background-color: #2DB667; color: white;">
                        🛒 Voir le menu
                    </a>

                    <a href="{{ route('shop.contact') }}"
                       class="inline-flex items-center gap-2 px-7 py-3.5 rounded-2xl
                              font-bold text-sm border border-white/30 text-white
                              hover:bg-white/15 transition-all backdrop-blur-sm">
                        💬 Nous contacter
                    </a>
                </div>

                {{-- Badges de confiance --}}
                <div class="flex flex-wrap justify-center lg:justify-start gap-5">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center shrink-0">🚚</div>
                        <span class="text-white/80 text-sm font-medium">Livraison 24h</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center shrink-0">✅</div>
                        <span class="text-white/80 text-sm font-medium">Qualité garantie</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center shrink-0">⭐</div>
                        <span class="text-white/80 text-sm font-medium">+1000 clients satisfaits</span>
                    </div>
                </div>
            </div>

            {{-- IMAGE --}}
            <div class="flex justify-center lg:justify-end">
                <div class="relative">

                    {{-- Anneau décoratif extérieur --}}
                    <div class="absolute inset-0 rounded-[2.5rem] border-2 border-white/15
                                scale-[1.07] pointer-events-none"></div>

                    {{-- Conteneur image : taille fixe, overflow hidden --}}
                    <div class="w-72 h-72 sm:w-80 sm:h-80 lg:w-[440px] lg:h-[440px]
                                rounded-[2rem] bg-white/10 backdrop-blur-xl border border-white/20
                                flex items-center justify-center overflow-hidden shadow-2xl relative">

                        <div class="absolute inset-0 bg-gradient-to-br from-white/20 via-transparent to-orange-900/20 pointer-events-none"></div>

                        @php $platVedette = $misEnAvant->first(); @endphp

                        @if($platVedette?->image_principale)
                            <img src="{{ Storage::url($platVedette->image_principale) }}"
                                 alt="{{ $platVedette->nom }}"
                                 class="relative z-10 w-4/5 h-4/5 object-contain
                                        drop-shadow-[0_20px_40px_rgba(0,0,0,0.35)]
                                        hover:scale-105 transition-transform duration-500
                                        animate-[float_4s_ease-in-out_infinite]"/>
                        @else
                            <svg class="w-28 h-28 text-white/30" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1"
                                      d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        @endif
                    </div>

                    {{-- Badge haut droite --}}
                    <div class="absolute -top-4 -right-3 lg:-right-5 bg-white rounded-2xl shadow-xl
                                px-3 py-1.5 flex items-center gap-1.5">
                        <span>⚡</span>
                        <span class="text-xs font-bold text-primary">Nouveautés</span>
                    </div>

                    {{-- Badge bas gauche --}}
                    <div class="absolute -bottom-4 -left-3 lg:-left-5 bg-white rounded-2xl shadow-xl
                                px-3 py-1.5 flex items-center gap-1.5">
                        <span>🔥</span>
                        <span class="text-xs font-bold" style="color: #2DB667;">Meilleurs prix</span>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- Vague --}}
    <div class="absolute bottom-[-1px] left-0 right-0">
        <svg class="block w-full" viewBox="0 0 1440 60" fill="none">
            <path d="M0 60L1440 60L1440 0C1440 0 1080 60 720 60C360 60 0 0 0 0L0 60Z"
                fill="#F9FAFB"/>
        </svg>
    </div>
</section>

{{-- Animation float --}}
<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-12px); }
}
</style>

{{-- ── CATÉGORIES ──────────────────────────── --}}
@if($categories->count() > 0)
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-center justify-between mb-8" data-reveal="up">
        <div>
            <h2 class="text-2xl font-bold text-dark">Nos catégories</h2>
            <p class="text-gray-400 text-sm mt-1">
                Trouvez ce que vous cherchez rapidement en filtrant directement la catégorie qui vous interesse
            </p>
        </div>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4" data-reveal="up" data-reveal-delay="100">
        @foreach($categories as $category)
            <a href="{{ route('shop.catalogue', ['categorie' => $category->slug]) }}"
               class="group bg-white rounded-2xl p-4 text-center hover:shadow-lg
                      hover:-translate-y-1 transition-all duration-200 border
                      border-gray-100">
                <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center
                            justify-center mx-auto mb-3 group-hover:bg-primary/20
                            transition-colors">
                    @if($category->image)
                        <img src="{{ Storage::url($category->image) }}"
                             class="w-8 h-8 object-contain"/>
                    @else
                        <svg class="w-6 h-6 text-primary" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0
                                     00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    @endif
                </div>
                <p class="text-xs font-semibold text-dark group-hover:text-primary
                          transition-colors">
                    {{ $category->nom }}
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $category->plats_actifs_count }} plat(s)
                </p>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ── PLATS VEDETTES ───────────────────── --}}
@if($misEnAvant->count() > 0)
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-center justify-between mb-8" data-reveal="up">
        <div>
            <h2 class="text-2xl font-bold text-dark">
                ⭐ Plats vedettes
            </h2>
            <p class="text-gray-400 text-sm mt-1">
                Nos meilleures sélections
            </p>
        </div>
        <a href="{{ route('shop.catalogue') }}"
           class="text-sm font-medium text-primary hover:underline">
            Voir tout →
        </a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6" data-reveal="up" data-reveal-delay="100">
        @foreach($misEnAvant as $plat)
            @include('shop.partials.plat-card', compact('plat'))
        @endforeach
    </div>
</section>
@endif

{{-- ── NOUVELLES ARRIVÉES ──────────────────── --}}
@if($nouveautes->count() > 0)
<section class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8" data-reveal="up">
            <div>
                <h2 class="text-2xl font-bold text-dark">🆕 Nouvelles arrivées</h2>
                <p class="text-gray-400 text-sm mt-1">
                    Les derniers plats disponibles
                </p>
            </div>
            <a href="{{ route('shop.catalogue', ['sort' => 'latest']) }}"
               class="text-sm font-medium text-primary hover:underline">
                Voir tout →
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6" data-reveal="up" data-reveal-delay="100">
            @foreach($nouveautes as $plat)
                @include('shop.partials.plat-card', compact('plat'))
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── AVIS DÉFILANTS ─────────────────────── --}}
@php
    $avis = App\Models\Avis::with('plat')
        ->where('est_approuve', true)
        ->latest()
        ->limit(10)
        ->get();
@endphp

@if($avis->count() > 0)
<section class="py-12 overflow-hidden bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8" data-reveal="fade">
        <h2 class="text-2xl font-bold text-dark text-center">
            Ce que disent nos clients
        </h2>
        <p class="text-gray-400 text-sm mt-1 text-center">
            Des milliers de clients satisfaits à Dakar
        </p>
    </div>

    {{-- Carrousel infini --}}
    <div class="relative">
        {{-- Gradient fade gauche --}}
        <div class="absolute left-0 top-0 bottom-0 w-24 z-10
                    bg-gradient-to-r from-gray-50 to-transparent pointer-events-none">
        </div>
        {{-- Gradient fade droite --}}
        <div class="absolute right-0 top-0 bottom-0 w-24 z-10
                    bg-gradient-to-l from-gray-50 to-transparent pointer-events-none">
        </div>

        {{-- Track défilant --}}
        <div class="flex gap-4 animate-scroll"
             style="width: max-content;">

            {{-- On duplique pour l'effet infini --}}
            @foreach([$avis, $avis] as $batch)
                @foreach($batch as $review)
                    <div class="w-72 flex-shrink-0 bg-white rounded-2xl border
                                border-gray-100 shadow-sm p-5">

                        {{-- Étoiles --}}
                        <div class="flex items-center gap-1 mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review->note
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
                        <p class="text-sm text-gray-600 leading-relaxed mb-4 line-clamp-3">
                            "{{ $review->commentaire }}"
                        </p>

                        {{-- Auteur --}}
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-primary flex items-center
                                        justify-center text-white font-bold text-xs
                                        flex-shrink-0">
                                {{ strtoupper(substr($review->nom_auteur, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-dark">
                                    {{ $review->nom_auteur }}
                                </p>
                                <p class="text-xs text-gray-400">
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
<section class="py-16 sm:py-20" style="background: #F9FAFB;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- En-tête --}}
        <div class="text-center mb-12" data-reveal="up">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold
                        uppercase tracking-widest mb-4"
                 style="background: rgba(30,111,191,0.08); color: #F27B20;">
                ❓ Questions fréquentes
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-dark mb-3">
                Tout ce que vous devez savoir
            </h2>
            <p class="text-gray-500 text-base max-w-xl mx-auto">
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
                            class="w-full flex items-center gap-4 px-6 py-5 text-left group">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 transition-colors"
                             :class="open === {{ $i }} ? 'bg-primary/10' : 'bg-gray-50 group-hover:bg-primary/5'">
                            {{ $faq['icon'] }}
                        </div>
                        <span class="flex-1 text-sm font-semibold transition-colors"
                              :class="open === {{ $i }} ? 'text-primary' : 'text-dark group-hover:text-primary'">
                            {{ $faq['q'] }}
                        </span>
                        <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 transition-all duration-200"
                             :class="open === {{ $i }} ? 'bg-primary rotate-45' : 'bg-gray-100 group-hover:bg-gray-200'">
                            <svg class="w-3 h-3 transition-colors"
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
                        <div class="px-6 pb-5 pl-[4.25rem]">
                            <p class="text-sm text-gray-500 leading-relaxed">
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
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" data-reveal="up">
    <div class="rounded-3xl p-8 lg:p-12 text-center"
         style="background: linear-gradient(135deg, #B8510D 0%, #F27B20 100%);">
        <h2 class="text-2xl lg:text-3xl font-bold text-white mb-3">
            Vous ne trouvez pas ce que vous cherchez ?
        </h2>
        <p class="text-white/70 mb-6">
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