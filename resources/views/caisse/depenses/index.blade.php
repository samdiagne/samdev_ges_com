@extends('layouts.app')
@section('title', 'Dépenses')
@section('page-title', 'Dépenses')
@section('page-subtitle')
    {{ now()->translatedFormat('F Y') }}
@endsection
@section('content')
    @livewire('caisse.depense-manager')
@endsection