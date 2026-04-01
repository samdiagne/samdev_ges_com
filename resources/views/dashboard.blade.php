@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle')
    {{ now()->translatedFormat('l d F Y') }}
@endsection

@section('content')
    @livewire('dashboard.dashboard-stats')
@endsection