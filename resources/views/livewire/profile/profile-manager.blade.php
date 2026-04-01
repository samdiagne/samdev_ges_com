<div class="max-w-2xl mx-auto space-y-6">

    {{-- Flash info --}}
    <div x-data="{ show: false }"
         x-on:info-saved.window="show = true; setTimeout(() => show = false, 3000)"
         x-show="show" x-transition
         class="flex items-center gap-3 bg-green-50 border border-green-200
                text-green-800 px-4 py-3 rounded-xl text-sm font-medium">
        ✅ Informations mises à jour avec succès !
    </div>

    {{-- Flash password --}}
    <div x-data="{ show: false }"
         x-on:password-saved.window="show = true; setTimeout(() => show = false, 3000)"
         x-show="show" x-transition
         class="flex items-center gap-3 bg-green-50 border border-green-200
                text-green-800 px-4 py-3 rounded-xl text-sm font-medium">
        ✅ Mot de passe modifié avec succès !
    </div>

    {{-- Carte avatar + identité --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-primary flex items-center
                        justify-center text-white font-bold text-2xl flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-dark">{{ Auth::user()->name }}</h2>
                <p class="text-sm text-gray-400">{{ Auth::user()->email }}</p>
                <span class="inline-flex items-center mt-1.5 px-2.5 py-1 rounded-full
                             text-xs font-medium bg-primary/10 text-primary capitalize">
                    {{ Auth::user()->getRoleNames()->first() === 'admin'
                        ? '👑 Administrateur'
                        : '🧾 Gestionnaire' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Informations personnelles --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-bold text-dark mb-5 flex items-center gap-2">
            <span class="w-6 h-6 bg-primary/10 rounded-lg flex items-center
                         justify-center text-primary text-xs">1</span>
            Informations personnelles
        </h3>

        <div class="space-y-4">
            {{-- Nom --}}
            <div>
                <label class="block text-sm font-medium text-dark mb-1.5">
                    Nom complet <span class="text-red-500">*</span>
                </label>
                <input wire:model="name" type="text"
                       placeholder="Prénom Nom"
                       class="w-full px-3 py-2.5 border rounded-xl text-sm
                              focus:outline-none transition-colors
                              {{ $errors->has('name')
                                 ? 'border-red-400 bg-red-50'
                                 : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-dark mb-1.5">
                    Adresse email <span class="text-red-500">*</span>
                </label>
                <input wire:model="email" type="email"
                       placeholder="email@boutique.sn"
                       class="w-full px-3 py-2.5 border rounded-xl text-sm
                              focus:outline-none transition-colors
                              {{ $errors->has('email')
                                 ? 'border-red-400 bg-red-50'
                                 : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Téléphone --}}
            <div>
                <label class="block text-sm font-medium text-dark mb-1.5">
                    Téléphone
                </label>
                <input wire:model="phone" type="text"
                       placeholder="+221 XX XXX XX XX"
                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                              text-sm bg-gray-50 focus:outline-none
                              focus:border-primary focus:bg-white transition-colors"/>
            </div>
        </div>

        <div class="flex justify-end mt-6">
            <button wire:click="saveInfo"
                    class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white
                           text-sm font-medium rounded-xl transition-all shadow-lg
                           shadow-primary/25 flex items-center gap-2">
                <svg wire:loading wire:target="saveInfo"
                     class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Sauvegarder
            </button>
        </div>
    </div>

    {{-- Changer mot de passe --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-bold text-dark mb-5 flex items-center gap-2">
            <span class="w-6 h-6 bg-secondary/10 rounded-lg flex items-center
                         justify-center text-secondary text-xs">2</span>
            Changer le mot de passe
        </h3>

        <div class="space-y-4">
            {{-- Mot de passe actuel --}}
            <div x-data="{ show: false }">
                <label class="block text-sm font-medium text-dark mb-1.5">
                    Mot de passe actuel <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input wire:model="current_password"
                           :type="show ? 'text' : 'password'"
                           placeholder="••••••••"
                           class="w-full px-3 py-2.5 pr-10 border rounded-xl text-sm
                                  focus:outline-none transition-colors
                                  {{ $errors->has('current_password')
                                     ? 'border-red-400 bg-red-50'
                                     : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2
                                   text-gray-400 hover:text-gray-600">
                        <svg x-show="!show" class="w-4 h-4" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478
                                     0 8.268 2.943 9.542 7-1.274 4.057-5.064
                                     7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="w-4 h-4" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478
                                     0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029
                                     m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242
                                     4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29
                                     M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478
                                     0 8.268 2.943 9.543 7a10.025 10.025 0
                                     01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('current_password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nouveau mot de passe --}}
            <div x-data="{ show: false }">
                <label class="block text-sm font-medium text-dark mb-1.5">
                    Nouveau mot de passe <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input wire:model="new_password"
                           :type="show ? 'text' : 'password'"
                           placeholder="Minimum 8 caractères"
                           class="w-full px-3 py-2.5 pr-10 border rounded-xl text-sm
                                  focus:outline-none transition-colors
                                  {{ $errors->has('new_password')
                                     ? 'border-red-400 bg-red-50'
                                     : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2
                                   text-gray-400 hover:text-gray-600">
                        <svg x-show="!show" class="w-4 h-4" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478
                                     0 8.268 2.943 9.542 7-1.274 4.057-5.064
                                     7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="w-4 h-4" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478
                                     0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029
                                     m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242
                                     4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29
                                     M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478
                                     0 8.268 2.943 9.543 7a10.025 10.025 0
                                     01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('new_password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirmer mot de passe --}}
            <div x-data="{ show: false }">
                <label class="block text-sm font-medium text-dark mb-1.5">
                    Confirmer le mot de passe <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input wire:model="confirm_password"
                           :type="show ? 'text' : 'password'"
                           placeholder="••••••••"
                           class="w-full px-3 py-2.5 pr-10 border rounded-xl text-sm
                                  focus:outline-none transition-colors
                                  {{ $errors->has('confirm_password')
                                     ? 'border-red-400 bg-red-50'
                                     : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2
                                   text-gray-400 hover:text-gray-600">
                        <svg x-show="!show" class="w-4 h-4" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478
                                     0 8.268 2.943 9.542 7-1.274 4.057-5.064
                                     7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="w-4 h-4" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478
                                     0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029
                                     m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242
                                     4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29
                                     M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478
                                     0 8.268 2.943 9.543 7a10.025 10.025 0
                                     01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('confirm_password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end mt-6">
            <button wire:click="savePassword"
                    class="px-6 py-2.5 bg-secondary hover:bg-secondary/90 text-white
                           text-sm font-medium rounded-xl transition-all shadow-lg
                           shadow-secondary/25 flex items-center gap-2">
                <svg wire:loading wire:target="savePassword"
                     class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Mettre à jour
            </button>
        </div>
    </div>
</div>