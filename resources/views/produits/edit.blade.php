@extends('layouts.app')

@section('title', 'Modifier le produit')
@section('page-title', 'Modifier le produit')
@section('page-subtitle', 'Mise à jour des informations')

@section('content')
    @livewire('products.product-form', ['productId' => $product->id])
@endsection