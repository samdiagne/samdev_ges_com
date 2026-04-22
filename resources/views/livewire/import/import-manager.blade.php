<div>
    {{-- Tabs --}}
    <div class="flex items-center gap-1 mb-6 bg-white rounded-2xl border
                border-gray-100 shadow-sm p-1.5 w-fit">
        @foreach([
            'produits' => 'Produits',
            'achats'   => 'Achats',
            'ventes'   => 'Ventes',
        ] as $tab => $label)
            <button wire:click="$set('activeTab', '{{ $tab }}')"
                    @class([
                        'px-4 py-2 rounded-xl text-sm font-medium transition-all',
                        'bg-primary text-white shadow-lg shadow-primary/25' => $activeTab === $tab,
                        'text-gray-500 hover:text-dark hover:bg-gray-50'   => $activeTab !== $tab,
                    ])>
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Note ordre recommandé --}}
    <div class="max-w-2xl mb-5 bg-blue-50 border border-blue-100 rounded-2xl p-4
                flex items-start gap-3">
        <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none"
             stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0"/>
        </svg>
        <p class="text-xs text-blue-700">
            <span class="font-semibold">Ordre recommandé :</span>
            Produits → Achats → Ventes. Si un produit référencé dans votre fichier n'existe pas encore,
            la ligne sera ignorée avec un message d'erreur.
        </p>
    </div>

    <div class="max-w-2xl space-y-5">

        {{-- Instructions + template --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-bold text-dark mb-3 flex items-center gap-2">
                <span class="w-6 h-6 bg-primary/10 rounded-lg flex items-center
                             justify-center text-primary text-xs">1</span>
                Télécharger le template
            </h3>

            @php
                $infos = [
                    'produits' => [
                        'desc'    => 'Importez votre catalogue produits. Les catégories et fournisseurs inexistants seront créés automatiquement.',
                        'cols'    => 'nom, categorie, fournisseur, prix_achat, prix_vente, stock, seuil_alerte, unite, marque, description',
                        'warning' => 'Les produits existants (même nom) seront mis à jour.',
                    ],
                    'ventes' => [
                        'desc'    => 'Importez l\'historique de vos ventes. Plusieurs lignes avec la même date + client forment une seule vente.',
                        'cols'    => 'date, client, telephone_client, mode_paiement, produit, quantite, prix_unitaire, remise',
                        'warning' => 'Les produits doivent déjà exister dans le système.',
                    ],
                    'achats' => [
                        'desc'    => 'Importez l\'historique de vos achats. Le stock des produits sera mis à jour automatiquement.',
                        'cols'    => 'date, fournisseur, produit, quantite, prix_unitaire, montant_paye, mode_paiement, notes',
                        'warning' => 'Les produits doivent déjà exister dans le système.',
                    ],
                ][$activeTab];
            @endphp

            <p class="text-sm text-gray-500 mb-4">{{ $infos['desc'] }}</p>

            <div class="bg-gray-50 rounded-xl p-3 mb-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">
                    Colonnes attendues
                </p>
                <p class="text-xs font-mono text-gray-600">{{ $infos['cols'] }}</p>
            </div>

            <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 mb-4
                        text-xs text-amber-700 flex items-start gap-2">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0
                             001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                {{ $infos['warning'] }}
            </div>

            <a href="{{ route('import.template', $activeTab) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary/10
                      hover:bg-primary/20 text-primary text-sm font-semibold
                      rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4
                             4V4"/>
                </svg>
                Télécharger le template Excel
            </a>
        </div>

        {{-- Upload --}}
        @if(!$done)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                <span class="w-6 h-6 bg-primary/10 rounded-lg flex items-center
                             justify-center text-primary text-xs">2</span>
                Importer votre fichier
            </h3>

            <div x-data="{ dragging: false }"
                 @dragover.prevent="dragging = true"
                 @dragleave="dragging = false"
                 @drop.prevent="dragging = false"
                 :class="dragging ? 'border-primary bg-primary/5' : 'border-gray-200 bg-gray-50'"
                 class="border-2 border-dashed rounded-2xl p-8 text-center
                        transition-colors cursor-pointer"
                 onclick="document.getElementById('file-input').click()">

                <input id="file-input" type="file" wire:model="file"
                       accept=".xlsx,.xls" class="hidden"/>

                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0
                             01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>

                @if($file)
                    <p class="text-sm font-semibold text-primary">{{ $file->getClientOriginalName() }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ number_format($file->getSize() / 1024, 1) }} Ko
                    </p>
                @else
                    <p class="text-sm font-medium text-gray-500">
                        Glissez votre fichier Excel ici ou cliquez pour parcourir
                    </p>
                    <p class="text-xs text-gray-400 mt-1">.xlsx ou .xls — max 5 Mo</p>
                @endif

                <div wire:loading wire:target="file"
                     class="mt-2 text-xs text-primary animate-pulse">
                    Chargement...
                </div>
            </div>

            @error('file')
                <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1
                                 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                              clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror

            <button wire:click="import"
                    wire:loading.attr="disabled"
                    wire:target="import"
                    @class([
                        'mt-4 w-full py-3 rounded-2xl font-bold text-sm text-white transition-all',
                        'bg-primary hover:bg-primary/90 shadow-lg shadow-primary/25' => $file,
                        'bg-gray-200 text-gray-400 cursor-not-allowed' => !$file,
                    ])>
                <span wire:loading.remove wire:target="import">
                    Lancer l'import
                </span>
                <span wire:loading wire:target="import"
                      class="flex items-center justify-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    Import en cours...
                </span>
            </button>
        </div>
        @endif

        {{-- Résultat --}}
        @if($done)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
                </svg>
                Résultat de l'import
            </h3>

            <div class="grid grid-cols-2 gap-3 mb-5">
                @if(isset($result['created']))
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $result['created'] }}</p>
                    <p class="text-xs text-green-700 mt-1">
                        {{ $activeTab === 'produits' ? 'Créés' : 'Importés' }}
                    </p>
                </div>
                @endif
                @if(isset($result['updated']))
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $result['updated'] }}</p>
                    <p class="text-xs text-blue-700 mt-1">Mis à jour</p>
                </div>
                @endif
                @if(count($result['errors']) > 0)
                <div class="bg-red-50 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-red-600">{{ count($result['errors']) }}</p>
                    <p class="text-xs text-red-700 mt-1">Lignes ignorées</p>
                </div>
                @endif
            </div>

            @if(count($result['errors']) > 0)
            <div class="bg-red-50 border border-red-100 rounded-xl p-4 mb-5">
                <p class="text-xs font-semibold text-red-700 mb-2">Détail des erreurs :</p>
                <ul class="space-y-1">
                    @foreach($result['errors'] as $err)
                        <li class="text-xs text-red-600 flex items-start gap-1.5">
                            <span class="mt-0.5 flex-shrink-0">•</span>
                            {{ $err }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <button wire:click="reset2"
                    class="w-full py-2.5 rounded-xl border border-gray-200 text-sm
                           font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                Faire un nouvel import
            </button>
        </div>
        @endif

    </div>
</div>
