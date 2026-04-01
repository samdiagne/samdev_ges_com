@extends('layouts.app')
@section('title', 'Commandes')
@section('page-title', 'Commandes')
@section('page-subtitle', 'Gestion des commandes en ligne')
@section('content')
    @livewire('commandes.commande-manager')
@endsection