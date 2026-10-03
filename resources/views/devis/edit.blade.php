@extends('layouts.app')
@section('title', 'Modifier le devis')
@section('page-title', 'Modifier le devis')
@section('page-subtitle', 'Mise à jour de la proposition')
@section('content')
    <div class="h-full w-full overflow-hidden flex flex-col min-h-0 flex-1">
        @livewire('devis.devis-form', ['quoteId' => $quote->id])
    </div>
@endsection
