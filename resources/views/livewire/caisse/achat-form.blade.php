<div class="max-w-4xl mx-auto">
    @if(session('form_error'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200
                    text-red-700 px-4 py-3 rounded-xl text-sm font-medium">
            {{ session('form_error') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ── Colonne principale : lignes de dépense ── --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

                    <h3 class="text-sm font-bold text-dark mb-5 flex items-center gap-2">
                        <span class="w-6 h-6 bg-primary/10 rounded-lg flex items-center
                                     justify-center text-primary text-xs">1</span>
                        Lignes de dépense
                    </h3>

                    {{-- En-têtes colonnes --}}
                    <div class="hidden sm:grid grid-cols-12 gap-2 mb-2 px-1">
                        <p class="col-span-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Description
                        </p>

                        <p class="col-span-2 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center">
                            Qté
                        </p>

                        <p class="col-span-3 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">
                            PU
                        </p>

                        <p class="col-span-2 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">
                            Total
                        </p>

                        <div class="col-span-1"></div>
                    </div>

                    {{-- Lignes --}}
                    <div class="space-y-2">
                        @forelse($lignes as $index => $ligne)
                            <div class="grid grid-cols-12 gap-2 items-center bg-gray-50 rounded-xl px-3 py-2.5 group">

                                {{-- Description --}}
                                <div class="col-span-12 sm:col-span-4">
                                    <input
                                        type="text"
                                        wire:change="updateLine({{ $index }}, 'description', $event.target.value)"
                                        value="{{ $ligne['description'] }}"
                                        placeholder="Ex: Cartons d'emballage..."
                                        class="w-full px-2.5 py-1.5 text-sm border border-gray-200
                                            rounded-lg bg-white focus:outline-none focus:border-primary
                                            transition-colors placeholder-gray-300"/>

                                    @error("items.{$index}.description")
                                        <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Quantité --}}
                                <div class="col-span-3 sm:col-span-2">
                                    <input
                                        type="number"
                                        wire:change="updateLine({{ $index }}, 'quantity', $event.target.value)"
                                        value="{{ $ligne['quantity'] }}"
                                        min="1"
                                        class="w-full px-2 py-1.5 text-sm text-center border border-gray-200
                                            rounded-lg bg-white focus:outline-none focus:border-primary
                                            transition-colors font-medium"/>
                                </div>

                                {{-- PU --}}
                                <div class="col-span-4 sm:col-span-3 relative">
                                    <input
                                        type="number"
                                        wire:change="updateLine({{ $index }}, 'unit_price', $event.target.value)"
                                        value="{{ $ligne['unit_price'] }}"
                                        min="0"
                                        step="1"
                                        placeholder="0"
                                        class="w-full px-2 py-1.5 pr-8 text-sm text-right border border-gray-200
                                            rounded-lg bg-white focus:outline-none focus:border-primary
                                            transition-colors font-medium"/>

                                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-400">
                                        FCFA
                                    </span>
                                </div>

                                {{-- Total --}}
                                <div class="col-span-4 sm:col-span-2 text-right">
                                    <p class="text-sm font-bold text-primary py-1.5">
                                        {{ number_format(($ligne['quantity'] ?? 1) * ($ligne['unit_price'] ?? 0), 0, ',', ' ') }}
                                    </p>
                                </div>

                                {{-- Supprimer --}}
                                <div class="col-span-1 flex justify-end">
                                    @if(count($lignes) > 1)
                                        <button
                                            type="button"
                                            wire:click="removeLine({{ $index }})"
                                            class="w-6 h-6 flex items-center justify-center
                                                text-gray-300 hover:text-red-500
                                                hover:bg-red-50 rounded-lg transition-colors
                                                opacity-0 group-hover:opacity-100">

                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2.5"
                                                    d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>

                            </div>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-4">
                                Aucune ligne
                            </p>
                        @endforelse
                    </div>

                    @error('items')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror

                    {{-- Bouton ajouter ligne --}}
                    <button type="button"
                            wire:click="addLine"
                            class="mt-3 w-full flex items-center justify-center gap-2 py-2.5
                                   border-2 border-dashed border-gray-200 rounded-xl text-sm
                                   text-gray-400 hover:border-primary hover:text-primary
                                   hover:bg-primary/5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter une ligne
                    </button>

                </div>
            </div>

            {{-- ── Colonne latérale : infos & paiement ── --}}
            <div class="space-y-6">

                {{-- Informations --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-secondary/10 rounded-lg flex items-center
                                     justify-center text-secondary text-xs">2</span>
                        Informations
                    </h3>
                    <div class="space-y-4">

                        {{-- Fournisseur --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Fournisseur
                                <span class="text-xs font-normal text-gray-400 ml-1">— optionnel</span>
                            </label>
                            <select wire:model.live="fournisseur_id"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                           text-sm bg-gray-50 focus:outline-none focus:border-primary
                                           transition-colors">
                                <option value="">Aucun fournisseur</option>
                                @foreach($fournisseurs as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date achat --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Date d'achat <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="date_achat" type="date"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none focus:border-primary
                                          transition-colors"/>
                            @error('date_achat')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Statut --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">Statut</label>
                            <select wire:model="statut"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                           text-sm bg-gray-50 focus:outline-none focus:border-primary
                                           transition-colors">
                                <option value="recu">✅ Reçu</option>
                                <option value="commande">📦 Commandé</option>
                                <option value="partiel">⏳ Partiel</option>
                                <option value="annule">❌ Annulé</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Paiement --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-green-100 rounded-lg flex items-center
                                     justify-center text-green-600 text-xs">3</span>
                        Paiement
                    </h3>
                    <div class="space-y-4">

                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Mode de paiement
                            </label>
                            <select wire:model="mode_paiement"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                           text-sm bg-gray-50 focus:outline-none focus:border-primary
                                           transition-colors">
                                <option value="cash">Cash</option>
                                <option value="wave">Wave</option>
                                <option value="om">Orange Money</option>
                                <option value="virement">Virement</option>
                                <option value="credit">Crédit</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Montant payé
                            </label>
                            <div class="relative">
                                <input wire:model.live="montant_paye"
                                       type="number" min="0" step="100"
                                       placeholder="0"
                                       class="w-full px-3 py-2.5 pr-16 border border-gray-200
                                              rounded-xl text-sm bg-gray-50 focus:outline-none
                                              focus:border-primary focus:bg-white transition-colors"/>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2
                                             text-xs text-gray-400">FCFA</span>
                            </div>
                            @error('montant_paye')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Situation compte fournisseur (si fournisseur sélectionné avec dette ou versement global) --}}
                        @if($fournisseur_id && $detteFournisseur > 0)
                            @php
                                $versement = max(0, (float) ($montant_paye ?? 0));
                                $nouveauSoldeGlobal = max(0, $detteFournisseur + $montantTotal - $versement);
                                $surplusAncien = (!$isEditing && $versement > $montantTotal)
                                    ? min($detteFournisseur, $versement - $montantTotal)
                                    : 0;
                            @endphp
                            <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl space-y-1.5 text-xs">
                                <p class="font-bold text-amber-900 uppercase tracking-wider text-[10px]">
                                    Compte courant fournisseur
                                </p>
                                <div class="flex justify-between text-amber-800">
                                    <span>Dette antérieure :</span>
                                    <span class="font-semibold">{{ number_format($detteFournisseur, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div class="flex justify-between text-amber-800">
                                    <span>Achat du jour :</span>
                                    <span class="font-semibold">{{ number_format($montantTotal, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div class="flex justify-between text-amber-800">
                                    <span>Versement saisi :</span>
                                    <span class="font-semibold">- {{ number_format($versement, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div class="flex justify-between font-bold text-amber-950 border-t border-amber-200 pt-1.5">
                                    <span>Nouveau solde dû global :</span>
                                    <span>{{ number_format($nouveauSoldeGlobal, 0, ',', ' ') }} FCFA</span>
                                </div>
                                @if($surplusAncien > 0)
                                    <p class="text-[11px] text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg p-2 mt-1 font-medium">
                                        💡 {{ number_format($montantTotal, 0, ',', ' ') }} F solderont cet achat et
                                        <strong>{{ number_format($surplusAncien, 0, ',', ' ') }} F</strong> réduiront automatiquement les anciennes factures impayées (FIFO).
                                    </p>
                                @endif
                            </div>
                        @endif

                        {{-- Résumé paiement --}}
                        @if($montantTotal > 0)
                            @if($montantDu > 0)
                                <div class="p-3 bg-red-50 rounded-xl">
                                    <p class="text-xs text-red-500 font-medium">Restant dû (sur cet achat)</p>
                                    <p class="text-lg font-bold text-red-600">
                                        {{ number_format($montantDu, 0, ',', ' ') }}
                                        <span class="text-sm font-normal">FCFA</span>
                                    </p>
                                </div>
                            @else
                                <div class="p-3 bg-green-50 rounded-xl">
                                    <p class="text-sm font-medium text-green-600">✅ Cet achat est entièrement couvert</p>
                                </div>
                            @endif
                        @endif

                        {{-- Notes --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">Notes</label>
                            <textarea wire:model="notes" rows="2"
                                      placeholder="Informations complémentaires..."
                                      class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                             text-sm bg-gray-50 focus:outline-none focus:border-primary
                                             transition-colors resize-none">
                            </textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between bg-white rounded-2xl
                    border border-gray-100 shadow-sm px-6 py-4">
            <a href="{{ route('caisse.achats.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-600
                      hover:bg-gray-100 rounded-xl transition-colors">
                ← Retour
            </a>
            <button type="button"
                    wire:click="save"
                    class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white
                           text-sm font-medium rounded-xl transition-all shadow-lg
                           shadow-primary/25 flex items-center gap-2">
                <svg wire:loading class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                {{ $isEditing ? 'Enregistrer les modifications' : 'Enregistrer l\'achat' }}
            </button>
        </div>
    </form>
</div>