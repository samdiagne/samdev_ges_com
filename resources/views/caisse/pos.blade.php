@extends('layouts.app')

@section('title', 'Point de Vente')
@section('page-title', 'Point de Vente')
@section('page-subtitle', now()->translatedFormat('l d F Y'))

@section('content')
    <div class="h-[calc(100vh-8rem)]">
        @livewire('caisse.point-of-sale')
    </div>
@endsection