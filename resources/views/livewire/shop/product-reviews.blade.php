<div class="mt-12 space-y-8">

    {{-- ── Résumé notes ── --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
        <h2 class="text-lg font-bold text-dark mb-6 flex items-center gap-2">
            ⭐ Avis clients
            <span class="text-sm font-normal text-gray-400">
                ({{ $totalReviews }} avis)
            </span>
        </h2>

        @if($totalReviews > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-8">

                {{-- Note globale --}}
                <div class="flex flex-col items-center justify-center
                            bg-gray-50 rounded-2xl p-6">
                    <p class="text-6xl font-bold text-dark">{{ $avgRating }}</p>
                    <div class="flex items-center gap-1 my-2">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-6 h-6 {{ $i <= round($avgRating)
                                ? 'text-yellow-400' : 'text-gray-200' }}"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902
                                         0l1.07 3.292a1 1 0 00.95.69h3.462c.969
                                         0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0
                                         00-.364 1.118l1.07 3.292c.3.921-.755
                                         1.688-1.54 1.118l-2.8-2.034a1 1 0
                                         00-1.175 0l-2.8 2.034c-.784.57-1.838-.197
                                         -1.539-1.118l1.07-3.292a1 1 0 00-.364
                                         -1.118L2.98 8.72c-.783-.57-.38-1.81.588
                                         -1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-400">sur 5 étoiles</p>
                </div>

                {{-- Répartition --}}
                <div class="space-y-2">
                    @foreach($ratingCounts as $star => $count)
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-medium text-gray-600 w-4">
                                {{ $star }}
                            </span>
                            <svg class="w-4 h-4 text-yellow-400 flex-shrink-0"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902
                                         0l1.07 3.292a1 1 0 00.95.69h3.462c.969
                                         0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0
                                         00-.364 1.118l1.07 3.292c.3.921-.755
                                         1.688-1.54 1.118l-2.8-2.034a1 1 0
                                         00-1.175 0l-2.8 2.034c-.784.57-1.838-.197
                                         -1.539-1.118l1.07-3.292a1 1 0 00-.364
                                         -1.118L2.98 8.72c-.783-.57-.38-1.81.588
                                         -1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <div class="flex-1 bg-gray-100 rounded-full h-2">
                                <div class="h-2 rounded-full bg-yellow-400 transition-all"
                                     style="width: {{ $totalReviews > 0
                                        ? round(($count / $totalReviews) * 100)
                                        : 0 }}%">
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 w-6 text-right">
                                {{ $count }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ── Liste des avis ── --}}
        @if($reviews->count() > 0)
            <div class="space-y-4">
                @foreach($reviews as $review)
                    <div class="bg-gray-50 rounded-2xl p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-primary flex items-center
                                            justify-center text-white font-bold text-sm
                                            flex-shrink-0">
                                    {{ strtoupper(substr($review->author_name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-dark">
                                        {{ $review->author_name }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $review->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            {{-- Étoiles --}}
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating
                                        ? 'text-yellow-400' : 'text-gray-200' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902
                                                 0l1.07 3.292a1 1 0 00.95.69h3.462c.969
                                                 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0
                                                 00-.364 1.118l1.07 3.292c.3.921-.755
                                                 1.688-1.54 1.118l-2.8-2.034a1 1 0
                                                 00-1.175 0l-2.8 2.034c-.784.57-1.838
                                                 -.197-1.539-1.118l1.07-3.292a1 1 0
                                                 00-.364-1.118L2.98 8.72c-.783-.57-.38
                                                 -1.81.588-1.81h3.461a1 1 0 00.951
                                                 -.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="text-sm text-gray-600 leading-relaxed">
                                {{ $review->comment }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-400">
                <p class="text-sm">Aucun avis pour l'instant.</p>
                <p class="text-xs mt-1">Soyez le premier à donner votre avis !</p>
            </div>
        @endif
    </div>

    {{-- ── Formulaire avis ── --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
        <h3 class="text-lg font-bold text-dark mb-6">
            Laisser un avis
        </h3>

        @if($submitted)
            <div class="bg-green-50 border border-green-200 rounded-2xl p-6
                        text-center">
                <p class="text-2xl mb-2">🎉</p>
                <p class="font-semibold text-green-700">Merci pour votre avis !</p>
                <p class="text-sm text-green-600 mt-1">
                    Il sera publié après modération.
                </p>
                <button wire:click="$set('submitted', false)"
                        class="mt-4 text-sm text-green-700 underline">
                    Laisser un autre avis
                </button>
            </div>
        @else
            <form wire:submit="submit" class="space-y-5">

                {{-- Note par étoiles --}}
                <div>
                    <label class="block text-sm font-medium text-dark mb-3">
                        Votre note <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-2"
                         x-data="{ hover: 0 }">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button"
                                    wire:click="setRating({{ $i }})"
                                    @mouseenter="hover = {{ $i }}"
                                    @mouseleave="hover = 0"
                                    class="transition-transform hover:scale-110">
                                <svg class="w-8 h-8 transition-colors"
                                     :class="({{ $i }} <= (hover || {{ $rating }}))
                                        ? 'text-yellow-400' : 'text-gray-200'"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902
                                             0l1.07 3.292a1 1 0 00.95.69h3.462c.969
                                             0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0
                                             00-.364 1.118l1.07 3.292c.3.921-.755
                                             1.688-1.54 1.118l-2.8-2.034a1 1 0
                                             00-1.175 0l-2.8 2.034c-.784.57-1.838
                                             -.197-1.539-1.118l1.07-3.292a1 1 0
                                             00-.364-1.118L2.98 8.72c-.783-.57-.38
                                             -1.81.588-1.81h3.461a1 1 0 00.951
                                             -.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        @endfor
                        <span class="text-sm text-gray-400 ml-2">
                            @switch($rating)
                                @case(1) Très mauvais @break
                                @case(2) Mauvais @break
                                @case(3) Correct @break
                                @case(4) Bien @break
                                @case(5) Excellent @break
                            @endswitch
                        </span>
                    </div>
                    @error('rating')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nom + Email --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Votre nom <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="authorName" type="text"
                               placeholder="Prénom Nom"
                               class="w-full px-3 py-2.5 border rounded-xl text-sm
                                      focus:outline-none transition-colors
                                      {{ $errors->has('authorName')
                                         ? 'border-red-400 bg-red-50'
                                         : 'border-gray-200 bg-gray-50
                                            focus:border-primary focus:bg-white' }}"/>
                        @error('authorName')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1.5">
                            Email
                            <span class="text-gray-400 font-normal">(facultatif)</span>
                        </label>
                        <input wire:model="authorEmail" type="email"
                               placeholder="email@exemple.com"
                               class="w-full px-3 py-2.5 border border-gray-200
                                      rounded-xl text-sm bg-gray-50 focus:outline-none
                                      focus:border-primary focus:bg-white
                                      transition-colors"/>
                    </div>
                </div>

                {{-- Commentaire --}}
                <div>
                    <label class="block text-sm font-medium text-dark mb-1.5">
                        Votre avis <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="comment" rows="4"
                              placeholder="Partagez votre expérience avec ce produit..."
                              class="w-full px-3 py-2.5 border rounded-xl text-sm
                                     focus:outline-none transition-colors resize-none
                                     {{ $errors->has('comment')
                                        ? 'border-red-400 bg-red-50'
                                        : 'border-gray-200 bg-gray-50
                                           focus:border-primary focus:bg-white' }}">
                    </textarea>
                    <div class="flex justify-between mt-1">
                        @error('comment')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @else
                            <span></span>
                        @enderror
                        <p class="text-xs text-gray-400">
                            {{ strlen($comment) }}/1000
                        </p>
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-3 bg-primary hover:bg-primary/90 text-white
                               font-semibold rounded-2xl transition-all shadow-lg
                               shadow-primary/25 flex items-center justify-center gap-2">
                    <svg wire:loading class="animate-spin w-4 h-4" fill="none"
                         viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Publier mon avis
                </button>

                <p class="text-xs text-gray-400 text-center">
                    Les avis sont publiés après validation par notre équipe.
                </p>
            </form>
        @endif
    </div>
</div>