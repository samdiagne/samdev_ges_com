@extends('layouts.shop')
@section('title', $product->name . ' — ' . $settings->name)
@section('description', Str::limit($product->description ?? $product->name, 160))

@push('meta')
    @php
        $reviewCount = $product->approvedReviews()->count();
        $jsonLd = [
            '@context'    => 'https://schema.org/',
            '@type'       => 'Product',
            'name'        => $product->name,
            'description' => Str::limit(strip_tags($product->description ?? ''), 200),
            'sku'         => $product->sku,
            'brand'       => [
                '@type' => 'Brand',
                'name'  => $product->brand ?? $settings->name,
            ],
            'offers' => [
                '@type'         => 'Offer',
                'url'           => url()->current(),
                'priceCurrency' => 'XOF',
                'price'         => (string) $product->selling_price,
                'availability'  => $product->stock_quantity > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name'  => $settings->name,
                ],
            ],
        ];
        if ($product->main_image) {
            $jsonLd['image'] = Storage::url($product->main_image);
        }
        if ($reviewCount > 0) {
            $jsonLd['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => (string) $product->averageRating(),
                'reviewCount' => $reviewCount,
            ];
        }
    @endphp

    {{-- OG spécifique produit --}}
    <meta property="og:type" content="product"/>
    @if($product->main_image)
        <meta property="og:image" content="{{ Storage::url($product->main_image) }}"/>
    @endif
    <meta property="product:price:amount"   content="{{ $product->selling_price }}"/>
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
        @if($product->category)
            <span>›</span>
            <a href="{{ route('shop.catalogue', ['categorie' => $product->category->slug]) }}"
               class="hover:text-primary transition-colors">
                {{ $product->category->name }}
            </a>
        @endif
        <span>›</span>
        <span class="text-dark font-medium truncate max-w-48">{{ $product->name }}</span>
    </nav>

    {{-- Fiche produit --}}
    @livewire('shop.fiche-produit', ['product' => $product])

        {{-- Avis clients --}}
    @livewire('shop.product-reviews', ['product' => $product])

    {{-- Produits similaires --}}
    @if($related->count() > 0)
        <section class="mt-16">
            <h2 class="text-xl font-bold text-dark mb-6">
                Vous aimerez aussi
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($related as $relProduct)
                    @include('shop.partials.product-card',
                        ['product' => $relProduct])
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection