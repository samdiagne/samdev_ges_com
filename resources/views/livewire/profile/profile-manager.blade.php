<div>
    {{-- Flash --}}
    <div x-data="{ show: false }"
         x-on:flash-saved.window="show = true; setTimeout(() => show = false, 3000)"
         x-show="show"
         x-transition
         class="fixed top-4 right-4 z-50 flex items-center gap-3 bg-green-500
                text-white px-5 py-3 rounded-2xl shadow-xl text-sm font-medium">
        Profil mis à jour !
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-1 mb-6 bg-white rounded-2xl border
                border-gray-100 shadow-sm p-1.5 w-fit">
        @foreach([
            'infos'    => 'Informations',
            'securite' => 'Mot de passe',
        ] as $tab => $label)
            <button wire:click="$set('ongletActif', '{{ $tab }}')"
                    @class([
                        'px-4 py-2 rounded-xl text-sm font-medium transition-all',
                        'bg-primary text-white shadow-lg shadow-primary/25' => $ongletActif === $tab,
                        'text-gray-500 hover:text-dark hover:bg-gray-50' => $ongletActif !== $tab,
                    ])>
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Informations --}}
    @if($ongletActif === 'infos')
        <div class="max-w-lg">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-dark mb-5">Informations personnelles</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="name" type="text"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                                      bg-gray-50 focus:outline-none focus:border-primary focus:bg-white
                                      transition-colors"/>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="email" type="email"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                                      bg-gray-50 focus:outline-none focus:border-primary focus:bg-white
                                      transition-colors"/>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">Téléphone</label>
                            <input wire:model="phone" type="text" placeholder="+221 XX XXX XX XX"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                                          bg-gray-50 focus:outline-none focus:border-primary focus:bg-white
                                          transition-colors"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">WhatsApp</label>
                            <input wire:model="whatsapp" type="text" placeholder="+221 XX XXX XX XX"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                                          bg-gray-50 focus:outline-none focus:border-primary focus:bg-white
                                          transition-colors"/>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button wire:click="saveInfos"
                            class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white text-sm
                                   font-medium rounded-xl transition-all shadow-lg shadow-primary/25
                                   flex items-center gap-2">
                        <svg wire:loading wire:target="saveInfos"
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
        </div>
    @endif

    {{-- Mot de passe --}}
    @if($ongletActif === 'securite')
        <div class="max-w-lg">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-dark mb-5">Changer le mot de passe</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Mot de passe actuel <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="current_password" type="password"
                               class="w-full px-3 py-2.5 border rounded-xl text-sm
                                      focus:outline-none transition-colors
                                      {{ $errors->has('current_password')
                                         ? 'border-red-400 bg-red-50'
                                         : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Nouveau mot de passe <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="password" type="password" placeholder="Minimum 8 caractères"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                                      bg-gray-50 focus:outline-none focus:border-primary focus:bg-white
                                      transition-colors"/>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Confirmer le mot de passe <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="password_confirmation" type="password"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                                      bg-gray-50 focus:outline-none focus:border-primary focus:bg-white
                                      transition-colors"/>
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button wire:click="savePassword"
                            class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white text-sm
                                   font-medium rounded-xl transition-all shadow-lg shadow-primary/25
                                   flex items-center gap-2">
                        <svg wire:loading wire:target="savePassword"
                             class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Changer le mot de passe
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
