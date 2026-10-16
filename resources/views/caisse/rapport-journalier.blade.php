@extends('layouts.app')

@section('title', 'Rapport Quotidien de Caisse')
@section('page-title', 'Rapport Quotidien')
@section('page-subtitle', 'Sessions de caisse, chiffre d\'affaires par mode de paiement et écarts')

@section('content')
    <div class="h-full w-full overflow-y-auto min-h-0 flex-1 px-4 sm:px-6 py-4">
        @livewire('caisse.rapport-journalier')
    </div>
@endsection
