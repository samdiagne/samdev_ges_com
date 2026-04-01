@extends('layouts.app')

@section('title', 'Produits')
@section('page-title', 'Produits')
@section('page-subtitle', 'Gestion du catalogue produits')

@section('content')
    @livewire('products.product-manager')
@endsection