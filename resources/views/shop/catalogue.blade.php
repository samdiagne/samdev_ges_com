@extends('layouts.shop')
@section('title', 'Catalogue — ' . $settings->name)
@section('description', 'Explorez notre catalogue de smartphones, ordinateurs et accessoires tech à Dakar.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @livewire('shop.catalogue-shop')
</div>
@endsection