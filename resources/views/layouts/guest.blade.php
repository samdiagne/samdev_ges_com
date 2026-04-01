<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Al-Adab Tech') }} — Connexion</title>
        @if($logo && file_exists(storage_path('app/public/' . $logo)))
            <link rel="icon" type="image/webp" href="{{ Storage::url($logo) }}"/>
        @else
            <link rel="icon" href="/favicon.ico"/>
        @endif
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50">
        @php $settings = App\Models\CompanySetting::getSettings(); $logo = $settings->logo; @endphp

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-6">
                <a href="{{ route('shop.home') }}">
                    @if($logo && file_exists(storage_path('app/public/' . $logo)))
                        <img src="{{ Storage::url($logo) }}"
                             alt="{{ $settings->name }}"
                             class="h-16 w-auto object-contain"/>
                    @else
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                        font-bold text-white text-sm"
                                 style="background-color: #1E6FBF;">
                                AT
                            </div>
                            <span class="font-bold text-lg" style="color: #2D2D2D;">
                                {{ $settings->name ?? 'Al-Adab Tech' }}
                            </span>
                        </div>
                    @endif
                </a>
            </div>

            <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-sm
                        overflow-hidden sm:rounded-2xl border border-gray-100">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
