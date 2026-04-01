<div>
    {{-- Flash saved --}}
    <div x-data="{ show: false }"
         x-on:flash-saved.window="show = true; setTimeout(() => show = false, 3000)"
         x-show="show"
         x-transition
         class="fixed top-4 right-4 z-50 flex items-center gap-3 bg-green-500
                text-white px-5 py-3 rounded-2xl shadow-xl text-sm font-medium">
        ✅ Paramètres sauvegardés !
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-1 mb-6 bg-white rounded-2xl border
                border-gray-100 shadow-sm p-1.5 w-fit">
        @foreach([
            'entreprise' => '🏢 Entreprise',
            'signature'  => '✍️ Signature & Cachet',
            'facturation'=> '🧾 Facturation',
        ] as $tab => $label)
            <button wire:click="$set('activeTab', '{{ $tab }}')"
                    @class([
                        'px-4 py-2 rounded-xl text-sm font-medium transition-all',
                        'bg-primary text-white shadow-lg shadow-primary/25' => $activeTab === $tab,
                        'text-gray-500 hover:text-dark hover:bg-gray-50' => $activeTab !== $tab,
                    ])>
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ══ TAB : Entreprise ══ --}}
    @if($activeTab === 'entreprise')
        <div class="max-w-2xl space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-dark mb-5 flex items-center gap-2">
                    <span class="w-6 h-6 bg-primary/10 rounded-lg flex items-center
                                 justify-center text-primary text-xs">1</span>
                    Informations légales
                </h3>
                <div class="space-y-4">

                    {{-- Nom --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Nom de la boutique <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="name" type="text"
                               placeholder="Al-Adab Tech Store"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                      text-sm bg-gray-50 focus:outline-none
                                      focus:border-primary focus:bg-white transition-colors"/>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NINEA + RC --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">NINEA</label>
                            <input wire:model="ninea" type="text"
                                   placeholder="Votre NINEA"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary focus:bg-white transition-colors"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">
                                Registre de commerce
                            </label>
                            <input wire:model="rc" type="text"
                                   placeholder="N° RC"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary focus:bg-white transition-colors"/>
                        </div>
                    </div>

                    {{-- Téléphone + WhatsApp --}}
                    <div class="grid grid-cols-2 gap-4">
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
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">WhatsApp</label>
                            <input wire:model="whatsapp" type="text"
                                   placeholder="+221 XX XXX XX XX"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary focus:bg-white transition-colors"/>
                        </div>
                    </div>

                    {{-- Email + Adresse --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">Email</label>
                            <input wire:model="email" type="email"
                                   placeholder="contact@boutique.sn"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary focus:bg-white transition-colors"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">Adresse</label>
                            <input wire:model="address" type="text"
                                   placeholder="Dakar, Sénégal"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary focus:bg-white transition-colors"/>
                        </div>
                    </div>

                    {{-- Réseaux sociaux --}}
                    <div>
                        <label class="block text-sm font-bold text-dark mb-3">
                            Réseaux sociaux
                        </label>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </div>
                                <input wire:model="facebook" type="url"
                                       placeholder="https://facebook.com/votrepage"
                                       class="flex-1 px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                                              bg-gray-50 focus:outline-none focus:border-primary focus:bg-white transition-colors"/>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-gradient-to-br from-purple-600 via-pink-500 to-orange-400 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                </div>
                                <input wire:model="instagram" type="url"
                                       placeholder="https://instagram.com/votrecompte"
                                       class="flex-1 px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                                              bg-gray-50 focus:outline-none focus:border-primary focus:bg-white transition-colors"/>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-black rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.75a4.85 4.85 0 01-1.01-.06z"/>
                                    </svg>
                                </div>
                                <input wire:model="tiktok" type="url"
                                       placeholder="https://tiktok.com/@votrecompte"
                                       class="flex-1 px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                                              bg-gray-50 focus:outline-none focus:border-primary focus:bg-white transition-colors"/>
                            </div>
                        </div>
                        @error('facebook') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('instagram') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('tiktok') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Logo --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Logo boutique
                        </label>
                        @if($currentLogo && !$logo)
                            <div class="mb-3 flex items-center gap-3">
                                <img src="{{ Storage::url($currentLogo) }}"
                                    class="h-14 object-contain rounded-xl border border-gray-200 bg-white p-1"/>
                                <button type="button"
                                        wire:click="$set('currentLogo', null)"
                                        class="text-xs text-red-500 hover:text-red-700 transition-colors">
                                    🗑️ Supprimer
                                </button>
                            </div>
                        @endif
                        @if($logo)
                            <div class="mb-2">
                                <img src="{{ $logo->temporaryUrl() }}"
                                     class="w-16 h-16 object-contain rounded-xl
                                            border border-gray-200 bg-white p-1"/>
                            </div>
                        @endif
                        <input wire:model="logo" type="file" accept="image/*"
                               class="w-full text-sm text-gray-500 file:mr-3 file:py-2
                                      file:px-3 file:rounded-lg file:border-0 file:text-xs
                                      file:font-medium file:bg-primary/10 file:text-primary
                                      hover:file:bg-primary/20 cursor-pointer"/>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button wire:click="saveEntreprise"
                            class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white
                                   text-sm font-medium rounded-xl transition-all shadow-lg
                                   shadow-primary/25 flex items-center gap-2">
                        <svg wire:loading wire:target="saveEntreprise"
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

    {{-- ══ TAB : Signature & Cachet ══ --}}
    @if($activeTab === 'signature')
        <div class="max-w-2xl space-y-6">

            {{-- Cachet --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-dark mb-2">Cachet de la boutique</h3>
                <p class="text-xs text-gray-400 mb-4">
                    Uploadez une image de votre cachet officiel (PNG avec fond transparent recommandé)
                </p>

                @if($currentStamp && !$stamp)
                    <div class="mb-4 p-4 bg-gray-50 rounded-xl inline-block">
                        <img src="{{ Storage::url($currentStamp) }}"
                             class="h-24 object-contain"/>
                        <p class="text-xs text-gray-400 mt-2 text-center">Cachet actuel</p>
                    </div>
                @endif

                @if($stamp)
                    <div class="mb-4 p-4 bg-gray-50 rounded-xl inline-block">
                        <img src="{{ $stamp->temporaryUrl() }}"
                             class="h-24 object-contain"/>
                        <p class="text-xs text-gray-400 mt-2 text-center">Aperçu</p>
                    </div>
                @endif

                <input wire:model="stamp" type="file" accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2
                              file:px-3 file:rounded-lg file:border-0 file:text-xs
                              file:font-medium file:bg-primary/10 file:text-primary
                              hover:file:bg-primary/20 cursor-pointer"/>
                <p class="text-xs text-gray-400 mt-2">
                    Format PNG recommandé · Max 2 Mo
                </p>
            </div>

            {{-- Signature --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6"
                 x-data="signaturePad()"
                 x-init="init()"
                 x-on:clear-signature-pad.window="clear()">

                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h3 class="text-sm font-bold text-dark">Signature électronique</h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Dessinez votre signature dans le cadre ci-dessous
                        </p>
                    </div>
                    <button type="button" @click="clear()"
                            class="text-xs text-red-500 hover:text-red-700
                                   hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">
                        🗑️ Effacer
                    </button>
                </div>

                {{-- Afficher signature existante si pas en train de dessiner --}}
                @if($currentSignature && !$signatureData && !$signatureCleared)
                    <div class="mb-3 p-3 bg-gray-50 rounded-xl">
                        <p class="text-xs text-gray-400 mb-2">Signature actuelle :</p>
                        <img src="{{ Storage::url($currentSignature) }}"
                             class="h-16 object-contain"/>
                    </div>
                @endif

                {{-- Canvas de dessin --}}
                <div class="border-2 border-dashed border-gray-200 rounded-xl
                            bg-gray-50 overflow-hidden" style="touch-action: none;">
                    <canvas id="signatureCanvas"
                            width="600" height="180"
                            class="w-full cursor-crosshair"
                            @mousedown="startDrawing($event)"
                            @mousemove="draw($event)"
                            @mouseup="stopDrawing()"
                            @mouseleave="stopDrawing()"
                            @touchstart.prevent="startDrawingTouch($event)"
                            @touchmove.prevent="drawTouch($event)"
                            @touchend="stopDrawing()">
                    </canvas>
                </div>
                <p class="text-xs text-gray-400 mt-2 text-center">
                    Utilisez la souris ou votre doigt pour signer
                </p>

                {{-- Bouton sauvegarder signature --}}
                <div class="flex justify-end mt-4">
                    <button wire:click="saveSignature"
                            @click="captureSignature()"
                            class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white
                                   text-sm font-medium rounded-xl transition-all shadow-lg
                                   shadow-primary/25 flex items-center gap-2">
                        <svg wire:loading wire:target="saveSignature"
                             class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Sauvegarder signature & cachet
                    </button>
                </div>
            </div>

            {{-- Aperçu sur facture --}}
            @if($currentStamp || $currentSignature)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-dark mb-4">
                        Aperçu sur la facture
                    </h3>
                    <div class="border border-gray-200 rounded-xl p-6 bg-gray-50">
                        <div class="flex items-end justify-between">
                            <div class="text-center">
                                @if($currentSignature)
                                    <img src="{{ Storage::url($currentSignature) }}"
                                         class="h-16 object-contain mb-2"/>
                                @endif
                                <div class="border-t border-gray-400 pt-1 w-40">
                                    <p class="text-xs text-gray-500">Signature</p>
                                </div>
                            </div>
                            <div class="text-center">
                                @if($currentStamp)
                                    <img src="{{ Storage::url($currentStamp) }}"
                                         class="h-20 object-contain mb-2 opacity-80"/>
                                @endif
                                <p class="text-xs text-gray-500">Cachet</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- ══ TAB : Facturation ══ --}}
    @if($activeTab === 'facturation')
        <div class="max-w-2xl">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-dark mb-5 flex items-center gap-2">
                    <span class="w-6 h-6 bg-secondary/10 rounded-lg flex items-center
                                 justify-center text-secondary text-xs">💰</span>
                    Paramètres de facturation
                </h3>
                <div class="space-y-4">

                    {{-- TVA --}}
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <p class="text-xs text-gray-500">
                            ℹ️ La TVA est désactivée pour cette boutique.
                            Tous les prix sont affichés sans taxes.
                        </p>
                    </div>
                    
                    {{-- Préfixe facture --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Préfixe des factures
                        </label>
                        <div class="flex items-center gap-3">
                            <input wire:model="invoice_prefix" type="text"
                                   maxlength="10"
                                   placeholder="FAC"
                                   class="w-32 px-3 py-2.5 border border-gray-200 rounded-xl
                                          text-sm bg-gray-50 focus:outline-none
                                          focus:border-primary focus:bg-white transition-colors
                                          font-mono"/>
                            <span class="text-sm text-gray-400">
                                → Exemple :
                                <strong class="text-dark">
                                    {{ $invoice_prefix }}-{{ date('Y') }}-0042
                                </strong>
                            </span>
                        </div>
                    </div>

                    {{-- Devise --}}
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">Devise</label>
                        <select wire:model="currency"
                                class="w-48 px-3 py-2.5 border border-gray-200 rounded-xl
                                       text-sm bg-gray-50 focus:outline-none
                                       focus:border-primary transition-colors">
                            <option value="FCFA">FCFA (Franc CFA)</option>
                            <option value="EUR">EUR (Euro)</option>
                            <option value="USD">USD (Dollar)</option>
                            <option value="XOF">XOF</option>
                        </select>
                    </div>

                    {{-- Info compteur --}}
                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <p class="text-xs font-semibold text-blue-700 mb-1">
                            ℹ️ Compteur de factures
                        </p>
                        <p class="text-xs text-blue-600">
                            Prochaine facture :
                            <strong>
                                {{ $invoice_prefix }}-{{ date('Y') }}-{{ str_pad($settings->invoice_counter, 4, '0', STR_PAD_LEFT) }}
                            </strong>
                        </p>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button wire:click="saveEntreprise"
                            class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white
                                   text-sm font-medium rounded-xl transition-all shadow-lg
                                   shadow-primary/25 flex items-center gap-2">
                        <svg wire:loading wire:target="saveEntreprise"
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

    {{-- Script pad de signature --}}
    <script>
        function signaturePad() {
            return {
                canvas: null,
                ctx: null,
                drawing: false,
                hasDrawn: false,

                init() {
                    this.canvas = document.getElementById('signatureCanvas');
                    if (!this.canvas) return;
                    this.ctx = this.canvas.getContext('2d');
                    this.ctx.strokeStyle = '#1E6FBF';
                    this.ctx.lineWidth   = 2.5;
                    this.ctx.lineCap     = 'round';
                    this.ctx.lineJoin    = 'round';
                },

                getPos(e) {
                    const rect = this.canvas.getBoundingClientRect();
                    const scaleX = this.canvas.width  / rect.width;
                    const scaleY = this.canvas.height / rect.height;
                    return {
                        x: (e.clientX - rect.left) * scaleX,
                        y: (e.clientY - rect.top)  * scaleY,
                    };
                },

                startDrawing(e) {
                    this.drawing = true;
                    this.hasDrawn = true;
                    const pos = this.getPos(e);
                    this.ctx.beginPath();
                    this.ctx.moveTo(pos.x, pos.y);
                },

                draw(e) {
                    if (!this.drawing) return;
                    const pos = this.getPos(e);
                    this.ctx.lineTo(pos.x, pos.y);
                    this.ctx.stroke();
                },

                stopDrawing() {
                    this.drawing = false;
                },

                startDrawingTouch(e) {
                    const touch = e.touches[0];
                    this.startDrawing(touch);
                },

                drawTouch(e) {
                    const touch = e.touches[0];
                    this.draw(touch);
                },

                clear() {
                    if (this.ctx) {
                        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                    }
                    this.hasDrawn = false;
                    @this.set('signatureCleared', true);
                    @this.set('signatureData', null);
                },

                captureSignature() {
                    if (!this.hasDrawn) return;
                    const dataUrl = this.canvas.toDataURL('image/png');
                    @this.set('signatureData', dataUrl);
                },
            }
        }
    </script>
</div>