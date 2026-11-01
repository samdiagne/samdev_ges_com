@extends('layouts.app')

@section('title', 'Modifier le produit')
@section('page-title', 'Modifier le produit')
@section('page-subtitle', 'Mise à jour des informations du produit')

@section('content')
    @livewire('produits.produit-form', ['produitId' => $produit->id, 'returnUrl' => $returnUrl ?? null])
@endsection