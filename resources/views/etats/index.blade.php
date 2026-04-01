@extends('layouts.app')
@section('title', 'États')
@section('page-title', 'États financiers')
@section('page-subtitle', 'Génération de rapports PDF imprimables')
@section('content')
    @livewire('etats.etat-manager')
@endsection