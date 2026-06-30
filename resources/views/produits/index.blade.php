@extends('layouts.app')

@section('title', 'Produits')
@section('page-title', 'Produits')
@section('page-subtitle', 'Gestion du catalogue')

@section('content')
    @livewire('produits.produit-manager')
@endsection