@extends('layouts.app')

@section('title', 'Nouveau produit')
@section('page-title', 'Nouveau produit')
@section('page-subtitle', 'Ajouter un produit au catalogue')

@section('content')
    @livewire('produits.produit-form', ['returnUrl' => $returnUrl ?? null])
@endsection