@extends('layouts.app')
@section('title', 'Avis clients')
@section('page-title', 'Avis clients')
@section('page-subtitle', 'Modération des avis clients')

@section('content')
<div class="space-y-6">

    {{-- En attente --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
            <h3 class="text-sm font-bold text-dark">En attente de modération</h3>
            @if($enAttente->count() > 0)
                <span class="bg-orange-100 text-orange-700 text-xs font-bold
                             px-2 py-0.5 rounded-full">
                    {{ $enAttente->count() }}
                </span>
            @endif
        </div>
        @forelse($enAttente as $avis)
            <div class="px-5 py-4 border-b border-gray-50 last:border-0">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <p class="text-sm font-semibold text-dark">
                                {{ $avis->nom_auteur }}
                            </p>
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3.5 h-3.5
                                                {{ $i <= $avis->note
                                                   ? 'text-yellow-400'
                                                   : 'text-gray-200' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921
                                                 1.902 0l1.07 3.292a1 1 0 00.95.69
                                                 h3.462c.969 0 1.371 1.24.588
                                                 1.81l-2.8 2.034a1 1 0 00-.364
                                                 1.118l1.07 3.292c.3.921-.755
                                                 1.688-1.54 1.118l-2.8-2.034a1 1
                                                 0 00-1.175 0l-2.8 2.034c-.784.57
                                                 -1.838-.197-1.539-1.118l1.07-3.292
                                                 a1 1 0 00-.364-1.118L2.98 8.72c
                                                 -.783-.57-.38-1.81.588-1.81h3.461
                                                 a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-xs text-gray-400">
                                {{ $avis->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-xs text-primary mb-1">
                            {{ $avis->Produit->nom }}
                        </p>
                        <p class="text-sm text-gray-600">{{ $avis->commentaire }}</p>
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <form action="{{ route('avis.approve', $avis->id) }}"
                              method="POST">
                            @csrf
                            <button type="submit"
                                    class="px-3 py-1.5 bg-green-100 text-green-700
                                           text-xs font-medium rounded-lg
                                           hover:bg-green-200 transition-colors">
                                Approuver
                            </button>
                        </form>
                        <form action="{{ route('avis.destroy', $avis->id) }}"
                              method="POST">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="px-3 py-1.5 bg-red-100 text-red-600
                                           text-xs font-medium rounded-lg
                                           hover:bg-red-200 transition-colors">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">
                Aucun avis en attente
            </div>
        @endforelse
    </div>

    {{-- Approuvés --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-dark">Avis approuvés récents</h3>
        </div>
        @forelse($approuves as $avis)
            <div class="px-5 py-4 border-b border-gray-50 last:border-0
                        flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        <p class="text-sm font-semibold text-dark">
                            {{ $avis->nom_auteur }}
                        </p>
                        <span class="text-xs text-gray-400">
                            {{ $avis->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <p class="text-xs text-primary mb-1">
                        {{ $avis->Produit->nom }}
                    </p>
                    <p class="text-sm text-gray-600 line-clamp-2">
                        {{ $avis->commentaire }}
                    </p>
                </div>
                <form action="{{ route('avis.destroy', $avis->id) }}"
                      method="POST">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-3 py-1.5 bg-red-100 text-red-600
                                   text-xs font-medium rounded-lg
                                   hover:bg-red-200 transition-colors">
                        Supprimer
                    </button>
                </form>
            </div>
        @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">
                Aucun avis approuvé
            </div>
        @endforelse
    </div>
</div>
@endsection
