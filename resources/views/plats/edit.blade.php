@extends('layouts.app')

@section('title', 'Modifier le plat')
@section('page-title', 'Modifier le plat')
@section('page-subtitle', 'Mise à jour des informations du plat')

@section('content')
    @livewire('plats.plat-form', ['platId' => $plat->id])
@endsection