@extends('layouts.app')
@section('title', 'Modifier le devis')
@section('page-title', 'Modifier le devis')
@section('page-subtitle', 'Mise à jour de la proposition')
@section('content')
    @livewire('devis.devis-form', ['quoteId' => $quote->id])
@endsection
