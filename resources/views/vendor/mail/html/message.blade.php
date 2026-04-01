@php
    $settings  = App\Models\CompanySetting::getSettings();
    $storeName = $settings->name ?? config('app.name');
@endphp

<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
{{ $storeName }}
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} {{ $storeName }}. Tous droits réservés.
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
