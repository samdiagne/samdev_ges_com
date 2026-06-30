<div>
    {{-- ── Header ───────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-dark">Catégories</h3>
            <p class="text-sm text-gray-400">Gérez les catégories du menu</p>
        </div>
        <button wire:click="openCreateModal"
                class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90
                       text-white font-medium px-4 py-2.5 rounded-xl transition-all
                       shadow-lg shadow-primary/25 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle catégorie
        </button>
    </div>

    {{-- ── Filtres ─────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Rechercher une catégorie..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl
                              text-sm focus:outline-none focus:border-primary bg-gray-50"/>
            </div>
            <select wire:model.live="filtreStatut"
                    class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-primary bg-gray-50">
                <option value="">Tous les statuts</option>
                <option value="1">Actives</option>
                <option value="0">Inactives</option>
            </select>
        </div>
    </div>

    {{-- ── Flash messages ──────────────────────────── --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200
                    text-green-800 px-4 py-3 rounded-xl text-sm font-medium">
            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200
                    text-red-800 px-4 py-3 rounded-xl text-sm font-medium">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Table ───────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">
                            Catégorie
                        </th>
                        <th class="text-left text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 hidden sm:table-cell">
                            Description
                        </th>
                        <th class="text-center text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">
                            Produits
                        </th>
                        <th class="text-center text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3">
                            Statut
                        </th>
                        <th class="text-right text-xs font-semibold text-gray-500
                                   uppercase tracking-wider px-4 py-3 sticky right-0 bg-gray-50 border-l border-gray-100">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            {{-- Nom --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center
                                                justify-center flex-shrink-0 overflow-hidden">
                                        @if($category->image)
                                            <img src="{{ Storage::url($category->image) }}"
                                                 alt="{{ $category->nom }}"
                                                 class="w-full h-full object-cover"/>
                                        @else
                                            <svg class="w-5 h-5 text-primary" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M7 7h.01M7 3h5l5 5v11a2 2 0 01-2 2H5a2 2
                                                         0 01-2-2V5a2 2 0 012-2h2z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-dark">
                                            {{ $category->nom }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ $category->slug }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Description --}}
                            <td class="px-4 py-3 hidden sm:table-cell">
                                <p class="text-sm text-gray-500 truncate max-w-xs">
                                    {{ $category->description ?: '—' }}
                                </p>
                            </td>

                            {{-- Nb Produits --}}
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7
                                             bg-accent/10 text-primary text-xs font-bold rounded-lg">
                                    {{ $category->Produits_count }}
                                </span>
                            </td>

                            {{-- Statut --}}
                            <td class="px-4 py-3 text-center">
                                <button wire:click="toggleStatus({{ $category->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1
                                               rounded-full text-xs font-medium transition-all
                                               {{ $category->est_active
                                                  ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                  : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full
                                                 {{ $category->est_active
                                                    ? 'bg-green-500' : 'bg-gray-400' }}">
                                    </span>
                                    {{ $category->est_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3 sticky right-0 bg-white border-l border-gray-100"
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEditModal({{ $category->id }})"
                                            class="w-8 h-8 flex items-center justify-center
                                                   rounded-lg text-gray-400 hover:text-primary
                                                   hover:bg-primary/10 transition-colors"
                                            title="Modifier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2
                                                     0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828
                                                     15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $category->id }})"
                                            class="w-8 h-8 flex items-center justify-center
                                                   rounded-lg text-gray-400 hover:text-red-500
                                                   hover:bg-red-50 transition-colors"
                                            title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2
                                                     2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1
                                                     1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center
                                                justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M7 7h.01M7 3h5l5 5v11a2 2 0 01-2 2H5a2 2
                                                     0 01-2-2V5a2 2 0 012-2h2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">
                                        Aucune catégorie trouvée
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        Créez votre première catégorie
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($categories->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════
         MODAL — Créer / Modifier
    ══════════════════════════════════════════════ --}}
    @if($afficherModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-init="$el.querySelector('[data-modal]').focus()">

            {{-- Overlay --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="closeModal"></div>

            {{-- Modal --}}
            <div data-modal tabindex="-1"
                 class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md
                        outline-none animate-fade-in">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4
                            border-b border-gray-100">
                    <h3 class="text-base font-bold text-dark">
                        {{ $isEditing ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}
                    </h3>
                    <button wire:click="closeModal"
                            class="w-8 h-8 flex items-center justify-center rounded-lg
                                   text-gray-400 hover:text-gray-600 hover:bg-gray-100
                                   transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <form wire:submit="save" class="px-6 py-4 space-y-4">

                    {{-- Nom --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="nom"
                               type="text"
                               placeholder="Ex: Produits principaux"
                               class="w-full px-3 py-2.5 border rounded-xl text-sm
                                      focus:outline-none transition-colors
                                      {{ $errors->has('nom')
                                         ? 'border-red-400 bg-red-50'
                                         : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                        @error('nom')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Description
                        </label>
                        <textarea wire:model="description"
                                  rows="3"
                                  placeholder="Description optionnelle..."
                                  class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                         text-sm bg-gray-50 focus:outline-none focus:border-primary
                                         focus:bg-white transition-colors resize-none">
                        </textarea>
                    </div>

                    {{-- Image --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Image
                        </label>

                        @if($imageActuelle && !$image)
                            <div class="mb-2 flex items-center gap-2">
                                <img src="{{ Storage::url($imageActuelle) }}"
                                     class="w-12 h-12 rounded-lg object-cover border border-gray-200"/>
                                <span class="text-xs text-gray-400">Image actuelle</span>
                            </div>
                        @endif

                        @if($image)
                            <div class="mb-2">
                                <img src="{{ $image->temporaryUrl() }}"
                                     class="w-12 h-12 rounded-lg object-cover border border-gray-200"/>
                            </div>
                        @endif

                        <input wire:model="image"
                               type="file"
                               accept="image/*"
                               class="w-full text-sm text-gray-500 file:mr-3 file:py-2
                                      file:px-3 file:rounded-lg file:border-0 file:text-xs
                                      file:font-medium file:bg-primary/10 file:text-primary
                                      hover:file:bg-primary/20 transition-colors cursor-pointer"/>
                        @error('image')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Statut --}}
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <div>
                            <p class="text-sm font-medium text-dark">Catégorie active</p>
                            <p class="text-xs text-gray-400">Visible sur le site et dans la caisse</p>
                        </div>
                        <button type="button"
                                wire:click="$toggle('est_active')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full
                                       transition-colors duration-200
                                       {{ $est_active ? 'bg-primary' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white
                                         shadow transition-transform duration-200
                                         {{ $est_active ? 'translate-x-6' : 'translate-x-1' }}">
                            </span>
                        </button>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button"
                                wire:click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-gray-600
                                       hover:text-gray-800 hover:bg-gray-100 rounded-xl
                                       transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-primary hover:bg-primary/90 text-white
                                       text-sm font-medium rounded-xl transition-all
                                       shadow-lg shadow-primary/25 flex items-center gap-2">
                            <svg wire:loading class="animate-spin w-4 h-4" fill="none"
                                 viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            {{ $isEditing ? 'Enregistrer' : 'Créer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════
         MODAL — Confirmation suppression
    ══════════════════════════════════════════════ --}}
    @if($afficherModalSuppression)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="$set('afficherModalSuppression', false)"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                                     01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                     00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-dark">Supprimer la catégorie</h3>
                        <p class="text-sm text-gray-400">Cette action est irréversible</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-6">
                    Êtes-vous sûr de vouloir supprimer cette catégorie ?
                    Les Produits associés ne seront pas supprimés.
                </p>
                <div class="flex items-center justify-end gap-3">
                    <button wire:click="$set('afficherModalSuppression', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-600
                                   hover:bg-gray-100 rounded-xl transition-colors">
                        Annuler
                    </button>
                    <button wire:click="delete"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white
                                   text-sm font-medium rounded-xl transition-colors
                                   flex items-center gap-2">
                        <svg wire:loading wire:target="delete"
                             class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>