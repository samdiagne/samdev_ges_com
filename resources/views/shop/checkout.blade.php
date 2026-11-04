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

                {{-- Mode de règlement / WhatsApp --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-dark mb-5 flex items-center gap-2">
                        <span class="w-6 h-6 bg-primary/10 rounded-lg flex items-center
                                     justify-center text-primary text-xs font-bold">3</span>
                        Mode de commande & règlement
                    </h2>

                    @if(config('paydunya.enabled'))
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
                    @else
                        <div class="bg-green-50 border border-green-200 rounded-2xl p-4 sm:p-5">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center text-white flex-shrink-0 shadow-md shadow-green-500/20">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-bold text-green-900">
                                        Validation directe sur WhatsApp
                                    </p>
                                    <p class="text-xs text-green-800 leading-relaxed">
                                        Votre commande sera enregistrée et vous serez redirigé vers WhatsApp avec le récapitulatif complet. Notre équipe conviendra avec vous de l'horaire de livraison et du mode de règlement souhaité (<strong>Wave, Orange Money ou à la livraison</strong>).
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-green-200/60 flex items-center justify-between text-xs text-green-800 font-medium">
                                <span>📱 Service client réactif</span>
                                <span>⚡ Confirmation rapide</span>
                                <span>💵 Règlement flexible</span>
                            </div>
                        </div>
                    @endif
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
                                    @if(! empty($item['details']))
                                        <p class="text-[11px] text-orange-600 font-medium truncate">
                                            🧀 {{ $item['details'] }}
                                        </p>
                                    @endif
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
                            <span>Total</span>
                            <span class="text-primary">
                                {{ number_format($total, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                    </div>

                    @if(config('paydunya.enabled'))
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
                    @else
                        <button type="submit"
                                class="w-full py-3.5 rounded-2xl font-bold text-sm
                                       text-white transition-all shadow-lg
                                       shadow-green-500/25 hover:scale-[1.02]
                                       bg-green-500 hover:bg-green-600
                                       flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Commander via WhatsApp
                        </button>
                    @endif

                    <p class="text-xs text-gray-400 text-center mt-3">
                        🔒 Vos données personnelles sont protégées
                    </p>

                    <div class="mt-4 bg-orange-50 border border-orange-100 rounded-xl p-3 text-xs text-orange-700">
                        📦 Les frais de livraison vous seront confirmés par notre service client sur WhatsApp.
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection