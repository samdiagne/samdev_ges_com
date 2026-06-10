@extends('layouts.app')

@section('title', 'Plats')
@section('page-title', 'Plats')
@section('page-subtitle', 'Gestion du menu')

@section('content')
    @livewire('plats.plat-manager')
@endsection