<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    @php
        $settings    = App\Models\Parametre::first();
        $siteName    = $settings->nom ?? 'Mon Restaurant';
        $siteUrl     = config('app.url');
        $defaultDesc = 'Découvrez nos plats faits maison à Dakar. Commande en ligne, livraison rapide, qualité garantie — ' . $siteName;
        $metaTitle   = (trim(strip_tags($__env->yieldContent('title')))
            ? trim(strip_tags($__env->yieldContent('title'))) . ' — '
            : '') . $siteName;
        $metaDesc    = trim(strip_tags($__env->yieldContent('description')))
            ?: $defaultDesc;
        $metaImage   = $settings->logo
            ? Storage::url($settings->logo)
            : $siteUrl . '/images/og-default.jpg';
        $metaUrl     = url()->current();
    @endphp

    {{-- Favicon --}}
    @if($settings->logo && file_exists(storage_path('app/public/' . $settings->logo)))
        <link rel="icon" type="image/webp" href="{{ Storage::url($settings->logo) }}"/>
        <link rel="apple-touch-icon" href="{{ Storage::url($settings->logo) }}"/>
    @else
        <link rel="icon" href="/favicon.ico"/>
    @endif

    {{-- SEO de base --}}
    <title>{!! $metaTitle !!}</title>
    <meta name="description" content="{{ Str::limit($metaDesc, 160) }}"/>
    <meta name="robots" content="index, follow"/>
    <link rel="canonical" href="{{ $metaUrl }}"/>

    {{-- Open Graph (Facebook, WhatsApp, LinkedIn) --}}
    <meta property="og:type"        content="website"/>
    <meta property="og:site_name"   content="{{ $siteName }}"/>
    <meta property="og:title"       content="{{ $metaTitle }}"/>
    <meta property="og:description" content="{{ Str::limit($metaDesc, 200) }}"/>
    <meta property="og:url"         content="{{ $metaUrl }}"/>
    <meta property="og:image"       content="{{ $metaImage }}"/>
    <meta property="og:image:width" content="1200"/>
    <meta property="og:image:height"content="630"/>
    <meta property="og:locale"      content="fr_SN"/>

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image"/>
    <meta name="twitter:title"       content="{{ $metaTitle }}"/>
    <meta name="twitter:description" content="{{ Str::limit($metaDesc, 200) }}"/>
    <meta name="twitter:image"       content="{{ $metaImage }}"/>

    {{-- Stack pour OG produit --}}
    @stack('meta')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        [data-reveal] {
            opacity: 0;
            transition: opacity 0.65s ease, transform 0.65s ease;
            will-change: opacity, transform;
        }
        [data-reveal="up"]    { transform: translateY(36px); }
        [data-reveal="left"]  { transform: translateX(-36px); }
        [data-reveal="right"] { transform: translateX(36px); }
        [data-reveal="fade"]  { transform: none; }
        [data-reveal].is-visible {
            opacity: 1 !important;
            transform: translate(0,0) !important;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

    {{-- Navbar --}}
    @include('shop.partials.navbar')

    {{-- Flash messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 4000)"
             class="fixed top-20 right-4 z-50 flex items-center gap-3
                    bg-green-500 text-white px-5 py-3 rounded-2xl shadow-xl
                    text-sm font-medium">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 4000)"
             class="fixed top-20 right-4 z-50 flex items-center gap-3
                    bg-red-500 text-white px-5 py-3 rounded-2xl shadow-xl
                    text-sm font-medium">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- Contenu --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('shop.partials.footer')

    {{-- Tiroir panier Ajax --}}
    @livewire('shop.cart-drawer')

    @livewireScripts
    @stack('scripts')

    {{-- Scroll reveal --}}
    <script>
        (function () {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var delay = parseInt(entry.target.dataset.revealDelay || 0);
                        setTimeout(function () {
                            entry.target.classList.add('is-visible');
                        }, delay);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -30px 0px' });

            function initReveal() {
                document.querySelectorAll('[data-reveal]').forEach(function (el) {
                    if (!el.classList.contains('is-visible')) {
                        observer.observe(el);
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', initReveal);
        })();
    </script>

    {{-- Empêche le bfcache de restaurer une page périmée (ex: panier vidé puis retour arrière) --}}
    <script>
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) {
                window.location.reload();
            }
        });
    </script>

    {{-- Scroll to top --}}
    <div x-data="{ show: false }"
        x-init="window.addEventListener('scroll', () => show = window.scrollY > 300)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50">
        <button @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                class="w-10 h-10 sm:w-11 sm:h-11 bg-primary hover:bg-primary/90 text-white rounded-xl sm:rounded-2xl
                    shadow-lg shadow-primary/30 flex items-center justify-center
                    transition-all hover:-translate-y-1">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2" d="M5 15l7-7 7 7"/>
            </svg>
        </button>
    </div>
</body>
</html>