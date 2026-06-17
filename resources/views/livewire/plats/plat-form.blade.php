<div class="max-w-4xl mx-auto">
    <form wire:submit="save" class="space-y-6">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ── Colonne principale (2/3) ──────────────── --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Infos générales --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-primary/10 rounded-lg flex items-center
                                     justify-center text-primary text-xs">1</span>
                        Informations générales
                    </h3>
                    <div class="space-y-4">

                        {{-- Nom --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Nom du plat <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="nom" type="text"
                                   placeholder="Ex: Thiéboudienne, Yassa poulet..."
                                   class="w-full px-3 py-2.5 border rounded-xl text-sm
                                          focus:outline-none transition-colors
                                          {{ $errors->has('nom')
                                             ? 'border-red-400 bg-red-50'
                                             : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                            @error('nom')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description courte --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Description courte
                            </label>
                            <input wire:model="description_courte" type="text"
                                   placeholder="Résumé en une ligne..."
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary focus:bg-white transition-colors"/>
                        </div>

                        {{-- Description longue --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Description complète
                            </label>
                            <textarea wire:model="description" rows="4"
                                      placeholder="Détails du plat, accompagnements..."
                                      class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                             text-sm bg-gray-50 focus:outline-none
                                             focus:border-primary focus:bg-white
                                             transition-colors resize-none">
                            </textarea>
                        </div>
                    </div>
                </div>

                {{-- Ingrédients --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-blue-100 rounded-lg flex items-center
                                     justify-center text-blue-600 text-xs">2</span>
                        Ingrédients
                    </h3>

                    @if(count($ingredients) > 0)
                        <div class="space-y-2 mb-3">
                            @foreach($ingredients as $index => $ingredient)
                                <div class="flex items-center gap-2">
                                    <input type="text"
                                           wire:model="ingredients.{{ $index }}"
                                           placeholder="Ex: Riz, Poisson, Tomate..."
                                           class="flex-1 px-3 py-2 border border-gray-200 rounded-xl
                                                  text-sm bg-gray-50 focus:outline-none
                                                  focus:border-primary focus:bg-white transition-colors"/>
                                    <button type="button"
                                            wire:click="removeIngredient({{ $index }})"
                                            class="w-8 h-8 flex items-center justify-center
                                                   text-gray-300 hover:text-red-500 hover:bg-red-50
                                                   rounded-lg transition-colors flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="border-2 border-dashed border-gray-200 rounded-xl px-4 py-4
                                    flex items-center gap-3 text-gray-400 mb-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center
                                        justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-300" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-400">
                                Aucun ingrédient ajouté
                            </p>
                        </div>
                    @endif

                    <button type="button"
                            wire:click="addIngredient"
                            class="inline-flex items-center gap-1.5 text-xs font-medium
                                   text-primary hover:text-primary/80 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M12 4v16M4 12h16"/>
                        </svg>
                        Ajouter un ingrédient
                    </button>
                </div>

                {{-- Prix --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-secondary/10 rounded-lg flex items-center
                                     justify-center text-secondary text-xs">3</span>
                        Prix
                    </h3>
                    <div class="space-y-4">

                        {{-- Prix de vente --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Prix de vente <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input wire:model.blur="prix_vente" type="number" step="1" min="0"
                                    placeholder="Ex: 3500"
                                    class="w-full px-3 py-2.5 pr-16 border rounded-xl text-sm
                                            focus:outline-none transition-colors
                                            {{ $errors->has('prix_vente')
                                                ? 'border-red-400 bg-red-50'
                                                : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2
                                            text-xs text-gray-400 font-medium">FCFA</span>
                            </div>
                            @error('prix_vente')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Prix dégressif --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-indigo-100 rounded-lg flex items-center
                                     justify-center text-indigo-600 text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </span>
                        Prix dégressif
                    </h3>
                    <p class="text-xs text-gray-400 mb-4">
                        Appliquer un prix réduit à partir d'une certaine quantité achetée.
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                À partir de (qté)
                            </label>
                            <input wire:model="quantite_palier" type="number" min="2" step="1"
                                   placeholder="Ex: 25"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                                          bg-gray-50 focus:outline-none focus:border-primary
                                          focus:bg-white transition-colors"/>
                            @error('quantite_palier')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Prix unitaire
                            </label>
                            <div class="relative">
                                <input wire:model="prix_palier" type="number" min="0" step="1"
                                       placeholder="Ex: 400"
                                       class="w-full px-3 py-2.5 pr-16 border border-gray-200 rounded-xl
                                              text-sm bg-gray-50 focus:outline-none focus:border-primary
                                              focus:bg-white transition-colors"/>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2
                                             text-xs text-gray-400 font-medium">FCFA</span>
                            </div>
                            @error('prix_palier')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    @if($quantite_palier && $prix_palier && $prix_vente)
                        <div class="mt-3 p-3 bg-indigo-50 rounded-xl text-xs text-indigo-700">
                            <span class="font-semibold">Résumé :</span>
                            {{ number_format($prix_vente, 0, ',', ' ') }} FCFA/u,
                            puis {{ number_format($prix_palier, 0, ',', ' ') }} FCFA/u
                            à partir de {{ $quantite_palier }} unités
                        </div>
                    @endif
                </div>

                {{-- Promotion --}}
                <div class="bg-white rounded-2xl border shadow-sm p-6 transition-colors
                            {{ $en_promo ? 'border-red-200 bg-red-50/30' : 'border-gray-100' }}">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-dark flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs
                                         {{ $en_promo ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-400' }}">
                                %
                            </span>
                            Promotion
                        </h3>
                        <button type="button" wire:click="$toggle('en_promo')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full
                                       transition-colors duration-200
                                       {{ $en_promo ? 'bg-red-500' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white
                                         shadow transition-transform duration-200
                                         {{ $en_promo ? 'translate-x-6' : 'translate-x-1' }}">
                            </span>
                        </button>
                    </div>

                    @if($en_promo)
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1.5">
                                    Prix avant promotion <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input wire:model.blur="ancien_prix" type="number" step="1" min="0"
                                           placeholder="Ex: 5000"
                                           class="w-full px-3 py-2.5 pr-16 border rounded-xl text-sm
                                                  focus:outline-none transition-colors
                                                  {{ $errors->has('ancien_prix')
                                                     ? 'border-red-400 bg-red-50'
                                                     : 'border-gray-200 bg-white focus:border-red-400' }}"/>
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2
                                                 text-xs text-gray-400 font-medium">FCFA</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">
                                    Le prix barré affiché dans la boutique (doit être supérieur au prix de vente)
                                </p>
                                @error('ancien_prix')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Aperçu badge promo --}}
                            @if($ancien_prix > 0 && $prix_vente > 0 && $ancien_prix > $prix_vente)
                                @php $reduction = round((($ancien_prix - $prix_vente) / $ancien_prix) * 100); @endphp
                                <div class="p-4 bg-white rounded-xl border border-red-100">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">
                                        Aperçu sur le menu
                                    </p>
                                    <div class="flex items-center gap-3">
                                        <span class="bg-red-600 text-white text-sm font-bold px-2.5 py-1 rounded-lg">
                                            -{{ $reduction }}%
                                        </span>
                                        <div>
                                            <p class="text-lg font-bold text-dark">
                                                {{ number_format($prix_vente, 0, ',', ' ') }}
                                                <span class="text-xs font-normal text-gray-400">FCFA</span>
                                            </p>
                                            <p class="text-sm text-gray-400 line-through">
                                                {{ number_format($ancien_prix, 0, ',', ' ') }} FCFA
                                            </p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-red-600 font-medium mt-2">
                                        Économie de {{ number_format($ancien_prix - $prix_vente, 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-gray-400">
                            Activez pour afficher un badge promo et un prix barré sur ce plat.
                        </p>
                    @endif
                </div>
            </div>

            {{-- ── Colonne latérale (1/3) ────────────────── --}}
            <div class="space-y-6">

                {{-- Catégorie --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-accent/20 rounded-lg flex items-center
                                     justify-center text-primary text-xs">3</span>
                        Classification
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Catégorie <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="categorie_id"
                                    class="w-full px-3 py-2.5 border rounded-xl text-sm
                                           focus:outline-none transition-colors
                                           {{ $errors->has('categorie_id')
                                              ? 'border-red-400 bg-red-50'
                                              : 'border-gray-200 bg-gray-50 focus:border-primary' }}">
                                <option value="0">Sélectionner...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nom }}</option>
                                @endforeach
                            </select>
                            @error('categorie_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Stock --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-green-100 rounded-lg flex items-center
                                     justify-center text-green-600 text-xs">4</span>
                        Stock
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Portions disponibles
                            </label>
                            <input wire:model="stock" type="number" min="0"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary focus:bg-white transition-colors"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Seuil d'alerte
                            </label>
                            <input wire:model="seuil_alerte_stock" type="number" min="0"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary focus:bg-white transition-colors"/>
                            <p class="text-xs text-gray-400 mt-1">
                                Alerte quand stock ≤ ce seuil
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Image --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-purple-100 rounded-lg flex items-center
                                     justify-center text-purple-600 text-xs">5</span>
                        Image principale
                    </h3>

                    @if($imageActuelle && !$imagePrincipale)
                        <div class="mb-3">
                            <img src="{{ Storage::url($imageActuelle) }}"
                                 class="w-full h-32 object-cover rounded-xl border border-gray-200"/>
                        </div>
                    @endif

                    @if($imagePrincipale)
                        <div class="mb-3">
                            <img src="{{ $imagePrincipale->temporaryUrl() }}"
                                 class="w-full h-32 object-cover rounded-xl border border-gray-200"/>
                        </div>
                    @endif

                    <input wire:model="imagePrincipale" type="file" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-2
                                  file:px-3 file:rounded-lg file:border-0 file:text-xs
                                  file:font-medium file:bg-primary/10 file:text-primary
                                  hover:file:bg-primary/20 transition-colors cursor-pointer"/>
                    @error('imagePrincipale')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Galerie --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-purple-100 rounded-lg flex items-center
                                     justify-center text-purple-600 text-xs">6</span>
                        Galerie d'images
                    </h3>

                    {{-- Images existantes en DB --}}
                    @if($galerieExistante->isNotEmpty())
                        <div class="grid grid-cols-3 gap-2 mb-3">
                            @foreach($galerieExistante as $img)
                                <div class="relative group">
                                    <img src="{{ Storage::url($img->chemin) }}"
                                         class="w-full aspect-square object-cover rounded-xl
                                                border border-gray-200"/>
                                    <button type="button"
                                            wire:click="removeGalleryImage({{ $img->id }})"
                                            wire:confirm="Supprimer cette image de la galerie ?"
                                            class="absolute top-1 right-1 w-5 h-5 bg-red-500
                                                   text-white rounded-full text-xs
                                                   hidden group-hover:flex items-center justify-center
                                                   leading-none">
                                        ×
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Aperçu des uploads en attente --}}
                    @if(count($imagesGalerie) > 0)
                        <div class="grid grid-cols-3 gap-2 mb-3">
                            @foreach($imagesGalerie as $i => $img)
                                <div class="relative">
                                    <img src="{{ $img->temporaryUrl() }}"
                                         class="w-full aspect-square object-cover rounded-xl
                                                border-2 border-primary/30"/>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <input wire:model="imagesGalerie" type="file" accept="image/*" multiple
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-2
                                  file:px-3 file:rounded-lg file:border-0 file:text-xs
                                  file:font-medium file:bg-purple-50 file:text-purple-600
                                  hover:file:bg-purple-100 transition-colors cursor-pointer"/>
                    <p class="text-xs text-gray-400 mt-1">
                        Sélectionnez plusieurs images (max 3 Mo chacune)
                    </p>
                    @error('imagesGalerie.*')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Visibilité --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-orange-100 rounded-lg flex items-center
                                     justify-center text-orange-600 text-xs">7</span>
                        Visibilité
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-dark">Plat actif</p>
                                <p class="text-xs text-gray-400">Visible en caisse et sur le menu</p>
                            </div>
                            <button type="button" wire:click="$toggle('est_actif')"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full
                                           transition-colors duration-200
                                           {{ $est_actif ? 'bg-primary' : 'bg-gray-300' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white
                                             shadow transition-transform duration-200
                                             {{ $est_actif ? 'translate-x-6' : 'translate-x-1' }}">
                                </span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-dark">Mis en avant</p>
                                <p class="text-xs text-gray-400">Affiché en page d'accueil</p>
                            </div>
                            <button type="button" wire:click="$toggle('est_mis_en_avant')"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full
                                           transition-colors duration-200
                                           {{ $est_mis_en_avant ? 'bg-secondary' : 'bg-gray-300' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white
                                             shadow transition-transform duration-200
                                             {{ $est_mis_en_avant ? 'translate-x-6' : 'translate-x-1' }}">
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Boutons action --}}
        <div class="flex items-center justify-between bg-white rounded-2xl
                    border border-gray-100 shadow-sm px-6 py-4">
            <a href="{{ route('plats.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-600
                      hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                ← Retour
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white
                           text-sm font-medium rounded-xl transition-all shadow-lg
                           shadow-primary/25 flex items-center gap-2">
                <svg wire:loading class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                {{ $isEditing ? 'Enregistrer les modifications' : 'Créer le plat' }}
            </button>
        </div>
    </form>
</div>
