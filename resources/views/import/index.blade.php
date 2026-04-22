@extends('layouts.app')
@section('title', 'Import de données')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-dark">Import de données</h1>
        <p class="text-sm text-gray-400 mt-1">
            Importez vos données existantes depuis un fichier Excel
        </p>
    </div>

    @livewire('import.import-manager')
</div>
@endsection
