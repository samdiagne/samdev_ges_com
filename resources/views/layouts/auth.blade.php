<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Al-Adab Tech Store')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased" style="background: #F1F5F9;">

<div class="min-h-screen flex items-stretch">

    {{-- ── Panneau gauche — décoratif (desktop only) ── --}}
    <div class="hidden lg:flex flex-col justify-between w-[46%] xl:w-[42%] relative overflow-hidden flex-shrink-0"
         style="background: #0F172A;">

        {{-- Grille de points --}}
        <div class="absolute inset-0"
             style="background-image: radial-gradient(circle, rgba(255,255,255,0.07) 1px, transparent 1px);
                    background-size: 28px 28px;"></div>

        {{-- Lueurs colorées subtiles --}}
        <div class="absolute top-0 right-0 w-72 h-72 rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(30,111,191,0.25), transparent 70%); transform: translate(30%, -30%);"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(245,168,0,0.12), transparent 70%); transform: translate(-30%, 30%);"></div>

        <div class="relative z-10 flex flex-col justify-between h-full p-10 xl:p-14">

            {{-- Logo --}}
            <div>
                <div class="flex items-center gap-3 mb-10">
                    @php $logo = App\Models\CompanySetting::getSettings()->logo; @endphp
                    @if($logo && file_exists(storage_path('app/public/' . $logo)))
                        <img src="{{ Storage::url($logo) }}"
                             class="h-16 w-auto object-contain"/>
                    @else
                        <div class="w-12 h-12 bg-primary rounded-xl flex items-center
                                    justify-center font-bold text-white text-base flex-shrink-0">AT</div>
                    @endif
                    <span class="text-white font-bold text-base">Al-Adab Tech Store</span>
                </div>

                {{-- Accroche --}}
                <div class="mb-8">
                    <h2 class="text-2xl xl:text-3xl font-bold text-white leading-tight mb-3">
                        Votre boutique tech<br/>
                        de confiance<br/>
                        <span style="color: #F5A800;">à Dakar</span>
                    </h2>
                    <p class="text-sm leading-relaxed" style="color: #94a3b8;">
                        Smartphones, ordinateurs et accessoires.
                        Qualité garantie, livraison rapide.
                    </p>
                </div>

                {{-- Badges --}}
                <div class="space-y-2.5">
                    @foreach([
                        ['icon' => 'check',  'color' => '#1E6FBF', 'text' => 'Livraison rapide à Dakar en 24h'],
                        ['icon' => 'shield', 'color' => '#F5A800', 'text' => 'Qualité garantie sur tous les produits'],
                        ['icon' => 'lock',   'color' => '#22c55e', 'text' => 'Paiement sécurisé Wave & Orange Money'],
                    ] as $badge)
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                                 style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);">
                                @if($badge['icon'] === 'check')
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="{{ $badge['color'] }}" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @elseif($badge['icon'] === 'shield')
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="{{ $badge['color'] }}" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                @else
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="{{ $badge['color'] }}" stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/>
                                    </svg>
                                @endif
                            </div>
                            <span class="text-sm" style="color: #cbd5e1;">{{ $badge['text'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Footer gauche --}}
            <p class="text-xs" style="color: #475569;">
                © {{ date('Y') }} Al-Adab Tech Store · Dakar, Sénégal
            </p>
        </div>
    </div>

    {{-- ── Panneau droit — formulaire ── --}}
    <div class="flex-1 flex flex-col bg-white min-h-screen overflow-y-auto">

        {{-- Header mobile décoratif --}}
        <div class="lg:hidden relative overflow-hidden flex-shrink-0"
             style="background: #0F172A; min-height: 180px;">

            {{-- Grille de points --}}
            <div class="absolute inset-0"
                 style="background-image: radial-gradient(circle, rgba(255,255,255,0.07) 1px, transparent 1px);
                        background-size: 24px 24px;"></div>

            {{-- Lueurs --}}
            <div class="absolute top-0 right-0 w-48 h-48 rounded-full pointer-events-none"
                 style="background: radial-gradient(circle, rgba(30,111,191,0.3), transparent 70%); transform: translate(30%,-30%);"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 rounded-full pointer-events-none"
                 style="background: radial-gradient(circle, rgba(245,168,0,0.15), transparent 70%); transform: translate(-30%,30%);"></div>

            <div class="relative z-10 flex flex-col items-center justify-center text-center px-6 py-8">
                @php $logo = App\Models\CompanySetting::getSettings()->logo; @endphp
                @if($logo && file_exists(storage_path('app/public/' . $logo)))
                    <img src="{{ Storage::url($logo) }}"
                         class="h-16 w-auto object-contain mb-3 drop-shadow-lg"/>
                @else
                    <div class="w-16 h-16 bg-primary rounded-2xl flex items-center
                                justify-center font-bold text-white text-2xl mb-3 shadow-lg">
                        AT
                    </div>
                @endif
                <p class="font-bold text-white text-lg leading-tight">Al-Adab Tech Store</p>
                <p class="text-xs mt-1" style="color: #94a3b8;">Votre boutique tech à Dakar</p>

                {{-- Badges compacts --}}
                <div class="flex items-center gap-4 mt-5">
                    @foreach([
                        ['color' => '#1E6FBF', 'text' => 'Livraison 24h'],
                        ['color' => '#F5A800', 'text' => 'Qualité garantie'],
                        ['color' => '#22c55e', 'text' => 'Paiement sécurisé'],
                    ] as $b)
                        <div class="flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                  style="background:{{ $b['color'] }};"></span>
                            <span class="text-[10px] font-medium" style="color: #cbd5e1;">
                                {{ $b['text'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Formulaire --}}
        <div class="flex-1 flex flex-col justify-center px-6 py-8 sm:px-10 lg:px-14 xl:px-20">
            <div class="w-full max-w-md mx-auto">
                @yield('content')
            </div>

            {{-- Footer mobile --}}
            <p class="lg:hidden text-center text-gray-400 text-xs mt-8">
                © {{ date('Y') }} Al-Adab Tech Store · Dakar, Sénégal
            </p>
        </div>
    </div>

</div>

@livewireScripts
</body>
</html>
