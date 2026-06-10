@extends('layouts.shop')
@section('title', 'Finaliser ma commande')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <h1 class="text-2xl font-bold text-dark mb-8">Finaliser ma commande</h1>

    <form action="{{ route('shop.checkout.store') }}" method="POST">
        @csrf
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
                            <input type="text" name="nom_client"
                                   value="{{ old('nom_client', auth()->user()?->name) }}"
                                   placeholder="Prénom Nom"
                                   class="w-full px-3 py-2.5 border rounded-xl text-sm
                                          focus:outline-none transition-colors
                                          {{ $errors->has('nom_client')
                                             ? 'border-red-400 bg-red-50'
                                             : 'border-gray-200 bg-gray-50
                                                focus:border-primary' }}"/>
                            @error('nom_client')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Téléphone <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="telephone_client"
                                   value="{{ old('telephone_client', auth()->user()?->phone) }}"
                                   placeholder="+221 XX XXX XX XX"
                                   class="w-full px-3 py-2.5 border rounded-xl text-sm
                                          focus:outline-none transition-colors
                                          {{ $errors->has('telephone_client')
                                             ? 'border-red-400 bg-red-50'
                                             : 'border-gray-200 bg-gray-50
                                                focus:border-primary' }}"/>
                            @error('telephone_client')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Email
                                <span class="text-gray-400 font-normal">(facultatif)</span>
                            </label>
                            <input type="email" name="email_client"
                                   value="{{ old('email_client', auth()->user()?->email) }}"
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
                                <select name="ville"
                                        class="w-full px-3 py-2.5 border border-gray-200
                                               rounded-xl text-sm bg-gray-50
                                               focus:outline-none focus:border-primary
                                               transition-colors">
                                    @foreach([
                                        'Dakar', 'Pikine', 'Guédiawaye', 'Rufisque',
                                        'Thiès', 'Saint-Louis', 'Ziguinchor', 'Kaolack',
                                        'Mbour', 'Autre'
                                    ] as $ville)
                                        <option value="{{ $ville }}"
                                            {{ old('ville', 'Dakar') === $ville
                                               ? 'selected' : '' }}>
                                            {{ $ville }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Adresse complète <span class="text-red-500">*</span>
                            </label>
                            <textarea name="adresse_livraison" rows="3"
                                      placeholder="Quartier, rue, numéro, point de repère..."
                                      class="w-full px-3 py-2.5 border rounded-xl text-sm
                                             focus:outline-none transition-colors resize-none
                                             {{ $errors->has('adresse_livraison')
                                                ? 'border-red-400 bg-red-50'
                                                : 'border-gray-200 bg-gray-50
                                                   focus:border-primary' }}">{{ old('adresse_livraison') }}</textarea>
                            @error('adresse_livraison')
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
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-dark mb-5 flex items-center gap-2">
                        <span class="w-6 h-6 bg-primary/10 rounded-lg flex items-center
                                     justify-center text-primary text-xs font-bold">3</span>
                        Moyens de paiement acceptés
                    </h2>

                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                        @foreach([
                            ['label' => 'Wave',        'logo' => 'wave.png'],
                            ['label' => 'Orange Money','logo' => 'om.png'],
                            ['label' => 'Mix by Yass', 'logo' => 'mby.png'],
                            ['label' => 'Visa',        'logo' => 'visa.webp'],
                            ['label' => 'Mastercard',  'logo' => 'mastercard.webp'],
                        ] as $pm)
                            <div class="flex flex-col items-center gap-2 p-3 rounded-2xl
                                        border border-gray-100 bg-gray-50">
                                <img src="{{ asset('images/payment/' . $pm['logo']) }}"
                                     alt="{{ $pm['label'] }}"
                                     class="w-10 h-10 object-contain"/>
                                <span class="text-xs font-medium text-gray-600">
                                    {{ $pm['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 bg-blue-50 border border-blue-100 rounded-xl
                                p-3 text-xs text-blue-700 flex items-start gap-2">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0"/>
                        </svg>
                        Après confirmation, vous serez redirigé vers la page de paiement
                        sécurisée PayDunya pour choisir votre moyen de paiement et
                        finaliser votre transaction.
                    </div>
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
                        @foreach($panier as $item)
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
                                        {{ $item['nom'] }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        ×{{ $item['quantite'] }}
                                    </p>
                                </div>
                                <p class="text-xs font-bold text-dark flex-shrink-0">
                                    {{ number_format(
                                        $item['prix'] * $item['quantite'],
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
                            style="background-color: #F27B20;">
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