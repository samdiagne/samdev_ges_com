@extends('layouts.shop')
@section('title', 'Questions fréquentes — FAQ')

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-br from-primary via-primary to-dark overflow-hidden py-20">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/5 rounded-full"></div>
        <div class="absolute bottom-0 left-1/4 w-56 h-56 bg-secondary/10 rounded-full translate-y-1/2"></div>
    </div>
    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm border border-white/20
                    px-4 py-1.5 rounded-full text-sm font-medium text-white mb-6">
            <span class="w-1.5 h-1.5 bg-secondary rounded-full"></span>
            Centre d'aide
        </div>
        <h1 class="text-3xl sm:text-4xl font-bold text-white mb-4">Questions fréquentes</h1>
        <p class="text-white/70 text-lg">
            Trouvez rapidement les réponses à vos questions.
        </p>
    </div>
</div>

<div class="bg-light">
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

    @php
    $categories = [
        [
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>',
            'label' => 'Commandes',
            'color' => 'bg-primary/10 text-primary',
            'border'=> 'border-primary/20',
            'faqs'  => [
                ['q' => 'Comment passer une commande ?',
                 'r' => 'Ajoutez les articles souhaités dans votre panier depuis le catalogue, puis cliquez sur "Commander". Renseignez vos coordonnées et votre adresse de livraison, choisissez votre mode de paiement et confirmez. Vous recevrez une confirmation avec le numéro de votre commande.'],
                ['q' => 'Puis-je modifier ou annuler ma commande ?',
                 'r' => 'Les modifications sont possibles tant que votre commande est au statut "En attente". Contactez-nous rapidement par WhatsApp ou par téléphone. Une fois la commande confirmée et en préparation, il n\'est plus possible de la modifier.'],
                ['q' => 'Comment suivre ma commande ?',
                 'r' => 'Connectez-vous à votre compte client et rendez-vous dans la section "Mon espace". Vous verrez l\'état de toutes vos commandes en temps réel avec une barre de progression. Nous vous contacterons également par téléphone à chaque étape importante.'],
                ['q' => 'Combien de temps garde-t-on une commande non récupérée ?',
                 'r' => 'Les commandes non récupérées sont conservées 7 jours à notre point de vente. Passé ce délai, la commande sera annulée et remboursée si elle a déjà été payée.'],
            ],
        ],
        [
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
            'label' => 'Paiement',
            'color' => 'bg-secondary/10 text-secondary',
            'border'=> 'border-secondary/20',
            'faqs'  => [
                ['q' => 'Quels modes de paiement acceptez-vous ?',
                 'r' => 'Nous acceptons Wave, Orange Money, Mix by Yass, Djamo, le paiement à la livraison (cash) et le virement bancaire. Choisissez le mode qui vous convient lors du checkout.'],
                ['q' => 'Le paiement en ligne est-il sécurisé ?',
                 'r' => 'Oui, tous nos paiements en ligne sont traités par Paydunya, une plateforme de paiement certifiée. Vos données bancaires ne sont jamais stockées sur notre serveur.'],
                ['q' => 'Est-il possible de payer en plusieurs fois ?',
                 'r' => 'Le paiement en plusieurs fois n\'est pas disponible en ligne, mais peut être discuté en boutique pour les achats importants. Contactez-nous pour plus d\'informations.'],
                ['q' => 'Ma transaction a échoué, que faire ?',
                 'r' => 'Vérifiez que vous avez suffisamment de solde et que votre réseau est stable. Si le problème persiste, contactez-nous directement par WhatsApp avec le numéro de votre commande. Nous vous aiderons à finaliser le paiement.'],
            ],
        ],
        [
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1.18 12.56A2 2 0 008.17 22h7.66a2 2 0 001.99-1.44L19 8M10 12v6M14 12v6"/>',
            'label' => 'Livraison',
            'color' => 'bg-accent/10 text-primary',
            'border'=> 'border-accent/20',
            'faqs'  => [
                ['q' => 'Quelles sont les zones de livraison ?',
                 'r' => 'Nous livrons dans tout Dakar et ses environs (Pikine, Guédiawaye, Rufisque, Thiès). Pour d\'autres régions du Sénégal, contactez-nous pour un devis de livraison personnalisé.'],
                ['q' => 'Quel est le délai de livraison ?',
                 'r' => 'La livraison à Dakar est effectuée sous 24 à 48h après confirmation de la commande. Pour les banlieues et autres villes, comptez 2 à 5 jours ouvrables.'],
                ['q' => 'Puis-je récupérer ma commande en boutique ?',
                 'r' => 'Oui, le retrait en boutique est gratuit et disponible dès que votre commande est prête (généralement sous quelques heures). Vous serez contacté par téléphone dès que c\'est prêt.'],
                ['q' => 'Les frais de livraison sont-ils inclus dans le prix ?',
                 'r' => 'Les frais de livraison sont calculés lors du checkout selon votre zone. Le retrait en boutique est toujours gratuit.'],
            ],
        ],
        [
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
            'label' => 'Garanties & SAV',
            'color' => 'bg-secondary/10 text-secondary',
            'border'=> 'border-secondary/20',
            'faqs'  => [
                ['q' => 'Quelle est la garantie sur les produits ?',
                 'r' => 'Tous nos produits bénéficient d\'une garantie constructeur. Les smartphones et ordinateurs sont garantis 12 mois minimum. Les accessoires sont garantis 3 à 6 mois selon le type de produit.'],
                ['q' => 'Comment activer le service après-vente ?',
                 'r' => 'En cas de panne ou de défaut, rapportez le produit en boutique avec votre preuve d\'achat. Notre technicien évaluera le problème et vous proposera une réparation, un échange ou un remboursement selon le cas.'],
                ['q' => 'Les produits sont-ils originaux ?',
                 'r' => 'Absolument. Nous ne vendons que des produits 100% originaux provenant de fournisseurs certifiés. Chaque produit dispose de son numéro IMEI ou de série vérifiable.'],
                ['q' => 'Puis-je retourner un produit ?',
                 'r' => 'Les retours sont acceptés sous 7 jours si le produit est dans son emballage d\'origine, non utilisé et accompagné de la facture. Hors ce délai, seules les pannes couvertes par la garantie sont prises en charge.'],
            ],
        ],
    ];
    @endphp

    {{-- Catégories de FAQ --}}
    <div class="space-y-10">
        @foreach($categories as $cat)
            <div>
                {{-- Header catégorie --}}
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 {{ $cat['color'] }} rounded-xl flex items-center justify-center border {{ $cat['border'] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $cat['icon'] !!}
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-dark">{{ $cat['label'] }}</h2>
                </div>

                {{-- Accordéon --}}
                <div class="space-y-2.5" x-data="{ open: null }">
                    @foreach($cat['faqs'] as $i => $item)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden"
                             x-data="{ id: '{{ $loop->parent->index }}_{{ $i }}' }">
                            <button @click="open === id ? open = null : open = id"
                                    class="w-full flex items-center justify-between px-6 py-4 text-left
                                           hover:bg-light transition-colors gap-4">
                                <span class="text-sm font-semibold text-dark">{{ $item['q'] }}</span>
                                <div class="shrink-0 w-6 h-6 rounded-lg flex items-center justify-center transition-colors"
                                     :class="open === id ? 'bg-primary text-white' : 'bg-gray-100 text-gray-400'">
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                         :class="open === id ? 'rotate-45' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </div>
                            </button>
                            <div x-show="open === id"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="px-6 pb-5 border-t border-gray-50">
                                <p class="text-sm text-gray-600 leading-relaxed pt-4">
                                    {{ $item['r'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- CTA contact --}}
    <div class="mt-16 relative bg-gradient-to-br from-primary to-dark rounded-3xl p-8 text-center text-white">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-40 h-40 bg-secondary/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        </div>
        <div class="relative">
            <div class="w-14 h-14 bg-white/15 border border-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold mb-2">Vous n'avez pas trouvé votre réponse ?</h3>
            <p class="text-white/70 text-sm mb-6 max-w-sm mx-auto">
                Notre équipe est disponible pour répondre à toutes vos questions.
            </p>
            <div class="flex items-center justify-center gap-3 flex-wrap">
                <a href="{{ route('shop.contact') }}"
                   class="inline-flex items-center gap-2 bg-white text-primary font-semibold
                          px-5 py-2.5 rounded-xl hover:bg-light transition-all text-sm shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Envoyer un message
                </a>
                @php
                    $settings = App\Models\CompanySetting::getSettings();
                    $wa = preg_replace('/[^0-9]/', '', $settings->whatsapp ?? '');
                    $sujets = [
                        ['label' => '🛒 Passer une commande',        'msg' => "Bonjour *{$settings->name}* 👋\nJe souhaite passer une commande. Pouvez-vous m'aider ?"],
                        ['label' => '📦 Disponibilité d\'un produit', 'msg' => "Bonjour *{$settings->name}* 👋\nJe voudrais savoir si un produit est disponible en stock."],
                        ['label' => '💰 Demande de prix',             'msg' => "Bonjour *{$settings->name}* 👋\nJe souhaite avoir des informations sur vos tarifs."],
                        ['label' => '🚚 Infos livraison',             'msg' => "Bonjour *{$settings->name}* 👋\nJe voudrais des informations sur vos délais et frais de livraison."],
                        ['label' => '🔧 Service après-vente',         'msg' => "Bonjour *{$settings->name}* 👋\nJ'ai besoin d'assistance pour un produit acheté."],
                        ['label' => '❓ Autre question',               'msg' => "Bonjour *{$settings->name}* 👋\nJ'ai une question à vous poser."],
                    ];
                @endphp
                @if($settings->whatsapp)
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="inline-flex items-center gap-2 bg-secondary hover:bg-secondary/90 text-dark
                                       font-semibold px-5 py-2.5 rounded-xl transition-all text-sm shadow-sm shadow-secondary/25">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            WhatsApp
                            <svg :class="open ? 'rotate-180' : ''"
                                 class="w-3.5 h-3.5 transition-transform duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             @click.outside="open = false"
                             class="absolute left-0 sm:left-auto sm:right-0 top-full mt-2
                                    w-64 bg-white rounded-2xl shadow-2xl border border-gray-100
                                    p-2 z-50">
                            <p class="text-xs font-semibold text-gray-400 px-3 py-1.5">Choisissez un sujet :</p>
                            @foreach($sujets as $sujet)
                                <a href="https://wa.me/{{ $wa }}?text={{ urlencode($sujet['msg']) }}"
                                   target="_blank"
                                   class="flex items-center justify-between gap-2 px-3 py-2.5
                                          text-sm font-medium text-gray-700 rounded-xl
                                          hover:bg-green-50 hover:text-green-700 transition-colors group">
                                    <span>{{ $sujet['label'] }}</span>
                                    <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-green-500
                                                group-hover:translate-x-0.5 transition-all shrink-0"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
</div>
@endsection
