@php
    $settings = App\Models\Parametre::first();
    $logo     = $settings->logo;
    $wa       = preg_replace('/[^0-9]/', '', $settings->whatsapp ?? $settings->telephone ?? '');
@endphp

<nav class="bg-white border-b border-gray-100 sticky top-0 z-40"
     x-data="{ mobileMenu: false, mobileSearch: false }">

    {{-- ── Barre principale ── --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 h-18" style="height: 72px;">

            {{-- Logo --}}
            <a href="{{ route('shop.home') }}"
               class="flex items-center gap-2.5 flex-shrink-0">
                @if($logo && file_exists(storage_path('app/public/' . $logo)))
                    <img src="{{ Storage::url($logo) }}"
                         class="h-14 sm:h-16 w-auto max-w-[160px] object-contain"/>
                @else
                    <div class="w-9 h-9 bg-primary rounded-xl flex items-center
                                justify-center flex-shrink-0">
                        <span class="text-white font-bold text-sm">AT</span>
                    </div>
                @endif
                <div class="hidden sm:block">
                    <p class="font-bold text-sm text-dark leading-tight">
                        {{ $settings->nom }}
                    </p>
                    <p class="text-xs leading-tight" style="color: #2DB667;">
                        Dakar, Sénégal
                    </p>
                </div>
            </a>

            {{-- Search bar centrale -- desktop --}}
            <div class="hidden md:flex flex-1 max-w-xl mx-auto">
                <form action="{{ route('shop.catalogue') }}" method="GET"
                      class="w-full">
                    <div class="flex items-center bg-gray-50 border border-gray-200
                                rounded-full px-4 h-10 gap-3 hover:border-primary/50
                                focus-within:border-primary focus-within:bg-white
                                transition-all group">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0
                                    group-focus-within:text-primary transition-colors"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                        <input type="text" name="q"
                               value="{{ request('q') }}"
                               placeholder="Rechercher un plat..."
                               class="flex-1 bg-transparent text-sm text-dark
                                      placeholder-gray-400 focus:outline-none"/>
                        <button type="submit"
                                class="flex-shrink-0 bg-primary text-white text-xs
                                       font-semibold px-3 py-1 rounded-full
                                       hover:bg-primary/90 transition-colors">
                            Chercher
                        </button>
                    </div>
                </form>
            </div>

            {{-- Actions droite --}}
            <div class="flex items-center gap-2 flex-shrink-0 ml-auto md:ml-0">

                {{-- Search mobile --}}
                <button @click="mobileSearch = !mobileSearch"
                        class="md:hidden w-9 h-9 flex items-center justify-center
                               border border-gray-200 rounded-xl hover:bg-gray-50
                               transition-colors">
                    <svg class="w-4 h-4 text-gray-600" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                </button>

                {{-- Panier --}}
                @livewire('shop.cart-icon')

                {{-- Auth --}}
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2 pl-1 pr-3 py-1
                                       border border-gray-200 rounded-full
                                       hover:border-gray-300 transition-colors">
                            <div class="w-7 h-7 rounded-full bg-primary flex items-center
                                        justify-center text-white font-bold text-xs">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-dark">
                                {{ explode(' ', auth()->user()->name)[0] }}
                            </span>
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false"
                             x-transition.opacity
                             class="absolute right-0 top-full mt-2 w-48 bg-white
                                    rounded-2xl shadow-xl border border-gray-100
                                    py-1.5 z-50">
                            @if(auth()->user()->hasAnyRole(['admin', 'gestionnaire']))
                                <a href="{{ route('dashboard') }}"
                                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm
                                          text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('compte.commandes') }}"
                                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm
                                          text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1
                                                 12H4L5 9z"/>
                                    </svg>
                                    Mes commandes
                                </a>
                            @endif
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('compte.logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-4
                                               py-2.5 text-sm text-red-500
                                               hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round" stroke-width="2"
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3
                                                 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0
                                                 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                       class="hidden sm:flex items-center h-9 px-4 border
                              border-gray-200 rounded-full text-sm font-medium
                              text-gray-600 hover:border-gray-300 hover:text-dark
                              transition-all">
                        Connexion
                    </a>
                    <a href="{{ route('compte.register') }}"
                       class="flex items-center h-9 px-4 bg-primary rounded-full
                              text-sm font-semibold text-white hover:bg-primary/90
                              transition-all shadow-lg shadow-primary/20">
                        S'inscrire
                    </a>
                @endauth

                {{-- Menu mobile --}}
                <button @click="mobileMenu = !mobileMenu"
                        class="md:hidden w-9 h-9 flex items-center justify-center
                               border border-gray-200 rounded-xl hover:bg-gray-50
                               transition-colors">
                    <svg x-show="!mobileMenu" class="w-4 h-4 text-gray-600"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenu" class="w-4 h-4 text-gray-600"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Barre catégories desktop ── --}}
    <div class="hidden md:block border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center h-11">

                {{-- Espaceur gauche --}}
                <div class="flex-1"></div>

                {{-- Items centrés --}}
                <div class="flex items-center gap-1 overflow-x-auto scrollbar-hide">

                    <a href="{{ route('shop.home') }}"
                       @class([
                           'flex-shrink-0 inline-flex items-center h-7 px-4 rounded-full text-xs font-semibold transition-all',
                           'bg-primary text-white' => request()->routeIs('shop.home'),
                           'text-gray-500 hover:text-dark hover:bg-gray-100' => !request()->routeIs('shop.home'),
                       ])>
                        Accueil
                    </a>

                    <a href="{{ route('shop.catalogue') }}"
                       @class([
                           'flex-shrink-0 inline-flex items-center h-7 px-4 rounded-full text-xs font-semibold transition-all',
                           'bg-primary text-white' => request()->routeIs('shop.catalogue*') && !request('categorie'),
                           'text-gray-500 hover:text-dark hover:bg-gray-100' => !(request()->routeIs('shop.catalogue*') && !request('categorie')),
                       ])>
                        Tout le catalogue
                    </a>

                    @foreach(App\Models\Categorie::where('est_active', true)->orderBy('ordre')->limit(6)->get() as $cat)
                        <a href="{{ route('shop.catalogue', ['categorie' => $cat->slug]) }}"
                           @class([
                               'flex-shrink-0 inline-flex items-center h-7 px-4 rounded-full text-xs font-semibold transition-all',
                               'bg-primary text-white' => request('categorie') === $cat->slug,
                               'text-gray-500 hover:text-dark hover:bg-gray-100' => request('categorie') !== $cat->slug,
                           ])>
                            {{ $cat->nom }}
                        </a>
                    @endforeach

                </div>

                {{-- Espaceur droit + badge Nouveautés --}}
                <div class="flex-1 flex justify-end">
                    <a href="{{ route('shop.catalogue', ['sort' => 'latest']) }}"
                       class="flex items-center gap-1.5 h-7 px-3 rounded-full
                              text-xs font-semibold transition-all flex-shrink-0"
                       style="background: #FFF8E7; color: #92400e;">
                        <span class="w-1.5 h-1.5 rounded-full"
                              style="background: #2DB667;"></span>
                        Nouveautés
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Search bar mobile déroulante ── --}}
    <div x-show="mobileSearch" x-transition
         class="md:hidden border-t border-gray-100 px-4 py-3">
        <form action="{{ route('shop.catalogue') }}" method="GET">
            <div class="flex items-center bg-gray-50 border border-gray-200
                        rounded-full px-4 h-10 gap-3 focus-within:border-primary
                        focus-within:bg-white transition-all">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input type="text" name="q" placeholder="Rechercher..."
                       autofocus
                       class="flex-1 bg-transparent text-sm focus:outline-none"/>
                <button type="submit"
                        class="flex-shrink-0 bg-primary text-white text-xs
                               font-semibold px-3 py-1 rounded-full">
                    OK
                </button>
            </div>
        </form>
    </div>

    {{-- ── Menu mobile déroulant ── --}}
    <div x-show="mobileMenu" x-transition
         class="md:hidden border-t border-gray-100">
        <div class="px-4 py-4 space-y-1">

            <a href="{{ route('shop.home') }}"
               @click="mobileMenu = false"
               class="flex items-center h-10 px-4 rounded-xl text-sm font-medium
                      transition-colors
                      {{ request()->routeIs('shop.home')
                         ? 'bg-primary/10 text-primary'
                         : 'text-gray-600 hover:bg-gray-50' }}">
                Accueil
            </a>
            <a href="{{ route('shop.catalogue') }}"
               @click="mobileMenu = false"
               class="flex items-center h-10 px-4 rounded-xl text-sm font-medium
                      transition-colors
                      {{ request()->routeIs('shop.catalogue*')
                         ? 'bg-primary/10 text-primary'
                         : 'text-gray-600 hover:bg-gray-50' }}">
                Catalogue
            </a>

            @foreach(App\Models\Categorie::where('est_active', true)->orderBy('ordre')->limit(6)->get() as $cat)
                <a href="{{ route('shop.catalogue', ['categorie' => $cat->slug]) }}"
                   @click="mobileMenu = false"
                   class="flex items-center h-10 px-4 rounded-xl text-sm
                          text-gray-500 hover:bg-gray-50 transition-colors">
                    {{ $cat->nom }}
                </a>
            @endforeach

            @guest
                <div class="flex gap-3 pt-3 border-t border-gray-100">
                    <a href="{{ route('login') }}"
                       class="flex-1 text-center py-2.5 border border-gray-200
                              rounded-full text-sm font-medium text-gray-600">
                        Connexion
                    </a>
                    <a href="{{ route('compte.register') }}"
                       class="flex-1 text-center py-2.5 bg-primary rounded-full
                              text-sm font-semibold text-white">
                        S'inscrire
                    </a>
                </div>
            @endguest
        </div>
    </div>
</nav>