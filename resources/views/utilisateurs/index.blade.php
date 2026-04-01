@extends('layouts.app')
@section('title', 'Utilisateurs')
@section('page-title', 'Utilisateurs')
@section('page-subtitle', 'Gestion des accès et comptes')
@section('content')
    @livewire('utilisateurs.utilisateur-manager')
@endsection