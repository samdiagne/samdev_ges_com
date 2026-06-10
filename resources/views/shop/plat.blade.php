@extends('layouts.shop')
@section('title', $plat->nom . ' — ' . $parametres->nom)
@section('description', Str::limit($plat->description ?? $plat->nom, 160))

@push('meta')
    @php
        $reviewCount = $plat->avisApprouves()->count();
        $jsonLd = [
            '@context'    => 'https://schema.org/',
            '@type'       => 'Product',
            'name'        => $plat->nom,
            'description' => Str::limit(strip_tags($plat->description ?? ''), 200),
            'brand'       => [
                '@type' => 'Brand',
                'name'  => $parametres->nom,
            ],
            'offers' => [
                '@type'         => 'Offer',
                'url'           => url()->current(),
                'priceCurrency' => 'XOF',
                'price'         => (string) $plat->prix_vente,
                'availability'  => $plat->stock > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name'  => $parametres->nom,
                ],
            ],
        ];
        if ($plat->image_principale) {
            $jsonLd['image'] = Storage::url($plat->image_principale);
        }
        if ($reviewCount > 0) {
            $jsonLd['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => (string) $plat->noteMoyenne(),
                'reviewCount' => $reviewCount,
            ];
        }
    @endphp

    {{-- OG spécifique plat --}}
    <meta property="og:type" content="product"/>
    @if($plat->image_principale)
        <meta property="og:image" content="{{ Storage::url($plat->image_principale) }}"/>
    @endif
    <meta property="product:price:amount"   content="{{ $plat->prix_vente }}"/>
    <meta property="product:price:currency" content="XOF"/>

    {{-- Schema.org Product JSON-LD --}}
    <script type="application/ld+json">
        {!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
        <a href="{{ route('shop.home') }}"
           class="hover:text-primary transition-colors">Accueil</a>
        <span>›</span>
        <a href="{{ route('shop.catalogue') }}"
           class="hover:text-primary transition-colors">Catalogue</a>
        @if($plat->categorie)
            <span>›</span>
            <a href="{{ route('shop.catalogue', ['categorie' => $plat->categorie->slug]) }}"
               class="hover:text-primary transition-colors">
                {{ $plat->categorie->nom }}
            </a>
        @endif
        <span>›</span>
        <span class="text-dark font-medium truncate max-w-48">{{ $plat->nom }}</span>
    </nav>

    {{-- Fiche plat --}}
    @livewire('shop.fiche-plat', ['plat' => $plat])

        {{-- Avis clients --}}
    @livewire('shop.plat-reviews', ['plat' => $plat])

    {{-- Plats similaires --}}
    @if($similaires->count() > 0)
        <section class="mt-16">
            <h2 class="text-xl font-bold text-dark mb-6">
                Vous aimerez aussi
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($similaires as $similaire)
                    @include('shop.partials.plat-card',
                        ['plat' => $similaire])
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection