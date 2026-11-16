<div>
    {{-- Entête de page --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-gray-900">
                Paliers de Prix Groupés
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Définissez les tarifs dégressifs appliqués lorsqu'un client commande une certaine quantité au sein d'une même catégorie (toutes variétés confondues).
            </p>
        </div>
        <button wire:click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-primary/25 transition-all hover:bg-primary/90 hover:scale-[1.02]">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau Palier
        </button>
    </div>

    {{-- Messages Flash --}}
    @if (session()->has('success'))
        <div class="mb-6 flex items-center gap-3 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm font-semibold text-emerald-800">
            <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Filtres & Recherche --}}
    <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="relative sm:col-span-2">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Rechercher par libellé ou catégorie..."
                   class="w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"/>
        </div>

        <div>
            <select wire:model.live="filtreCategorie"
                    class="w-full rounded-xl border border-gray-200 bg-white py-2.5 px-3 text-sm text-gray-800 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                <option value="">Toutes les catégories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nom }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Table des Paliers --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/75 border-b border-gray-200/80 text-xs font-bold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-4">Catégorie</th>
                        <th class="px-6 py-4">Quantité min.</th>
                        <th class="px-6 py-4">Prix dégressif</th>
                        <th class="px-6 py-4">Libellé affiché</th>
                        <th class="px-6 py-4 text-center">Statut</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($paliers as $palier)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-primary/10 text-primary font-bold text-xs">
                                    {{ $palier->categorie?->nom ?? '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">
                                Dès <span class="text-base text-primary">{{ $palier->quantite_min }}</span> unités
                            </td>
                            <td class="px-6 py-4 font-black text-gray-900">
                                {{ number_format($palier->prix_unitaire, 0, ',', ' ') }} <span class="text-xs font-semibold text-gray-400">FCFA / u</span>
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ $palier->label ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button"
                                        wire:click="toggleActif({{ $palier->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold cursor-pointer transition-colors {{ $palier->est_actif ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $palier->est_actif ? 'bg-emerald-600' : 'bg-gray-400' }}"></span>
                                    {{ $palier->est_actif ? 'Actif' : 'Inactif' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button wire:click="openEditModal({{ $palier->id }})"
                                            title="Modifier"
                                            class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $palier->id }})"
                                            title="Supprimer"
                                            class="rounded-lg p-1.5 text-red-500 hover:bg-red-50 hover:text-red-700 transition-colors">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-400">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                                <p class="font-semibold text-gray-700">Aucun palier trouvé</p>
                                <p class="text-xs text-gray-400 mt-1">Cliquez sur « Nouveau Palier » pour en créer un.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($paliers->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">
                {{ $paliers->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL CRÉATION / MODIFICATION --}}
    @if($afficherModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm animate-fadeIn">
            <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl transition-all">
                <div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4">
                    <h2 class="text-lg font-black text-gray-900">
                        {{ $isEditing ? 'Modifier le palier' : 'Nouveau palier de groupe' }}
                    </h2>
                    <button wire:click="$set('afficherModal', false)" class="text-gray-400 hover:text-gray-600 rounded-lg p-1">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    {{-- Catégorie --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">
                            Catégorie éligible <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="categorie_id"
                                class="w-full rounded-xl border border-gray-200 bg-white py-2.5 px-3 text-sm text-gray-800 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="">Sélectionnez une catégorie...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nom }}</option>
                            @endforeach
                        </select>
                        @error('categorie_id') <span class="text-xs font-semibold text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Quantité Min --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">
                                Quantité minimale <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   min="2"
                                   step="1"
                                   wire:model="quantite_min"
                                   placeholder="Ex: 25"
                                   class="w-full rounded-xl border border-gray-200 bg-white py-2.5 px-3 text-sm text-gray-800 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"/>
                            @error('quantite_min') <span class="text-xs font-semibold text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Prix Unitaire --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">
                                Prix unitaire réduit (FCFA) <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   min="1"
                                   step="50"
                                   wire:model="prix_unitaire"
                                   placeholder="Ex: 400"
                                   class="w-full rounded-xl border border-gray-200 bg-white py-2.5 px-3 text-sm text-gray-800 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"/>
                            @error('prix_unitaire') <span class="text-xs font-semibold text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Libellé personnalisé --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">
                            Libellé affiché au client (optionnel)
                        </label>
                        <input type="text"
                               wire:model="label"
                               placeholder="Ex: Jus 250ml ≥ 25 bouteilles"
                               class="w-full rounded-xl border border-gray-200 bg-white py-2.5 px-3 text-sm text-gray-800 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"/>
                        <p class="text-[11px] text-gray-400 mt-1">Si laissé vide, un libellé sera généré automatiquement.</p>
                        @error('label') <span class="text-xs font-semibold text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Est actif --}}
                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox"
                               id="est_actif"
                               wire:model="est_actif"
                               class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"/>
                        <label for="est_actif" class="text-sm font-semibold text-gray-700">
                            Activer ce palier immédiatement
                        </label>
                    </div>

                    {{-- Boutons d'action --}}
                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                        <button type="button"
                                wire:click="$set('afficherModal', false)"
                                class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                                class="rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-primary/25 hover:bg-primary/90 transition-all">
                            {{ $isEditing ? 'Enregistrer les modifications' : 'Créer le palier' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- MODAL CONFIRMATION SUPPRESSION --}}
    @if($afficherModalSuppression)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm animate-fadeIn">
            <div class="w-full max-w-sm rounded-3xl bg-white p-6 shadow-2xl text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-red-100 text-red-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-black text-gray-900 mb-1">Supprimer ce palier ?</h3>
                <p class="text-xs text-gray-500 mb-6">
                    Cette action retirera ce tarif dégressif du panier et de la caisse.
                </p>
                <div class="flex items-center justify-center gap-3">
                    <button wire:click="$set('afficherModalSuppression', false)"
                            class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                        Annuler
                    </button>
                    <button wire:click="delete"
                            class="rounded-xl bg-red-600 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-red-600/25 hover:bg-red-700">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
