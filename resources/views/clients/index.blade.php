@extends('layouts.app')
@section('title', 'Clients')
@section('page-title', 'Clients')
@section('page-subtitle', 'Gestion de la clientèle')
@section('content')
    @livewire('clients.client-manager')
@endsection