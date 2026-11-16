<!DOCTYPE html>
<html lang="fr"
      x-data="{
          sidebarOpen: true,
          sidebarMobile: false
      }">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="google-site-verification" content="jbqwi92eZFZEShq8RZBrBJxrYqAdKmJRTZI74Sveets" />
    @php $logo = App\Models\Parametre::first()?->logo; @endphp
    <title>@yield('title', 'Mon Restaurant') — Dashboard</title>
    @if($logo && file_exists(storage_path('app/public/' . $logo)))
        <link rel="icon" type="image/webp" href="{{ Storage::url($logo) }}"/>
        <link rel="apple-touch-icon" href="{{ Storage::url($logo) }}"/>
    @else
        <link rel="icon" href="/favicon.ico"/>
    @endif
    {{-- Remise à zéro propre du localStorage pour forcer le thème clair naturel --}}
    <script>
        localStorage.removeItem('dashboard_theme');
        document.documentElement.classList.remove('dark');
    </script>
    <style>
        #page-loader {
            position: fixed; inset: 0; z-index: 9999;
            display: flex; align-items: center; justify-content: center;
            transition: opacity 0.15s ease;
            background: #F8F9FC;
        }
        #page-loader .pl-spinner {
            width: 28px; height: 28px; border-radius: 50%;
            border: 3px solid rgba(var(--color-primary-rgb, 242, 123, 32), 0.15);
            border-top-color: var(--color-primary, #F27B20);
            animation: pl-spin 0.6s linear infinite;
        }
        @keyframes pl-spin { to { transform: rotate(360deg); } }
        #page-loader.fade-out { opacity: 0; pointer-events: none; }

        /* Sidebar */
        .sidebar-glow { background: radial-gradient(ellipse at 30% 0%, rgba(255,255,255,0.08) 0%, transparent 60%); }
        .nav-item-active { position: relative; }
        .nav-item-active::before {
            content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%; border-radius: 0 4px 4px 0;
            background: linear-gradient(180deg, #ffffff, rgba(255,255,255,0.6));
            box-shadow: 0 0 8px rgba(255,255,255,0.4);
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-light font-sans antialiased">

{{-- Overlay anti-FOUC --}}
<div id="page-loader">
    <div class="pl-spinner"></div>
</div>
<script>
    (function () {
        var l = document.getElementById('page-loader');
        if (!l) return;

        // Disparaît dès qu'Alpine a tout initialisé
        document.addEventListener('alpine:initialized', function () {
            l.classList.add('fade-out');
            setTimeout(function () { l.remove(); }, 160);
        });

        // Filet de sécurité : retire l'overlay au bout de 3s quoi qu'il arrive
        setTimeout(function () {
            if (l.parentNode) { l.classList.add('fade-out'); setTimeout(function () { l.remove(); }, 160); }
        }, 3000);
    })();
</script>

<div class="flex h-screen overflow-hidden"
style="height: 100dvh;">

    {{-- ═══════════════════════════════════════════
         SIDEBAR DESKTOP
    ═══════════════════════════════════════════ --}}
    <aside :class="sidebarOpen ? 'w-[220px]' : 'w-[64px]'"
           class="hidden lg:flex flex-col text-white flex-shrink-0 bg-gradient-to-b from-primary to-primary/90
                  transition-all duration-300 ease-in-out relative overflow-hidden shadow-xl">

        <div class="absolute inset-0 sidebar-glow pointer-events-none"></div>

        {{-- Header logo --}}
        <div class="relative z-10 flex items-center justify-between px-4 py-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
                @if($logo && file_exists(storage_path('app/public/' . $logo)))
                    <div :class="sidebarOpen ? 'h-12 w-12' : 'h-10 w-10'"
                         class="flex-shrink-0 rounded-xl logo-badge p-1 shadow-sm transition-all duration-300">
                        <img src="{{ Storage::url($logo) }}"
                             class="w-full h-full object-contain"/>
                    </div>
                @else
                    <div class="h-10 w-10 rounded-xl flex items-center justify-center
                                font-extrabold text-white text-sm flex-shrink-0
                                ring-2 ring-white/30"
                         style="background: rgba(255,255,255,0.2);">
                        {{ strtoupper(substr(App\Models\Parametre::first()?->nom ?? config('app.name', 'SC'), 0, 2)) }}
                    </div>
                @endif
                <div x-show="sidebarOpen" x-transition.opacity class="overflow-hidden min-w-0">
                    <p class="font-bold text-[15px] text-white truncate leading-tight tracking-tight">{{ App\Models\Parametre::first()?->nom ?? config('app.name', 'svmdev_ges_com') }}</p>
                    <p class="text-[11px] font-medium truncate tracking-wide uppercase"
                       style="color: white;">Tableau de bord</p>
                </div>
            </a>

            <button @click="sidebarOpen = !sidebarOpen"
                    x-show="sidebarOpen"
                    x-transition.opacity
                    class="flex-shrink-0 w-7 h-7 rounded-lg bg-white/[0.06]
                           hover:bg-white/[0.12] flex items-center justify-center
                           transition-all duration-200 ml-2 group">
                <svg :class="sidebarOpen ? 'rotate-180' : ''"
                     class="w-3.5 h-3.5 text-white/70 group-hover:text-white transition-all duration-300"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        <div class="mx-4 border-t border-white/[0.06]"></div>

        {{-- Navigation --}}
        <nav class="relative z-10 flex-1 overflow-y-auto pt-2 pb-4 px-3 space-y-0.5
                    scrollbar-thin scrollbar-thumb-white/10">
            @include('layouts.partials.sidebar-nav')
        </nav>

        <div class="mx-4 border-t border-white/[0.06]"></div>

        {{-- User info --}}
        <div class="relative z-10 p-3" x-data="{ userMenuOpen: false }">
            <button @click="userMenuOpen = !userMenuOpen"
                    class="w-full flex items-center gap-3 px-2 py-2.5 rounded-xl
                           hover:bg-white/[0.06] transition-all duration-200 cursor-pointer group">
                <div class="relative flex-shrink-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center
                                text-white font-bold text-xs"
                         style="background: rgba(255,255,255,0.2);">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2
                                border-white/40" style="background-color: #2DB667;"></div>
                </div>
                <div x-show="sidebarOpen" x-transition.opacity
                     class="overflow-hidden flex-1 min-w-0 text-left">
                    <p class="text-sm font-semibold text-white truncate leading-tight
                              transition-colors">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-[11px] text-white/70 truncate capitalize">
                        {{ Auth::user()->roles->first()?->name ?? 'utilisateur' }}
                    </p>
                </div>
                <svg x-show="sidebarOpen" x-transition.opacity
                     :class="userMenuOpen ? 'rotate-180' : ''"
                     class="w-3.5 h-3.5 text-white/60 group-hover:text-white
                            transition-all duration-200 flex-shrink-0"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Dropdown vers le haut --}}
            <div x-show="userMenuOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click.outside="userMenuOpen = false"
                 class="absolute bottom-full left-3 right-3 mb-2 rounded-xl
                        overflow-hidden border border-white/[0.08] shadow-2xl"
                 style="background: rgba(28,25,23,0.95); backdrop-filter: blur(20px);">

                <a href="{{ route('shop.home') }}"
                   target="_blank"
                   class="flex items-center gap-2.5 px-4 py-3 text-sm text-white/60
                          hover:bg-white/[0.06] hover:text-white transition-all duration-150">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14
                                 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Voir le site
                </a>

                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2.5 px-4 py-3 text-sm text-white/60
                          hover:bg-white/[0.06] hover:text-white transition-all duration-150">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12
                                 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Mon profil
                </a>

                <div class="mx-3 border-t border-white/[0.06]"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2.5 px-4 py-3 text-sm
                                   text-red-400/80 hover:bg-red-500/[0.08] hover:text-red-400
                                   transition-all duration-150">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3
                                     3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ═══════════════════════════════════════════
         SIDEBAR MOBILE overlay
    ═══════════════════════════════════════════ --}}
    <div x-show="sidebarMobile"
         x-transition.opacity
         class="fixed inset-0 z-40 bg-black/60 lg:hidden"
         @click="sidebarMobile = false">
    </div>

    <aside x-show="sidebarMobile"
           x-transition:enter="transition transform duration-300 ease-out"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition transform duration-200 ease-in"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 z-50 w-[270px] text-white flex flex-col lg:hidden bg-gradient-to-b from-primary to-primary/90
                  overflow-hidden shadow-2xl">

        <div class="absolute inset-0 sidebar-glow pointer-events-none"></div>

        <div class="relative z-10 flex items-center justify-between px-4 py-5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
                @if($logo && file_exists(storage_path('app/public/' . $logo)))
                    <div class="h-12 w-12 flex-shrink-0 rounded-xl logo-badge p-1 shadow-sm">
                        <img src="{{ Storage::url($logo) }}"
                             class="w-full h-full object-contain"/>
                    </div>
                @else
                    <div class="h-10 w-10 rounded-xl flex items-center justify-center
                                font-extrabold text-white text-sm flex-shrink-0
                                ring-2 ring-white/30"
                         style="background: rgba(255,255,255,0.2);">
                        {{ strtoupper(substr(App\Models\Parametre::first()?->nom ?? config('app.name', 'SC'), 0, 2)) }}
                    </div>
                @endif
                <div>
                    <p class="font-bold text-[15px] text-white leading-tight tracking-tight">{{ App\Models\Parametre::first()?->nom ?? config('app.name', 'svmdev_ges_com') }}</p>
                    <p class="text-[11px] font-medium tracking-wide uppercase"
                       style="color: white;">Tableau de bord</p>
                </div>
            </a>
            <button @click="sidebarMobile = false"
                    class="w-8 h-8 rounded-lg bg-white/[0.06] hover:bg-white/[0.12]
                           flex items-center justify-center transition-all duration-200">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="mx-4 border-t border-white/[0.06]"></div>

        <nav class="relative z-10 flex-1 overflow-y-auto py-4 px-3 space-y-0.5">
            @include('layouts.partials.sidebar-nav')
        </nav>

        <div class="mx-4 border-t border-white/[0.06]"></div>

        <div class="relative z-10 p-3" x-data="{ userMenuOpen: false }">
            <button @click="userMenuOpen = !userMenuOpen"
                    class="w-full flex items-center gap-3 px-2 py-2.5 rounded-xl
                           hover:bg-white/[0.06] transition-all duration-200 cursor-pointer group">
                <div class="relative flex-shrink-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center
                                text-white font-bold text-xs"
                         style="background: rgba(255,255,255,0.2);">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2
                                border-white/40" style="background-color: #2DB667;"></div>
                </div>
                <div class="overflow-hidden flex-1 min-w-0 text-left">
                    <p class="text-sm font-semibold text-white truncate leading-tight">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-[11px] text-white/70 truncate capitalize">
                        {{ Auth::user()->roles->first()?->name ?? 'utilisateur' }}
                    </p>
                </div>
                <svg :class="userMenuOpen ? 'rotate-180' : ''"
                     class="w-3.5 h-3.5 text-white/60 group-hover:text-white
                            transition-all duration-200 flex-shrink-0"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="userMenuOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 @click.outside="userMenuOpen = false"
                 class="absolute bottom-full left-3 right-3 mb-2 rounded-xl
                        overflow-hidden border border-white/[0.08] shadow-2xl"
                 style="background: rgba(28,25,23,0.95); backdrop-filter: blur(20px);">

                <a href="{{ route('shop.home') }}" target="_blank"
                   class="flex items-center gap-2.5 px-4 py-3 text-sm text-white/60
                          hover:bg-white/[0.06] hover:text-white transition-all duration-150">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Voir le site
                </a>
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2.5 px-4 py-3 text-sm text-white/60
                          hover:bg-white/[0.06] hover:text-white transition-all duration-150">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Mon profil
                </a>
                <div class="mx-3 border-t border-white/[0.06]"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2.5 px-4 py-3 text-sm
                                   text-red-400/80 hover:bg-red-500/[0.08] hover:text-red-400
                                   transition-all duration-150">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ═══════════════════════════════════════════
         MAIN CONTENT
    ═══════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col overflow-hidden"
         :class="darkMode ? 'border-l border-white/10' : ''">

        {{-- Topbar --}}
        <header class="relative bg-white border-b border-gray-100 px-4 lg:px-6
                       flex items-center justify-between flex-shrink-0"
                style="min-height: 60px;">

            <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-gradient-to-r from-primary via-accent to-secondary opacity-40"></div>

            {{-- Gauche : toggle + titre --}}
            <div class="flex items-center gap-3 min-w-0 max-w-[calc(50%_-_60px)] sm:max-w-[calc(50%_-_100px)]">
                <button @click="sidebarMobile = true"
                        class="lg:hidden flex-shrink-0 flex items-center justify-center w-9 h-9
                               rounded-xl hover:bg-gray-100 transition-all duration-200">
                    <svg class="w-5 h-5 text-gray-500" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <button @click="sidebarOpen = !sidebarOpen"
                        class="hidden lg:flex flex-shrink-0 items-center justify-center w-8 h-8
                               rounded-lg hover:bg-gray-100 transition-all duration-200"
                        x-show="!sidebarOpen">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-dark truncate">
                        @yield('page-title', 'Dashboard')
                    </h2>
                    <p class="text-[11px] text-gray-400 hidden sm:block truncate">
                        @yield('page-subtitle', '')
                    </p>
                </div>
            </div>

            {{-- Centre : Logo --}}
            <div class="absolute left-1/2 -translate-x-1/2 pointer-events-none">
                @if($logo && file_exists(storage_path('app/public/' . $logo)))
                    <img src="{{ Storage::url($logo) }}"
                         alt="{{ App\Models\Parametre::first()?->nom ?? config('app.name', 'svmdev_ges_com') }}"
                         class="h-10 sm:h-14 w-auto max-w-[120px] sm:max-w-[180px] object-contain"/>
                @else
                    <span class="font-bold text-primary text-sm">{{ App\Models\Parametre::first()?->nom ?? config('app.name', 'svmdev_ges_com') }}</span>
                @endif
            </div>

            {{-- Droite --}}
            <div class="flex items-center gap-3">
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 {{ request()->routeIs('caisse.pos', 'devis.create', 'devis.edit') ? 'overflow-hidden p-2 sm:p-3 flex flex-col' : 'overflow-y-auto p-4 lg:p-6' }}"
        style="-webkit-overflow-scrolling: touch;">

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200
                            text-green-800 px-4 py-3 rounded-xl">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                    <button @click="show = false"
                            class="ml-auto text-green-600 hover:text-green-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200
                            text-red-800 px-4 py-3 rounded-xl">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                    <button @click="show = false"
                            class="ml-auto text-red-600 hover:text-red-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@livewireScripts
@stack('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('exception', (e) => {
            if (e.status === 419) window.location.href = '/login';
        });
    });
</script>
</body>
</html>