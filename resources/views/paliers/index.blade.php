@extends('layouts.app')

@section('title', 'Paliers de Prix')
@section('page-title', 'Paliers de Prix Groupés')
@section('page-subtitle', 'Gestion des tarifs dégressifs par catégorie')

@section('content')
    @livewire('paliers.palier-groupe-manager')
@endsection
