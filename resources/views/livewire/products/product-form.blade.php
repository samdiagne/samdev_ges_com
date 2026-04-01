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
                                Nom du produit <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="name" type="text"
                                   placeholder="Ex: MacBook Pro 14 M3"
                                   class="w-full px-3 py-2.5 border rounded-xl text-sm
                                          focus:outline-none transition-colors
                                          {{ $errors->has('name')
                                             ? 'border-red-400 bg-red-50'
                                             : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- SKU + Marque --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1.5">SKU</label>
                                <input wire:model="sku" type="text"
                                       placeholder="Auto-généré si vide"
                                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                              text-sm bg-gray-50 focus:outline-none
                                              focus:border-primary focus:bg-white transition-colors"/>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1.5">Marque</label>
                                <input wire:model="brand" type="text"
                                       placeholder="Ex: Apple, Samsung..."
                                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                              text-sm bg-gray-50 focus:outline-none
                                              focus:border-primary focus:bg-white transition-colors"/>
                            </div>
                        </div>

                        {{-- Modèle + Unité --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1.5">Modèle</label>
                                <input wire:model="model" type="text"
                                       placeholder="Ex: MBP14-M3-16GB"
                                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                              text-sm bg-gray-50 focus:outline-none
                                              focus:border-primary focus:bg-white transition-colors"/>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1.5">Unité</label>
                                <select wire:model="unit"
                                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                               text-sm bg-gray-50 focus:outline-none
                                               focus:border-primary transition-colors">
                                    <option value="unité">Unité</option>
                                    <option value="pièce">Pièce</option>
                                    <option value="lot">Lot</option>
                                    <option value="paire">Paire</option>
                                    <option value="boîte">Boîte</option>
                                </select>
                            </div>
                        </div>

                        {{-- Description courte --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Description courte
                            </label>
                            <input wire:model="short_description" type="text"
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
                                      placeholder="Caractéristiques, spécifications techniques..."
                                      class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                             text-sm bg-gray-50 focus:outline-none
                                             focus:border-primary focus:bg-white
                                             transition-colors resize-none">
                            </textarea>
                        </div>
                    </div>
                </div>

                {{-- Prix --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-secondary/10 rounded-lg flex items-center
                                     justify-center text-secondary text-xs">2</span>
                        Prix & TVA
                    </h3>
                    <div class="space-y-4">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Prix achat --}}
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1.5">
                                    Prix d'achat <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input wire:model="purchase_price" type="number" step="1" min="0"
                                        placeholder="0"
                                        class="w-full px-3 py-2.5 pr-16 border rounded-xl text-sm
                                                focus:outline-none transition-colors
                                                {{ $errors->has('purchase_price')
                                                    ? 'border-red-400 bg-red-50'
                                                    : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2
                                                text-xs text-gray-400 font-medium">FCFA</span>
                                </div>
                                @error('purchase_price')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Prix de vente --}}
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1.5">
                                    Prix de vente <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input wire:model.live="selling_price" type="number" step="1" min="0"
                                        placeholder="0"
                                        class="w-full px-3 py-2.5 pr-16 border rounded-xl text-sm
                                                focus:outline-none transition-colors
                                                {{ $errors->has('selling_price')
                                                    ? 'border-red-400 bg-red-50'
                                                    : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2
                                                text-xs text-gray-400 font-medium">FCFA</span>
                                </div>
                                @error('selling_price')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Ancien prix (promo) --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5 flex items-center gap-2">
                                Ancien prix
                                <span class="text-xs font-normal text-gray-400">
                                    — laissez vide si pas de promotion
                                </span>
                            </label>
                            <div class="relative">
                                <input wire:model.live="old_price" type="number" step="1" min="0"
                                       placeholder="Ex: 150 000"
                                       class="w-full px-3 py-2.5 pr-16 border rounded-xl text-sm
                                              focus:outline-none transition-colors
                                              {{ $errors->has('old_price')
                                                 ? 'border-red-400 bg-red-50'
                                                 : 'border-gray-200 bg-gray-50 focus:border-orange-400 focus:bg-white' }}"/>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2
                                             text-xs text-gray-400 font-medium">FCFA</span>
                            </div>
                            @error('old_price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Indicateur promo --}}
                        @if($old_price > 0 && $selling_price > 0 && $old_price > $selling_price)
                            @php $reduction = round((($old_price - $selling_price) / $old_price) * 100); @endphp
                            <div class="p-3 bg-red-50 rounded-xl border border-red-100 flex items-center gap-3">
                                <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-lg">
                                    -{{ $reduction }}%
                                </span>
                                <p class="text-xs text-red-700 font-medium">
                                    Économie de
                                    {{ number_format($old_price - $selling_price, 0, ',', ' ') }} FCFA
                                    — badge promo affiché sur la carte produit
                                </p>
                            </div>
                        @elseif($old_price > 0 && $selling_price > 0 && $old_price <= $selling_price)
                            <div class="p-3 bg-yellow-50 rounded-xl border border-yellow-100">
                                <p class="text-xs text-yellow-700">
                                    ⚠️ L'ancien prix doit être supérieur au prix de vente actuel.
                                </p>
                            </div>
                        @endif

                        {{-- Marge --}}
                        @if($purchase_price > 0 && $selling_price > 0)
                            @php $marge = $selling_price - $purchase_price;
                                $margePercent = round(($marge / $purchase_price) * 100, 1); @endphp
                            <div class="p-3 bg-green-50 rounded-xl border border-green-100">
                                <p class="text-xs text-green-700 font-medium">
                                    💰 Marge brute :
                                    {{ number_format($marge, 0, ',', ' ') }} FCFA
                                    ({{ $margePercent }}%)
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Colonne latérale (1/3) ────────────────── --}}
            <div class="space-y-6">

                {{-- Catégorie & Fournisseur --}}
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
                            <select wire:model="category_id"
                                    class="w-full px-3 py-2.5 border rounded-xl text-sm
                                           focus:outline-none transition-colors
                                           {{ $errors->has('category_id')
                                              ? 'border-red-400 bg-red-50'
                                              : 'border-gray-200 bg-gray-50 focus:border-primary' }}">
                                <option value="0">Sélectionner...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Fournisseur
                            </label>
                            <select wire:model="supplier_id"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                           text-sm bg-gray-50 focus:outline-none
                                           focus:border-primary transition-colors">
                                <option value="">Aucun</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
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
                                Quantité en stock
                            </label>
                            <input wire:model="stock_quantity" type="number" min="0"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary focus:bg-white transition-colors"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Seuil d'alerte
                            </label>
                            <input wire:model="stock_alert_threshold" type="number" min="0"
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

                    @if($currentMainImage && !$mainImage)
                        <div class="mb-3">
                            <img src="{{ Storage::url($currentMainImage) }}"
                                 class="w-full h-32 object-cover rounded-xl border border-gray-200"/>
                        </div>
                    @endif

                    @if($mainImage)
                        <div class="mb-3">
                            <img src="{{ $mainImage->temporaryUrl() }}"
                                 class="w-full h-32 object-cover rounded-xl border border-gray-200"/>
                        </div>
                    @endif

                    <input wire:model="mainImage" type="file" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-2
                                  file:px-3 file:rounded-lg file:border-0 file:text-xs
                                  file:font-medium file:bg-primary/10 file:text-primary
                                  hover:file:bg-primary/20 transition-colors cursor-pointer"/>
                    @error('mainImage')
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
                    @if($galleryExisting->isNotEmpty())
                        <div class="grid grid-cols-3 gap-2 mb-3">
                            @foreach($galleryExisting as $img)
                                <div class="relative group">
                                    <img src="{{ Storage::url($img->image_path) }}"
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
                    @if(count($galleryImages) > 0)
                        <div class="grid grid-cols-3 gap-2 mb-3">
                            @foreach($galleryImages as $i => $img)
                                <div class="relative group">
                                    <img src="{{ $img->temporaryUrl() }}"
                                         class="w-full aspect-square object-cover rounded-xl
                                                border-2 border-primary/30"/>
                                    <button type="button"
                                            wire:click="removePendingGallery({{ $i }})"
                                            class="absolute top-1 right-1 w-5 h-5 bg-red-500
                                                   text-white rounded-full text-xs flex
                                                   items-center justify-center leading-none">
                                        ×
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <input wire:model="galleryImages" type="file" accept="image/*" multiple
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-2
                                  file:px-3 file:rounded-lg file:border-0 file:text-xs
                                  file:font-medium file:bg-purple-50 file:text-purple-600
                                  hover:file:bg-purple-100 transition-colors cursor-pointer"/>
                    <p class="text-xs text-gray-400 mt-1">
                        Sélectionnez plusieurs images (max 3 Mo chacune)
                    </p>
                    @error('galleryImages.*')
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
                                <p class="text-sm font-medium text-dark">Produit actif</p>
                                <p class="text-xs text-gray-400">Visible en caisse et sur le site</p>
                            </div>
                            <button type="button" wire:click="$toggle('is_active')"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full
                                           transition-colors duration-200
                                           {{ $is_active ? 'bg-primary' : 'bg-gray-300' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white
                                             shadow transition-transform duration-200
                                             {{ $is_active ? 'translate-x-6' : 'translate-x-1' }}">
                                </span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-dark">Mis en avant</p>
                                <p class="text-xs text-gray-400">Affiché en homepage du site</p>
                            </div>
                            <button type="button" wire:click="$toggle('is_featured')"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full
                                           transition-colors duration-200
                                           {{ $is_featured ? 'bg-secondary' : 'bg-gray-300' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white
                                             shadow transition-transform duration-200
                                             {{ $is_featured ? 'translate-x-6' : 'translate-x-1' }}">
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
            <a href="{{ route('produits.index') }}"
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
                {{ $isEditing ? 'Enregistrer les modifications' : 'Créer le produit' }}
            </button>
        </div>
    </form>
</div>