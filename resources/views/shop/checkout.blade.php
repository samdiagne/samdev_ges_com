@extends('layouts.shop')
@section('title', 'Finaliser ma commande')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

    <h1 class="text-xl sm:text-2xl font-bold text-dark mb-6 sm:mb-8">Finaliser ma commande</h1>

    <form action="{{ route('shop.checkout.store') }}" method="POST">
        @csrf

        {{-- Résumé compact mobile (masqué sur lg) --}}
        <div class="lg:hidden mb-5"
             x-data="{ open: false }">
            <button type="button"
                    @click="open = !open"
                    class="w-full flex items-center justify-between
                           bg-white border border-gray-100 shadow-sm
                           rounded-2xl px-4 py-3.5">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="text-sm font-semibold text-dark">
                        Mon panier
                        <span class="text-gray-400 font-normal">({{ count($cart) }} article{{ count($cart) > 1 ? 's' : '' }})</span>
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-bold text-primary">
                        {{ number_format($total, 0, ',', ' ') }} FCFA
                    </span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                         :class="open ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="bg-white border border-t-0 border-gray-100 rounded-b-2xl
                        shadow-sm px-4 pb-4">
                <div class="space-y-3 pt-3 border-t border-gray-50">
                    @foreach($cart as $item)
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-gray-50 rounded-lg flex-shrink-0 overflow-hidden">
                                @if($item['image'])
                                    <img src="{{ Storage::url($item['image']) }}"
                                         class="w-full h-full object-contain p-1"/>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-dark truncate">{{ $item['name'] }}</p>
                                <p class="text-xs text-gray-400">×{{ $item['quantity'] }}</p>
                            </div>
                            <p class="text-xs font-bold text-dark flex-shrink-0">
                                {{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }}
                            </p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-xs text-gray-500">Livraison</span>
                    <span class="text-xs text-orange-500 font-medium">À confirmer</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ── Formulaire ── --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Infos client --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-dark mb-5 flex items-center gap-2">
                        <span class="w-6 h-6 bg-primary/10 rounded-lg flex items-center
                                     justify-center text-primary text-xs font-bold">1</span>
                        Vos informations
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Nom complet <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="customer_name"
                                   value="{{ old('customer_name', auth()->user()?->name) }}"
                                   placeholder="Prénom Nom"
                                   class="w-full px-3 py-2.5 border rounded-xl text-sm
                                          focus:outline-none transition-colors
                                          {{ $errors->has('customer_name')
                                             ? 'border-red-400 bg-red-50'
                                             : 'border-gray-200 bg-gray-50
                                                focus:border-primary' }}"/>
                            @error('customer_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Téléphone <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="customer_phone"
                                   value="{{ old('customer_phone', auth()->user()?->phone) }}"
                                   placeholder="+221 XX XXX XX XX"
                                   class="w-full px-3 py-2.5 border rounded-xl text-sm
                                          focus:outline-none transition-colors
                                          {{ $errors->has('customer_phone')
                                             ? 'border-red-400 bg-red-50'
                                             : 'border-gray-200 bg-gray-50
                                                focus:border-primary' }}"/>
                            @error('customer_phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Email
                                <span class="text-gray-400 font-normal">(facultatif)</span>
                            </label>
                            <input type="email" name="customer_email"
                                   value="{{ old('customer_email', auth()->user()?->email) }}"
                                   placeholder="email@exemple.com"
                                   class="w-full px-3 py-2.5 border border-gray-200
                                          rounded-xl text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary transition-colors"/>
                        </div>
                    </div>
                </div>

                {{-- Livraison --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-dark mb-5 flex items-center gap-2">
                        <span class="w-6 h-6 bg-primary/10 rounded-lg flex items-center
                                     justify-center text-primary text-xs font-bold">2</span>
                        Adresse de livraison
                    </h2>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1.5">
                                    Ville <span class="text-red-500">*</span>
                                </label>
                                <select name="city"
                                        class="w-full px-3 py-2.5 border border-gray-200
                                               rounded-xl text-sm bg-gray-50
                                               focus:outline-none focus:border-primary
                                               transition-colors">
                                    @foreach([
                                        'Dakar', 'Pikine', 'Guédiawaye', 'Rufisque',
                                        'Thiès', 'Saint-Louis', 'Ziguinchor', 'Kaolack',
                                        'Mbour', 'Autre'
                                    ] as $city)
                                        <option value="{{ $city }}"
                                            {{ old('city', 'Dakar') === $city
                                               ? 'selected' : '' }}>
                                            {{ $city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Adresse complète <span class="text-red-500">*</span>
                            </label>
                            <textarea name="delivery_address" rows="3"
                                      placeholder="Quartier, rue, numéro, point de repère..."
                                      class="w-full px-3 py-2.5 border rounded-xl text-sm
                                             focus:outline-none transition-colors resize-none
                                             {{ $errors->has('delivery_address')
                                                ? 'border-red-400 bg-red-50'
                                                : 'border-gray-200 bg-gray-50
                                                   focus:border-primary' }}">{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Instructions spéciales
                                <span class="text-gray-400 font-normal">(facultatif)</span>
                            </label>
                            <textarea name="notes" rows="2"
                                      placeholder="Sonnez à la porte bleue, disponible après 17h..."
                                      class="w-full px-3 py-2.5 border border-gray-200
                                             rounded-xl text-sm bg-gray-50 focus:outline-none
                                             focus:border-primary transition-colors
                                             resize-none">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Paiement --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6"
                     x-data="{ method: '{{ old('payment_method', 'delivery') }}' }">
                    <h2 class="text-sm font-bold text-dark mb-5 flex items-center gap-2">
                        <span class="w-6 h-6 bg-primary/10 rounded-lg flex items-center
                                     justify-center text-primary text-xs font-bold">3</span>
                        Mode de paiement
                    </h2>

                    <input type="hidden" name="payment_method" :value="method"/>

                    <div class="grid grid-cols-2 gap-3">
                        @foreach([
                            ['value' => 'wave',     'label' => 'Wave',
                             'desc'  => 'Paiement mobile Wave',    'logo' => 'wave.png'],
                            ['value' => 'om',       'label' => 'Orange Money',
                             'desc'  => 'Paiement mobile OM',      'logo' => 'om.png'],
                            ['value' => 'mix',      'label' => 'Mix by Yass',
                             'desc'  => 'Ex Free Money',           'logo' => 'mby.png'],
                            ['value' => 'djamo',    'label' => 'Djamo',
                             'desc'  => 'Carte Djamo SN',          'logo' => 'djamo.png'],
                            ['value' => 'delivery', 'label' => 'À la livraison',
                             'desc'  => 'Cash à la réception',     'logo' => null],
                            ['value' => 'virement', 'label' => 'Virement',
                             'desc'  => 'Virement bancaire',       'logo' => null],
                        ] as $pm)
                            <button type="button"
                                    @click="method = '{{ $pm['value'] }}'"
                                    :class="method === '{{ $pm['value'] }}'
                                        ? 'border-primary bg-primary/5 shadow-md'
                                        : 'border-gray-200 bg-gray-50 hover:border-gray-300'"
                                    class="flex items-start gap-3 p-4 rounded-2xl
                                           border-2 transition-all text-left">
                                @if($pm['logo'])
                                    <img src="{{ asset('images/payment/' . $pm['logo']) }}"
                                         alt="{{ $pm['label'] }}"
                                         class="w-8 h-8 object-contain flex-shrink-0"/>
                                @else
                                    <span class="text-2xl">{{ $pm['value'] === 'delivery' ? '🚚' : '💳' }}</span>
                                @endif
                                <div>
                                    <p :class="method === '{{ $pm['value'] }}'
                                               ? 'text-primary' : 'text-dark'"
                                       class="text-sm font-semibold">
                                        {{ $pm['label'] }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $pm['desc'] }}
                                    </p>
                                </div>
                                <div :class="method === '{{ $pm['value'] }}'
                                             ? 'border-primary bg-primary' : 'border-gray-300'"
                                     class="w-4 h-4 rounded-full border-2 flex items-center
                                            justify-center ml-auto flex-shrink-0 mt-0.5">
                                    <div x-show="method === '{{ $pm['value'] }}'"
                                         class="w-2 h-2 rounded-full bg-white"></div>
                                </div>
                            </button>
                        @endforeach
                    </div>

                    {{-- Info Wave/OM/Mix/Wizall --}}
                    <div x-show="method === 'wave' || method === 'om' || method === 'mix' || method === 'djamo'"
                         class="mt-4 bg-blue-50 border border-blue-100 rounded-xl
                                p-3 text-xs text-blue-700">
                        💡 Vous serez redirigé vers la page de paiement PayDunya
                        pour finaliser votre transaction en toute sécurité.
                    </div>

                    {{-- Info livraison --}}
                    <div x-show="method === 'delivery'"
                         class="mt-4 bg-green-50 border border-green-100 rounded-xl
                                p-3 text-xs text-green-700">
                        🚚 Payez en cash lors de la livraison.
                        Nous vous contacterons pour confirmer.
                    </div>

                    {{-- Info virement --}}
                    <div x-show="method === 'virement'"
                         class="mt-4 bg-orange-50 border border-orange-100 rounded-xl
                                p-3 text-xs text-orange-700">
                        🏦 Nos coordonnées bancaires vous seront envoyées
                        après confirmation de la commande.
                    </div>

                    @error('payment_method')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- ── Résumé ── --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100
                            shadow-sm p-6 sticky top-24">
                    <h2 class="text-base font-bold text-dark mb-5">
                        Récapitulatif
                    </h2>

                    {{-- Articles --}}
                    <div class="space-y-3 mb-5">
                        @foreach($cart as $item)
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-50 rounded-lg
                                            flex-shrink-0 overflow-hidden">
                                    @if($item['image'])
                                        <img src="{{ Storage::url($item['image']) }}"
                                             class="w-full h-full object-contain p-1"/>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-dark truncate">
                                        {{ $item['name'] }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        ×{{ $item['quantity'] }}
                                    </p>
                                </div>
                                <p class="text-xs font-bold text-dark flex-shrink-0">
                                    {{ number_format(
                                        $item['price'] * $item['quantity'],
                                        0, ',', ' ') }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-2 mb-6">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Sous-total</span>
                            <span>{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500 items-start">
                            <span>Livraison</span>
                            <span class="text-orange-500 font-medium text-right">À confirmer</span>
                        </div>
                        <div class="flex justify-between text-base font-bold
                                    text-dark border-t border-gray-100 pt-2 mt-2">
                            <span>Total HT</span>
                            <span class="text-primary">
                                {{ number_format($total, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full py-3.5 rounded-2xl font-bold text-sm
                                   text-white transition-all shadow-lg
                                   shadow-primary/25 hover:scale-[1.02]
                                   flex items-center justify-center gap-2"
                            style="background-color: #1E6FBF;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
                        </svg>
                        Confirmer la commande
                    </button>

                    <p class="text-xs text-gray-400 text-center mt-3">
                        🔒 Transaction 100% sécurisée
                    </p>

                    <div class="mt-4 bg-orange-50 border border-orange-100 rounded-xl p-3 text-xs text-orange-700">
                        📦 Les frais de livraison vous seront communiqués par notre service client après validation de votre commande.
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection