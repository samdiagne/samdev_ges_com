<!DOCTYPE html>
<html lang="fr"
      x-data="{
          sidebarOpen: true,
          sidebarMobile: false,
          darkMode: localStorage.getItem('dashboard_theme') === 'dark',
          toggleDark() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('dashboard_theme', this.darkMode ? 'dark' : 'light');
          }
      }"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="google-site-verification" content="jbqwi92eZFZEShq8RZBrBJxrYqAdKmJRTZI74Sveets" />
    @php $logo = App\Models\CompanySetting::getSettings()->logo; @endphp
    <title>@yield('title', 'Al-Adab Tech Store') — Dashboard</title>
    @if($logo && file_exists(storage_path('app/public/' . $logo)))
        <link rel="icon" type="image/webp" href="{{ Storage::url($logo) }}"/>
        <link rel="apple-touch-icon" href="{{ Storage::url($logo) }}"/>
    @else
        <link rel="icon" href="/favicon.ico"/>
    @endif
    {{-- Anti-flash : applique le thème + prépare l'overlay avant le rendu Alpine --}}
    <script>
        var _dark = localStorage.getItem('dashboard_theme') === 'dark';
        if (_dark) document.documentElement.classList.add('dark');
    </script>
    <style>
        #page-loader {
            position: fixed; inset: 0; z-index: 9999;
            display: flex; align-items: center; justify-content: center;
            transition: opacity 0.15s ease;
        }
        #page-loader .pl-spinner {
            width: 28px; height: 28px; border-radius: 50%;
            border: 3px solid rgba(30,111,191,0.2);
            border-top-color: #1E6FBF;
            animation: pl-spin 0.6s linear infinite;
        }
        @keyframes pl-spin { to { transform: rotate(360deg); } }
        #page-loader.fade-out { opacity: 0; pointer-events: none; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-light font-sans antialiased">

{{-- Overlay anti-FOUC : masque la page jusqu'à ce qu'Alpine ait initialisé --}}
<div id="page-loader" style="background: {{ request()->cookie('dashboard_theme') === 'dark' ? '#0F172A' : '#F8F9FC' }};">
    <div class="pl-spinner"></div>
</div>
<script>
    // Correction immédiate de la couleur de fond selon le thème stocké
    (function () {
        var l = document.getElementById('page-loader');
        if (!l) return;
        l.style.background = _dark ? '#0F172A' : '#F8F9FC';

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
    <aside :class="sidebarOpen ? 'w-64' : 'w-16'"
           :style="darkMode
               ? 'background-color: #0F172A'
               : 'background: linear-gradient(135deg, #1E6FBF 0%, #1E6FBF 50%, #2D2D2D 100%)'"
           class="hidden lg:flex flex-col text-white flex-shrink-0
                  transition-all duration-300 ease-in-out relative">

        {{-- Header logo --}}
        <div class="flex items-center justify-between px-4 py-4
                    border-b border-white/5">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
        @if($logo && file_exists(storage_path('app/public/' . $logo)))
            <img src="{{ Storage::url($logo) }}"
                 :class="sidebarOpen ? 'h-12 w-auto max-w-[140px]' : 'h-10 w-10'"
                 class="object-contain flex-shrink-0 rounded-lg transition-all duration-300"/>
        @else
            <div class="h-9 w-9 bg-primary rounded-lg flex items-center
                        justify-center font-bold text-white text-sm flex-shrink-0">
                AT
            </div>
        @endif
        <div x-show="sidebarOpen" x-transition.opacity class="overflow-hidden min-w-0">
            <p class="font-bold text-sm text-white truncate leading-tight">Al-Adab Tech</p>
            <p class="text-xs truncate" style="color:#F5A800;">Store Manager</p>
        </div>
    </a>

            {{-- Toggle bouton --}}
            <button @click="sidebarOpen = !sidebarOpen"
                    class="flex-shrink-0 w-6 h-6 rounded-full bg-white/10
                           hover:bg-white/20 flex items-center justify-center
                           transition-colors ml-2">
                <svg :class="sidebarOpen ? 'rotate-180' : ''"
                     class="w-3 h-3 text-white/60 transition-transform duration-300"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">
            @include('layouts.partials.sidebar-nav')
        </nav>

        {{-- User info --}}
    <div class="border-t border-white/5 p-3 relative" x-data="{ userMenuOpen: false }">
        <button @click="userMenuOpen = !userMenuOpen"
                class="w-full flex items-center gap-3 px-2 py-2 rounded-xl
                    hover:bg-white/5 transition-colors cursor-pointer">
            <div class="w-8 h-8 rounded-full bg-primary flex items-center
                        justify-center text-white font-bold text-xs flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div class="overflow-hidden flex-1 min-w-0 text-left">
                <p class="text-sm font-semibold text-white truncate leading-tight">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-xs text-white/40 truncate capitalize">
                    {{ Auth::user()->getRoleNames()->first() }}
                </p>
            </div>
            <svg :class="userMenuOpen ? 'rotate-0' : 'rotate-180'"
                class="w-3.5 h-3.5 text-white/30 transition-transform flex-shrink-0"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Dropdown vers le haut --}}
        <div x-show="userMenuOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            @click.outside="userMenuOpen = false"
            class="absolute bottom-full left-3 right-3 mb-1 bg-white/10
                    rounded-xl overflow-hidden border border-white/10 shadow-xl
                    backdrop-blur-sm">

            <a href="{{ route('shop.home') }}"
               target="_blank"
               class="flex items-center gap-2.5 px-4 py-3 text-sm text-white/70
                       hover:bg-white/10 hover:text-white transition-colors">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14
                             4h6m0 0v6m0-6L10 14"/>
                </svg>
                Voir le site
            </a>

            <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-2.5 px-4 py-3 text-sm text-white/70
                    hover:bg-white/10 hover:text-white transition-colors">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12
                            14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Mon profil
            </a>

            <div class="border-t border-white/5"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2.5 px-4 py-3 text-sm
                            text-red-400 hover:bg-red-500/10 hover:text-red-300
                            transition-colors">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
           x-transition:enter="transition transform duration-300"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition transform duration-300"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           :style="darkMode
               ? 'background-color: #0F172A'
               : 'background: linear-gradient(135deg, #1E6FBF 0%, #1E6FBF 50%, #2D2D2D 100%)'"
           class="fixed inset-y-0 left-0 z-50 w-64 text-white flex flex-col lg:hidden">

        <div class="flex items-center justify-between px-4 py-4
                    border-b border-white/5">
            <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
                @if($logo && file_exists(storage_path('app/public/' . $logo)))
                    <img src="{{ Storage::url($logo) }}"
                         class="h-12 w-auto max-w-[140px] object-contain flex-shrink-0 rounded-lg"/>
                @else
                    <div class="h-9 w-9 bg-primary rounded-lg flex items-center
                                justify-center font-bold text-white text-sm flex-shrink-0">
                        AT
                    </div>
                @endif
                <div>
                    <p class="font-bold text-sm text-white leading-tight">Al-Adab Tech</p>
                    <p class="text-xs" style="color:#F5A800;">Store Manager</p>
                </div>
            </a>
            </div>
            <button @click="sidebarMobile = false"
                    class="w-7 h-7 rounded-full bg-white/10 hover:bg-white/20
                           flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">
            @include('layouts.partials.sidebar-nav')
        </nav>

        <div class="border-t border-white/5 p-3 relative" x-data="{ userMenuOpen: false }">
        <button @click="userMenuOpen = !userMenuOpen"
                class="w-full flex items-center gap-3 px-2 py-2 rounded-xl
                    hover:bg-white/5 transition-colors cursor-pointer">
            <div class="w-8 h-8 rounded-full bg-primary flex items-center
                        justify-center text-white font-bold text-xs flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div class="overflow-hidden flex-1 min-w-0 text-left">
                <p class="text-sm font-semibold text-white truncate leading-tight">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-xs text-white/40 truncate capitalize">
                    {{ Auth::user()->getRoleNames()->first() }}
                </p>
            </div>
            <svg :class="userMenuOpen ? 'rotate-0' : 'rotate-180'"
                class="w-3.5 h-3.5 text-white/30 transition-transform flex-shrink-0"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Dropdown vers le haut --}}
        <div x-show="userMenuOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            @click.outside="userMenuOpen = false"
            class="absolute bottom-full left-3 right-3 mb-1 bg-white/10
                    rounded-xl overflow-hidden border border-white/10 shadow-xl
                    backdrop-blur-sm">

            <a href="{{ route('shop.home') }}"
               target="_blank"
               class="flex items-center gap-2.5 px-4 py-3 text-sm text-white/70
                       hover:bg-white/10 hover:text-white transition-colors">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14
                             4h6m0 0v6m0-6L10 14"/>
                </svg>
                Voir le site
            </a>

            <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-2.5 px-4 py-3 text-sm text-white/70
                    hover:bg-white/10 hover:text-white transition-colors">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12
                            14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Mon profil
            </a>

            <div class="border-t border-white/5"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2.5 px-4 py-3 text-sm
                            text-red-400 hover:bg-red-500/10 hover:text-red-300
                            transition-colors">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
         MAIN CONTENT
    ═══════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col overflow-hidden"
         :class="darkMode ? 'border-l border-white/10' : ''">

        {{-- Topbar --}}
        <header class="relative bg-white border-b border-gray-200 px-4 lg:px-6
               flex items-center justify-between flex-shrink-0 shadow-sm"
                style="min-height: 64px; padding-top: 10px; padding-bottom: 10px;">

            {{-- Gauche : toggle + titre --}}
            <div class="flex items-center gap-3 min-w-0">
                <button @click="sidebarMobile = true"
                        class="lg:hidden flex-shrink-0 flex items-center justify-center w-9 h-9
                            rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-dark truncate">
                        @yield('page-title', 'Dashboard')
                    </h2>
                    <p class="text-xs text-gray-400 hidden sm:block truncate">
                        @yield('page-subtitle', '')
                    </p>
                </div>
            </div>

            {{-- Centre : Logo --}}
            <div class="absolute left-1/2 -translate-x-1/2 pointer-events-none">
                @if($logo && file_exists(storage_path('app/public/' . $logo)))
                    <img src="{{ Storage::url($logo) }}"
                        class="h-14 sm:h-20 w-auto max-w-[200px] object-contain"/>
                @else
                    <span class="font-bold text-primary text-sm">Al-Adab Tech</span>
                @endif
            </div>

            {{-- Droite : date + toggle thème + user --}}
            <div class="flex items-center gap-2" x-data="{ userMenu: false }">
                <span class="hidden md:block text-xs text-gray-400">
                    {{ now()->translatedFormat('l d F Y') }}
                </span>

                {{-- Toggle Dark / Light --}}
                <button @click="toggleDark()"
                        title="Changer le thème"
                        class="relative w-14 h-7 rounded-full border-2 transition-all duration-300 flex-shrink-0
                               focus:outline-none focus:ring-2 focus:ring-primary/40"
                        :class="darkMode
                            ? 'bg-slate-700 border-slate-600'
                            : 'bg-gray-100 border-gray-200'">
                    {{-- Track icons --}}
                    <span class="absolute inset-0 flex items-center justify-between px-1.5 pointer-events-none">
                        {{-- Soleil --}}
                        <svg class="w-3.5 h-3.5 transition-opacity duration-200"
                             :class="darkMode ? 'opacity-30' : 'opacity-100 text-yellow-500'"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.772 17.303a.75.75 0 00-1.06 1.06l1.59 1.591a.75.75 0 001.061-1.06l-1.59-1.591zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.166 5.106a.75.75 0 011.06 1.06L5.636 7.757a.75.75 0 01-1.06-1.06l1.59-1.591z"/>
                        </svg>
                        {{-- Lune --}}
                        <svg class="w-3.5 h-3.5 transition-opacity duration-200"
                             :class="darkMode ? 'opacity-100 text-blue-300' : 'opacity-30 text-gray-400'"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                    {{-- Curseur --}}
                    <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full shadow transition-all duration-300"
                          :class="darkMode
                              ? 'translate-x-7 bg-slate-200'
                              : 'translate-x-0 bg-white'">
                    </span>
                </button>

                {{-- ... reste du user dropdown inchangé --}}
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-6"
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
    // Redirige vers le login si la session Laravel expire (erreur 419 Livewire)
    document.addEventListener('livewire:init', () => {
        Livewire.hook('request', ({ fail }) => {
            fail(({ status, preventDefault }) => {
                if (status === 419) {
                    preventDefault();
                    if (confirm('Votre session a expiré. Cliquez OK pour vous reconnecter.')) {
                        window.location.href = '/login';
                    }
                }
            });
        });
    });
</script>
</body>
</html>