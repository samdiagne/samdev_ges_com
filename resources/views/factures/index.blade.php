@extends('layouts.app')
@section('title', 'Factures')
@section('page-title', 'Factures')
@section('page-subtitle')
    {{ now()->translatedFormat('F Y') }}
@endsection
@section('content')
    @livewire('factures.facture-manager')
@endsection