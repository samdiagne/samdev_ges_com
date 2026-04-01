<button wire:click="$dispatch('open-cart')"
        class="relative flex items-center justify-center w-10 h-10
               rounded-xl hover:bg-gray-100 transition-colors">
    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
         viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293
                 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100
                 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
    </svg>
    @if($count > 0)
        <span class="absolute -top-1 -right-1 w-5 h-5 bg-primary rounded-full
                     text-white text-xs font-bold flex items-center justify-center">
            {{ $count > 9 ? '9+' : $count }}
        </span>
    @endif
</button>