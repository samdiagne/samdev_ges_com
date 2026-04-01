@extends('layouts.app')

@section('title', 'Ventes')
@section('page-title', 'Ventes')
@section('page-subtitle')
    {{ now()->translatedFormat('F Y') }}
@endsection

@section('content')
    @livewire('caisse.vente-manager')
@endsection