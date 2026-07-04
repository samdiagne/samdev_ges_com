<div>
    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-dark mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Admins</p>
            <p class="text-2xl font-bold text-primary mt-1">{{ $stats['admins'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Gestionnaires</p>
            <p class="text-2xl font-bold text-secondary mt-1">{{ $stats['gestionnaires'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Inactifs</p>
            <p class="text-2xl font-bold text-gray-400 mt-1">{{ $stats['inactifs'] }}</p>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-bold text-dark">Utilisateurs</h3>
            <p class="text-sm text-gray-400">Gestion des accès</p>
        </div>
        <button wire:click="openCreateModal"
                class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90
                       text-white font-medium px-4 py-2.5 rounded-xl transition-all
                       shadow-lg shadow-primary/25 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvel utilisateur
        </button>
    </div>

    {{-- Filtres --}}
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
                       placeholder="Nom, email..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl
                              text-sm focus:outline-none focus:border-primary bg-gray-50"/>
            </div>
            <select wire:model.live="filtreRole"
                    class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-primary bg-gray-50">
                <option value="">Tous les rôles</option>
                <option value="admin">Admin</option>
                <option value="gestionnaire">Gestionnaire</option>
            </select>
            <select wire:model.live="filtreStatut"
                    class="px-3 py-2 border border-gray-200 rounded-xl text-sm
                           focus:outline-none focus:border-primary bg-gray-50">
                <option value="">Tous les statuts</option>
                <option value="1">Actifs</option>
                <option value="0">Inactifs</option>
            </select>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200
                    text-green-800 px-4 py-3 rounded-xl text-sm font-medium">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 4000)"
             class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200
                    text-red-800 px-4 py-3 rounded-xl text-sm font-medium">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left text-xs font-semibold text-gray-500
                               uppercase tracking-wider px-4 py-3">Utilisateur</th>
                    <th class="text-left text-xs font-semibold text-gray-500
                               uppercase tracking-wider px-4 py-3 hidden md:table-cell">
                        Email
                    </th>
                    <th class="text-center text-xs font-semibold text-gray-500
                               uppercase tracking-wider px-4 py-3">Rôle</th>
                    <th class="text-center text-xs font-semibold text-gray-500
                               uppercase tracking-wider px-4 py-3">Statut</th>
                    <th class="text-right text-xs font-semibold text-gray-500
                               uppercase tracking-wider px-4 py-3 sticky right-0 bg-gray-50 border-l border-gray-100">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50/50 transition-colors
                               {{ $user->id === auth()->id() ? 'bg-primary/5' : '' }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-primary flex items-center
                                            justify-center text-white font-bold text-sm
                                            flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-dark">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="text-xs text-primary font-normal ml-1">
                                                (vous)
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <p class="text-sm text-gray-600">{{ $user->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($user->hasRole('admin'))
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                             text-xs font-medium bg-primary/10 text-primary">
                                    Admin
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                             text-xs font-medium bg-secondary/10 text-secondary">
                                    Gestionnaire
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="toggleStatus({{ $user->id }})"
                                    @class([
                                        'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                         text-xs font-medium transition-all',
                                        'bg-green-100 text-green-700 hover:bg-green-200'
                                            => $user->is_active,
                                        'bg-gray-100 text-gray-500 hover:bg-gray-200'
                                            => !$user->is_active,
                                    ])>
                                <span @class([
                                    'w-1.5 h-1.5 rounded-full',
                                    'bg-green-500' => $user->is_active,
                                    'bg-gray-400'  => !$user->is_active,
                                ])></span>
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 sticky right-0 bg-white border-l border-gray-100">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="openEditModal({{ $user->id }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg
                                               text-gray-400 hover:text-primary
                                               hover:bg-primary/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2
                                                 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828
                                                 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @if($user->id !== auth()->id())
                                    <button wire:click="confirmDelete({{ $user->id }})"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg
                                                   text-gray-400 hover:text-red-500
                                                   hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2
                                                     2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1
                                                     1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-400">
                            Aucun utilisateur trouvé
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($users->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Créer/Modifier --}}
    @if($afficherModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="closeModal"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md
            max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4
                            border-b border-gray-100">
                    <h3 class="text-base font-bold text-dark">
                        {{ $isEditing ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' }}
                    </h3>
                    <button wire:click="closeModal"
                            class="w-8 h-8 flex items-center justify-center rounded-lg
                                   text-gray-400 hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form wire:submit="save" class="px-6 py-4 space-y-4">

                    {{-- Nom --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="name" type="text" placeholder="Prénom Nom"
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
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="email" type="email" placeholder="email@restaurant.sn"
                               class="w-full px-3 py-2.5 border rounded-xl text-sm
                                      focus:outline-none transition-colors
                                      {{ $errors->has('email')
                                         ? 'border-red-400 bg-red-50'
                                         : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Mot de passe --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Mot de passe
                            @if($isEditing)
                                <span class="text-xs text-gray-400 font-normal ml-1">
                                    (laisser vide pour ne pas changer)
                                </span>
                            @else
                                <span class="text-red-500">*</span>
                            @endif
                        </label>
                        <input wire:model="password" type="password"
                               placeholder="{{ $isEditing ? '••••••••' : 'Minimum 8 caractères' }}"
                               class="w-full px-3 py-2.5 border rounded-xl text-sm
                                      focus:outline-none transition-colors
                                      {{ $errors->has('password')
                                         ? 'border-red-400 bg-red-50'
                                         : 'border-gray-200 bg-gray-50 focus:border-primary focus:bg-white' }}"/>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Rôle --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Rôle</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button"
                                    wire:click="$set('role', 'admin')"
                                    @class([
                                        'py-2.5 rounded-xl text-sm font-semibold transition-all border-2',
                                        'border-primary bg-primary text-white' => $role === 'admin',
                                        'border-gray-200 bg-gray-50 text-gray-600' => $role !== 'admin',
                                    ])>
                                👑 Admin
                            </button>
                            <button type="button"
                                    wire:click="$set('role', 'gestionnaire')"
                                    @class([
                                        'py-2.5 rounded-xl text-sm font-semibold transition-all border-2',
                                        'border-secondary bg-secondary text-white' => $role === 'gestionnaire',
                                        'border-gray-200 bg-gray-50 text-gray-600' => $role !== 'gestionnaire',
                                    ])>
                                🧾 Gestionnaire
                            </button>
                        </div>
                        @error('role')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Statut --}}
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <div>
                            <p class="text-sm font-medium text-dark">Compte actif</p>
                            <p class="text-xs text-gray-400">L'utilisateur peut se connecter</p>
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

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-gray-600
                                       hover:bg-gray-100 rounded-xl transition-colors">
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

    {{-- Modal suppression --}}
    @if($afficherModalSuppression)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="$set('afficherModalSuppression', false)"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
                <h3 class="text-base font-bold text-dark mb-2">
                    Supprimer l'utilisateur
                </h3>
                <p class="text-sm text-gray-500 mb-6">Cette action est irréversible.</p>
                <div class="flex gap-3">
                    <button wire:click="$set('afficherModalSuppression', false)"
                            class="flex-1 py-2 text-sm font-medium text-gray-600
                                   hover:bg-gray-100 rounded-xl transition-colors">
                        Annuler
                    </button>
                    <button wire:click="delete"
                            class="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white
                                   text-sm font-medium rounded-xl transition-colors">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
