@extends('layouts.shop')
@section('title', 'Mes commandes')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-dark">Mon espace client</h1>
            <p class="text-sm text-gray-500 mt-1">
                Bonjour <span class="font-semibold text-dark">{{ auth()->user()->name }}</span>
            </p>
        </div>
        <a href="{{ route('shop.catalogue') }}"
           class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white
                  text-sm font-medium px-4 py-2.5 rounded-xl transition-all shadow-sm shadow-primary/25 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span class="hidden sm:inline">Continuer mes achats</span>
            <span class="sm:hidden">Catalogue</span>
        </a>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 bg-gray-100 p-1 rounded-xl mb-8 w-fit">
        <a href="{{ route('compte.commandes') }}"
           class="px-4 py-2 rounded-lg text-sm font-medium bg-white text-primary shadow-sm">
            Mes commandes
        </a>
        <a href="{{ route('compte.profil') }}"
           class="px-4 py-2 rounded-lg text-sm font-medium text-gray-500 hover:text-dark transition-colors">
            Mon profil
        </a>
    </div>

    {{-- Stats rapides --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        @php
            $total    = $orders->total();
            $enCours  = $orders->getCollection()->whereIn('status', ['pending','confirmed','processing','shipped'])->count();
            $livrees  = $orders->getCollection()->where('status','delivered')->count();
            $montant  = $orders->getCollection()->sum('total');
        @endphp
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-dark">{{ $total }}</p>
            <p class="text-xs text-gray-500 mt-1">Commandes</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-primary">{{ $enCours }}</p>
            <p class="text-xs text-gray-500 mt-1">En cours</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $livrees }}</p>
            <p class="text-xs text-gray-500 mt-1">Livrées</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-lg font-bold text-dark">{{ number_format($montant, 0, ',', ' ') }}</p>
            <p class="text-xs text-gray-500 mt-1">FCFA dépensés</p>
        </div>
    </div>

    {{-- Liste des commandes --}}
    @if($orders->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <p class="text-gray-500 font-medium">Aucune commande pour le moment</p>
            <p class="text-sm text-gray-400 mt-1">Vos commandes apparaîtront ici après achat.</p>
            <a href="{{ route('shop.catalogue') }}"
               class="inline-flex items-center gap-2 mt-6 bg-primary text-white text-sm font-medium
                      px-5 py-2.5 rounded-xl hover:bg-primary/90 transition-all">
                Découvrir le catalogue
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5
                            hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 flex-wrap mb-2">
                                <span class="font-bold text-dark text-sm">{{ $order->reference }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status_color }}">
                                    {{ $order->status_label }}
                                </span>
                                @if($order->payment_status === 'paid')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        Payé
                                    </span>
                                @endif
                            </div>

                            {{-- Produits --}}
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                @foreach($order->items->take(3) as $item)
                                    <div class="flex items-center gap-1.5">
                                        @if($item->product?->main_image)
                                            <img src="{{ Storage::url($item->product->main_image) }}"
                                                 alt="{{ $item->product_name }}"
                                                 class="w-7 h-7 rounded-lg object-contain bg-gray-50 border border-gray-100 shrink-0"/>
                                        @else
                                            <div class="w-7 h-7 rounded-lg bg-gray-100 shrink-0 flex items-center justify-center">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <span class="text-xs text-gray-600">
                                            {{ $item->product_name }}
                                            <span class="text-gray-400">×{{ $item->quantity }}</span>
                                        </span>
                                    </div>
                                @endforeach
                                @if($order->items->count() > 3)
                                    <span class="text-xs text-gray-400 italic">
                                        +{{ $order->items->count() - 3 }} autre(s)
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-4 text-xs text-gray-400">
                                <span>{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                                <span>{{ $order->payment_label }}</span>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <p class="font-bold text-dark">
                                {{ number_format($order->total, 0, ',', ' ') }}
                                <span class="text-xs font-normal text-gray-400">FCFA</span>
                            </p>
                            <a href="{{ route('compte.commande.show', $order) }}"
                               class="inline-flex items-center gap-1 text-xs text-primary
                                      hover:underline font-medium mt-2">
                                Voir le détail
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- Barre de progression statut --}}
                    @php
                        $steps = ['pending','confirmed','processing','shipped','delivered'];
                        $currentStep = array_search($order->status, $steps);
                    @endphp
                    @if($order->status !== 'cancelled')
                        <div class="mt-4 pt-4 border-t border-gray-50">
                            <div class="flex items-center justify-between">
                                @foreach([
                                    ['pending',    'En attente'],
                                    ['confirmed',  'Confirmée'],
                                    ['processing', 'Préparation'],
                                    ['shipped',    'Expédiée'],
                                    ['delivered',  'Livrée'],
                                ] as $i => [$key, $label])
                                    <div class="flex-1 flex flex-col items-center gap-1 {{ $i < count($steps) - 1 ? 'relative' : '' }}">
                                        @if($i < count($steps) - 1)
                                            <div class="absolute top-2 left-1/2 w-full h-0.5
                                                        {{ $currentStep !== false && $i < $currentStep ? 'bg-primary' : 'bg-gray-200' }}">
                                            </div>
                                        @endif
                                        <div class="w-4 h-4 rounded-full border-2 z-10
                                                    {{ $currentStep !== false && $i <= $currentStep
                                                        ? 'bg-primary border-primary'
                                                        : 'bg-white border-gray-200' }}">
                                        </div>
                                        <span class="text-[10px] {{ $currentStep !== false && $i <= $currentStep ? 'text-primary font-medium' : 'text-gray-400' }} hidden sm:block">
                                            {{ $label }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mt-4 pt-4 border-t border-gray-50">
                            <span class="text-xs text-red-500 font-medium">Cette commande a été annulée.</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    @endif

</div>
@endsection
