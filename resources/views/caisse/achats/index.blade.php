@extends('layouts.app')
@section('title', 'Achats')
@section('page-title', 'Achats fournisseurs')
@section('page-subtitle')
    {{ now()->translatedFormat('F Y') }}
@endsection
@section('content')
    @livewire('caisse.achat-manager')
@endsection