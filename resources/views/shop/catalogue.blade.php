@extends('layouts.shop')
@section('title', 'Catalogue — ' . $parametres->nom)
@section('description', 'Explorez notre menu de plats faits maison à Dakar. Commandez en ligne.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @livewire('shop.catalogue-shop')
</div>
@endsection