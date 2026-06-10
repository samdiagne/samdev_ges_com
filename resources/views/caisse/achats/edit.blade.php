@extends('layouts.app')
@section('title', 'Modifier achat')
@section('page-title', 'Modifier l\'achat')
@section('page-subtitle', 'Mise à jour de la commande')
@section('content')
    @livewire('caisse.achat-form', ['achatId' => $achat->id])
@endsection