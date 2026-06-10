@extends('layouts.app')

@section('title', 'Nouveau plat')
@section('page-title', 'Nouveau plat')
@section('page-subtitle', 'Ajouter un plat au menu')

@section('content')
    @livewire('plats.plat-form')
@endsection