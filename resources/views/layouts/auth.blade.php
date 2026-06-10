<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Mon Restaurant')</title>
    @php $faviconLogo = App\Models\Parametre::first()?->logo; @endphp
    @if($faviconLogo && file_exists(storage_path('app/public/' . $faviconLogo)))
        <link rel="icon" type="image/png" sizes="32x32" href="{{ Storage::url($faviconLogo) }}"/>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-slate-100">

<div class="min-h-screen flex items-stretch">

    {{-- Panneau gauche décoratif (desktop) --}}
    <div class="hidden lg:flex flex-col justify-between w-[46%] relative overflow-hidden flex-shrink-0"
         style="background: #0F172A;">

        <div class="absolute inset-0"
             style="background-image: radial-gradient(circle, rgba(255,255,255,0.07) 1px, transparent 1px);
                    background-size: 28px 28px;"></div>

        <div class="absolute top-0 right-0 w-72 h-72 rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(30,111,191,0.25), transparent 70%); transform: translate(30%,-30%);"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(245,168,0,0.12), transparent 70%); transform: translate(-30%,30%);"></div>

        <a href="{{ url('/') }}"
           class="absolute top-5 left-6 z-20 inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-white transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour à la boutique
        </a>

        <div class="relative z-10 flex flex-col justify-between h-full p-10 xl:p-14">
            <div>
                {{-- Logo --}}
                <div class="flex items-center gap-3 mb-10">
                    @php $logo = App\Models\Parametre::first()?->logo; @endphp
                    @if($logo && file_exists(storage_path('app/public/' . $logo)))
                        <img src="{{ Storage::url($logo) }}" class="h-14 w-auto object-contain"/>
                    @else
                        <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center font-bold text-white text-lg flex-shrink-0">
                            MR
                        </div>
                    @endif
                    <span class="text-white font-bold text-base">Mon Restaurant</span>
                </div>

                {{-- Accroche --}}
                <div class="mb-8">
                    <h2 class="text-2xl xl:text-3xl font-bold text-white leading-tight mb-3">
                        Commandez vos plats<br/>
                        préférés<br/>
                        <span style="color: #2DB667;">directement en ligne</span>
                    </h2>
                    <p class="text-sm leading-relaxed text-slate-400">
                        Plats, desserts, jus et bien plus.<br/>
                        Livraison rapide à Dakar.
                    </p>
                </div>

                {{-- Badges --}}
                <div class="space-y-2.5">
                    @foreach([
                        ['color' => '#F27B20', 'text' => 'Livraison rapide à Dakar'],
                        ['color' => '#2DB667', 'text' => 'Plats frais préparés à la commande'],
                        ['color' => '#22c55e', 'text' => 'Paiement sécurisé Wave & Orange Money'],
                    ] as $badge)
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full flex-shrink-0"
                                  style="background: {{ $badge['color'] }};"></span>
                            <span class="text-sm text-slate-300">{{ $badge['text'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <p class="text-xs text-slate-600">
                © {{ date('Y') }} Mon Restaurant · Dakar, Sénégal
            </p>
        </div>
    </div>

    {{-- Panneau droit — formulaire --}}
    <div class="flex-1 flex flex-col bg-white min-h-screen overflow-y-auto">

        {{-- Header mobile --}}
        <div class="lg:hidden relative overflow-hidden flex-shrink-0" style="background: #0F172A; min-height: 160px;">
            <div class="absolute inset-0"
                 style="background-image: radial-gradient(circle, rgba(255,255,255,0.07) 1px, transparent 1px);
                        background-size: 24px 24px;"></div>
            <div class="relative z-10 flex flex-col items-center justify-center text-center px-6 py-8">
                @php $logo = App\Models\Parametre::first()?->logo; @endphp
                @if($logo && file_exists(storage_path('app/public/' . $logo)))
                    <img src="{{ Storage::url($logo) }}" class="h-14 w-auto object-contain mb-3"/>
                @else
                    <div class="w-14 h-14 bg-primary rounded-2xl flex items-center justify-center font-bold text-white text-xl mb-3">
                        MR
                    </div>
                @endif
                <p class="font-bold text-white text-lg">Mon Restaurant</p>
                <p class="text-xs mt-1 text-slate-400">Commandez en ligne</p>
            </div>
        </div>

        {{-- Contenu formulaire --}}
        <div class="flex-1 flex flex-col justify-center px-6 py-8 sm:px-10 lg:px-14 xl:px-20">
            <div class="w-full max-w-md mx-auto">
                @yield('content')
            </div>
            <p class="lg:hidden text-center text-gray-400 text-xs mt-8">
                © {{ date('Y') }} Mon Restaurant · Dakar, Sénégal
            </p>
        </div>
    </div>

</div>

@livewireScripts
</body>
</html>