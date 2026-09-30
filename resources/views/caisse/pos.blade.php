@extends('layouts.app')

@section('title', 'Point de Vente')
@section('page-title', 'Point de Vente')
@section('page-subtitle', now()->translatedFormat('l d F Y'))

@section('content')
    <div class="h-full w-full overflow-hidden flex flex-col min-h-0 flex-1">
        @livewire('caisse.point-of-sale')
    </div>
@endsection