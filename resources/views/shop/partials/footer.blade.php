@php $settings = App\Models\Parametre::first(); @endphp

<footer style="background: #1C1917;" class="mt-10 sm:mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-10">

            {{-- Colonne brand --}}
            <div class="col-span-2 md:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-2.5 sm:gap-3 mb-3 sm:mb-4">
                    @php $logo = $settings->logo; @endphp
                    @if($logo && file_exists(storage_path('app/public/' . $logo)))
                        <img src="{{ Storage::url($logo) }}"
                             class="h-12 sm:h-16 w-auto object-contain"/>
                    @else
                        <div class="w-9 h-9 sm:w-10 sm:h-10 bg-primary rounded-xl flex items-center
                                    justify-center font-bold text-white text-xs sm:text-sm">MF</div>
                    @endif
                    <p class="font-bold text-white text-sm">{{ $settings->nom }}</p>
                </div>

                <p class="text-xs sm:text-sm leading-relaxed mb-4 sm:mb-5" style="color: #a8a29e;">
                    Votre restaurant de plats faits maison à Dakar.
                    Qualité garantie, livraison rapide.
                </p>

                {{-- Badges confiance --}}
                <div class="flex flex-wrap gap-1.5 sm:gap-2">
                    @foreach([
                        ['color' => '#22c55e', 'label' => 'Livraison rapide'],
                        ['color' => '#F27B20', 'label' => 'Qualité garantie'],
                        ['color' => '#2DB667', 'label' => 'Paiement sécurisé'],
                    ] as $badge)
                        <div class="flex items-center gap-1 sm:gap-1.5 px-2 sm:px-3 py-1 rounded-full"
                             style="background: rgba(255,255,255,0.06);
                                    border: 0.5px solid rgba(255,255,255,0.1);">
                            <div class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                 style="background: {{ $badge['color'] }};"></div>
                            <span class="text-[10px] sm:text-xs" style="color: #d6d3d1;">
                                {{ $badge['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Navigation --}}
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest mb-4"
                   style="color: white; letter-spacing: 0.08em;">
                    Navigation
                </p>
                <ul class="space-y-2.5">
                    <li>
                        <a href="{{ route('shop.home') }}"
                           class="text-sm transition-colors hover:text-white"
                           style="color: #a8a29e;">Accueil</a>
                    </li>
                    <li>
                        <a href="{{ route('shop.catalogue') }}"
                           class="text-sm transition-colors hover:text-white"
                           style="color: #a8a29e;">Catalogue</a>
                    </li>
                    @foreach(App\Models\Categorie::where('est_active', true)->orderBy('ordre')->limit(6)->get() as $cat)
                        <li>
                            <a href="{{ route('shop.catalogue', ['categorie' => $cat->slug]) }}"
                               class="text-sm transition-colors hover:text-white"
                               style="color: #a8a29e;">{{ $cat->nom }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Informations --}}
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest mb-4"
                   style="color: white; letter-spacing: 0.08em;">
                    Informations
                </p>
                <ul class="space-y-2.5">
                    <li>
                        <a href="{{ route('shop.faq') }}"
                           class="text-sm transition-colors hover:text-white"
                           style="color: #a8a29e;">FAQ</a>
                    </li>
                    <li>
                        <a href="{{ route('shop.cgv') }}"
                           class="text-sm transition-colors hover:text-white"
                           style="color: #a8a29e;">Conditions générales de vente</a>
                    </li>
                    <li>
                        <a href="{{ route('shop.contact') }}"
                           class="text-sm transition-colors hover:text-white"
                           style="color: #a8a29e;">Contactez-nous</a>
                    </li>
                    <li>
                        <a href="{{ route('compte.register') }}"
                           class="text-sm transition-colors hover:text-white"
                           style="color: #a8a29e;">Créer un compte</a>
                    </li>
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest mb-4"
                   style="color: white; letter-spacing: 0.08em;">
                    Contact
                </p>
                <ul class="space-y-3">
                    @if($settings->adresse)
                        <li class="flex items-start gap-2.5">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5"
                                 fill="none" stroke="#F27B20" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span class="text-sm" style="color: #a8a29e;">
                                {{ $settings->adresse }}
                            </span>
                        </li>
                    @endif
                    @if($settings->telephone)
                        <li class="flex items-center gap-2.5">
                            <svg class="w-3.5 h-3.5 flex-shrink-0"
                                 fill="none" stroke="#F27B20" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498
                                         4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042
                                         11.042 0 005.516 5.516l1.13-2.257a1 1 0
                                         011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2
                                         2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="text-sm" style="color: #a8a29e;">
                                {{ $settings->telephone }}
                            </span>
                        </li>
                    @endif
                    @if($settings->email)
                        <li class="flex items-center gap-2.5">
                            <svg class="w-3.5 h-3.5 flex-shrink-0"
                                 fill="none" stroke="#F27B20" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2
                                         2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2
                                         2 0 002 2z"/>
                            </svg>
                            <span class="text-sm" style="color: #a8a29e;">
                                {{ $settings->email }}
                            </span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- Paiements acceptés --}}
        <div class="mt-6 sm:mt-10 pt-6 sm:pt-8 text-center" style="border-top: 0.5px solid rgba(255,255,255,0.08);">
            <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-widest mb-3 sm:mb-4"
               style="color: #78716c; letter-spacing: 0.08em;">
                Modes de paiement acceptés
            </p>
            <div class="flex flex-wrap justify-center items-center gap-2 sm:gap-3">

                {{-- Wave --}}
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl"
                     style="background: rgba(255,255,255,0.05); border: 0.5px solid rgba(255,255,255,0.1);">
                    <img src="/images/payment/wave.png" alt="Wave" class="h-5 w-auto object-contain"/>
                    <span class="hidden sm:inline text-xs font-medium" style="color: #a8a29e;">Wave</span>
                </div>

                {{-- Orange Money --}}
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl"
                     style="background: rgba(255,255,255,0.05); border: 0.5px solid rgba(255,255,255,0.1);">
                    <img src="/images/payment/om.png" alt="Orange Money" class="h-5 w-auto object-contain"/>
                    <span class="hidden sm:inline text-xs font-medium" style="color: #a8a29e;">Orange Money</span>
                </div>

                {{-- Mix by Yass --}}
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl"
                     style="background: rgba(255,255,255,0.05); border: 0.5px solid rgba(255,255,255,0.1);">
                    <img src="/images/payment/mby.png" alt="Mix by Yass" class="h-5 w-auto object-contain"/>
                    <span class="hidden sm:inline text-xs font-medium" style="color: #a8a29e;">Mix by Yass</span>
                </div>

                {{-- Visa --}}
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl"
                     style="background: rgba(255,255,255,0.05); border: 0.5px solid rgba(255,255,255,0.1);">
                    <img src="/images/payment/visa.webp" alt="Visa" class="h-5 w-auto object-contain"/>
                    <span class="hidden sm:inline text-xs font-medium" style="color: #a8a29e;">Visa</span>
                </div>

                {{-- Mastercard --}}
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl"
                     style="background: rgba(255,255,255,0.05); border: 0.5px solid rgba(255,255,255,0.1);">
                    <img src="/images/payment/mastercard.webp" alt="Mastercard" class="h-5 w-auto object-contain"/>
                    <span class="hidden sm:inline text-xs font-medium" style="color: #a8a29e;">Mastercard</span>
                </div>

                {{-- À la livraison --}}
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl"
                     style="background: rgba(255,255,255,0.05); border: 0.5px solid rgba(255,255,255,0.1);">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="#a8a29e" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                    </svg>
                    <span class="hidden sm:inline text-xs font-medium" style="color: #a8a29e;">À la livraison</span>
                </div>

            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="flex flex-col items-center gap-3 sm:gap-4 mt-5 sm:mt-6 pt-5 sm:pt-6 text-center"
             style="border-top: 0.5px solid rgba(255,255,255,0.08);">

            {{-- Réseaux sociaux --}}
            @if(($settings->facebook ?? false) || ($settings->instagram ?? false) || ($settings->tiktok ?? false))
                <div class="flex items-center justify-center gap-2">
                    @if($settings->facebook ?? false)
                        <a href="{{ $settings->facebook }}" target="_blank"
                           class="w-8 h-8 flex items-center justify-center rounded-lg
                                  transition-all hover:opacity-100"
                           style="background: rgba(255,255,255,0.06);
                                  border: 0.5px solid rgba(255,255,255,0.1);
                                  opacity: 0.7;">
                            <svg class="w-3.5 h-3.5" fill="#a8a29e" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                    @endif
                    @if($settings->instagram ?? false)
                        <a href="{{ $settings->instagram }}" target="_blank"
                           class="w-8 h-8 flex items-center justify-center rounded-lg
                                  transition-all hover:opacity-100"
                           style="background: rgba(255,255,255,0.06);
                                  border: 0.5px solid rgba(255,255,255,0.1);
                                  opacity: 0.7;">
                            <svg class="w-3.5 h-3.5" fill="#a8a29e" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                    @endif
                    @if($settings->tiktok ?? false)
                        <a href="{{ $settings->tiktok }}" target="_blank"
                           class="w-8 h-8 flex items-center justify-center rounded-lg
                                  transition-all hover:opacity-100"
                           style="background: rgba(255,255,255,0.06);
                                  border: 0.5px solid rgba(255,255,255,0.1);
                                  opacity: 0.7;">
                            <svg class="w-3.5 h-3.5" fill="#a8a29e" viewBox="0 0 24 24">
                                <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.75a4.85 4.85 0 01-1.01-.06z"/>
                            </svg>
                        </a>
                    @endif
                </div>
            @endif

            {{-- Copyright + légal --}}
            <p class="text-[10px] sm:text-xs" style="color: #78716c;">
                © {{ date('Y') }} {{ $settings->nom }}. Tous droits réservés.
                @if($settings->ninea)
                    · NINEA : {{ $settings->ninea }}
                @endif
                @if($settings->rc)
                    · RC : {{ $settings->rc }}
                @endif
            </p>
        </div>
    </div>
</footer>