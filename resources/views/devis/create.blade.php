@extends('layouts.app')
@section('title', 'Nouveau devis')
@section('page-title', 'Nouveau devis')
@section('page-subtitle', 'Créer une proposition commerciale')
@section('content')
    <div class="h-full w-full overflow-hidden flex flex-col min-h-0 flex-1">
        @livewire('devis.devis-form')
    </div>
@endsection
