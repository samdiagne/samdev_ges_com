@extends('layouts.app')

@section('title', 'Catégories')
@section('page-title', 'Catégories')
@section('page-subtitle', 'Gestion des catégories du menu')

@section('content')
    @livewire('categories.category-manager')
@endsection