@extends('layouts.shop')
@section('title', 'Conditions Générales de Vente')

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-br from-primary via-primary to-dark overflow-hidden py-14">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/5 rounded-full"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-secondary/10 rounded-full -translate-x-1/2 translate-y-1/2"></div>
    </div>
    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm border border-white/20
                    px-4 py-1.5 rounded-full text-sm font-medium text-white mb-6">
            <span class="w-1.5 h-1.5 bg-secondary rounded-full"></span>
            Document légal
        </div>
        <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">
            Conditions Générales de Vente
        </h1>
        <p class="text-white/60 text-sm">
            En vigueur au {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('D MMMM YYYY') }}
            &nbsp;·&nbsp; {{ $settings->nom ?? 'Maffood' }}
        </p>
    </div>
</div>

<div class="bg-light">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="lg:grid lg:grid-cols-4 lg:gap-12">

        {{-- Table des matières --}}
        <aside class="hidden lg:block">
            <div class="sticky top-24">
                <p class="text-xs font-bold text-primary uppercase tracking-widest mb-4">
                    Sommaire
                </p>
                <nav class="space-y-1">
                    @foreach([
                        ['#article-1',  '1. Identification'],
                        ['#article-2',  '2. Objet'],
                        ['#article-3',  '3. Menu'],
                        ['#article-4',  '4. Commandes'],
                        ['#article-5',  '5. Prix'],
                        ['#article-6',  '6. Paiement'],
                        ['#article-7',  '7. Livraison'],
                        ['#article-8',  '8. Hygiène'],
                        ['#article-9',  '9. Réclamations'],
                        ['#article-10', '10. Données'],
                        ['#article-11', '11. Responsabilité'],
                        ['#article-12', '12. Droit applicable'],
                    ] as [$href, $label])
                        <a href="{{ $href }}"
                           class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs
                                  text-gray-500 hover:text-primary hover:bg-primary/5 transition-colors">
                            <span class="w-1 h-1 bg-primary/30 rounded-full shrink-0"></span>
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>

                <div class="mt-8 p-4 bg-secondary/10 border border-secondary/20 rounded-2xl">
                    <p class="text-xs font-semibold text-secondary mb-1">Une question ?</p>
                    <p class="text-xs text-gray-500 mb-3">Notre équipe vous répond.</p>
                    <a href="{{ route('shop.contact') }}"
                       class="block text-center text-xs font-semibold bg-primary text-white
                              py-2 rounded-xl hover:bg-primary/90 transition-colors">
                        Nous contacter
                    </a>
                </div>
            </div>
        </aside>

        {{-- Contenu --}}
        <div class="lg:col-span-3 space-y-8">

            @php
            function cgvSection(string $id, string $num, string $title): void {
                echo '<div id="' . $id . '" class="scroll-mt-28">';
                echo '<div class="flex items-center gap-3 mb-4">';
                echo '<span class="w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center text-sm font-bold shrink-0">' . $num . '</span>';
                echo '<h2 class="text-lg font-bold text-dark">' . $title . '</h2>';
                echo '</div>';
            }
            @endphp

            {{-- Article 1 --}}
            @php cgvSection('article-1', '1', 'Identification du vendeur') @endphp
            <div class="bg-white rounded-2xl border border-primary/10 p-5 text-sm text-gray-600 leading-relaxed space-y-1.5">
                <p><span class="font-semibold text-dark">Raison sociale :</span> {{ $settings->nom ?? 'Maffood' }}</p>
                @if($settings->adresse ?? false)
                    <p><span class="font-semibold text-dark">Adresse :</span> {{ $settings->adresse }}, Dakar, Sénégal</p>
                @endif
                @if($settings->telephone ?? false)
                    <p><span class="font-semibold text-dark">Téléphone :</span>
                        <a href="tel:{{ $settings->telephone }}" class="text-primary hover:underline">{{ $settings->telephone }}</a>
                    </p>
                @endif
                @if($settings->email ?? false)
                    <p><span class="font-semibold text-dark">Email :</span>
                        <a href="mailto:{{ $settings->email }}" class="text-primary hover:underline">{{ $settings->email }}</a>
                    </p>
                @endif
            </div>
            </div>

            {{-- Article 2 --}}
            @php cgvSection('article-2', '2', 'Objet') @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-sm text-gray-600 leading-relaxed space-y-3">
                <p>Les présentes Conditions Générales de Vente (CGV) régissent l'ensemble des relations commerciales entre <strong class="text-dark">{{ $settings->nom ?? 'Maffood' }}</strong> (ci-après « le Restaurant ») et tout acheteur (ci-après « le Client ») effectuant une commande via le site en ligne ou sur place.</p>
                <p>Tout achat implique l'acceptation pleine et entière des présentes CGV. Le Client déclare avoir la capacité légale de contracter et être majeur ou émancipé.</p>
            </div>
            </div>

            {{-- Article 3 --}}
            @php cgvSection('article-3', '3', 'Menu et disponibilité') @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-sm text-gray-600 leading-relaxed space-y-3">
                <p>Les plats proposés sont ceux figurant sur notre menu en ligne, dans la limite des disponibilités du jour. Le Restaurant se réserve le droit de modifier le menu à tout moment.</p>
                <p>Chaque plat est accompagné d'une description, d'images représentatives et de son prix. Les photographies sont les plus fidèles possible mais peuvent légèrement différer du plat servi selon les arrivages.</p>
                <p>En cas d'indisponibilité d'un plat après validation d'une commande, le Client sera informé dans les meilleurs délais et pourra choisir un plat de remplacement ou un remboursement intégral.</p>
            </div>
            </div>

            {{-- Article 4 --}}
            @php cgvSection('article-4', '4', 'Commandes') @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-sm text-gray-600 leading-relaxed space-y-3">
                <p>La validation d'une commande vaut acceptation des prix, du menu et des présentes CGV. Un email de confirmation est envoyé au Client dès réception de la commande.</p>
                <p>Le Restaurant se réserve le droit d'annuler toute commande suspecte, frauduleuse ou passée de mauvaise foi, ainsi que toute commande dont le paiement ne serait pas confirmé.</p>
                <p>Toute modification ou annulation après confirmation n'est acceptée que si la commande est encore au statut « En attente » et doit être signalée immédiatement par téléphone ou WhatsApp.</p>
            </div>
            </div>

            {{-- Article 5 --}}
            @php cgvSection('article-5', '5', 'Prix') @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-sm text-gray-600 leading-relaxed space-y-3">
                <p>Les prix sont indiqués en Francs CFA (FCFA). Le Restaurant se réserve le droit de modifier ses prix à tout moment, étant entendu que le prix applicable est celui en vigueur au moment de la validation de la commande.</p>
                <p>Les frais de livraison éventuels sont indiqués clairement lors du passage de commande avant confirmation définitive. Le retrait au restaurant est toujours gratuit.</p>
            </div>
            </div>

            {{-- Article 6 --}}
            @php cgvSection('article-6', '6', 'Modalités de paiement') @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-sm text-gray-600 leading-relaxed space-y-3">
                <p>Le paiement s'effectue au moment de la commande par les moyens suivants :</p>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    @foreach(['Wave', 'Orange Money', 'Carte bancaire', 'Espèces', 'Paiement à la livraison'] as $mode)
                        <div class="flex items-center gap-2 bg-light rounded-xl px-3 py-2">
                            <span class="w-2 h-2 bg-primary rounded-full shrink-0"></span>
                            <span class="text-xs font-medium text-dark">{{ $mode }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="mt-2">Les paiements en ligne sont sécurisés par <strong class="text-dark">Paydunya</strong>. En cas d'échec de paiement, la commande n'est pas validée. Le Restaurant ne conserve aucune donnée bancaire.</p>
            </div>
            </div>

            {{-- Article 7 --}}
            @php cgvSection('article-7', '7', 'Livraison') @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-sm text-gray-600 leading-relaxed space-y-3">
                <p>La livraison est effectuée à l'adresse indiquée par le Client lors de la commande. Le Client doit s'assurer de l'exactitude des informations de livraison.</p>
                <div class="bg-light rounded-xl overflow-hidden border border-gray-100">
                    <div class="grid grid-cols-2 bg-primary px-4 py-2 text-xs font-semibold text-white">
                        <span>Zone</span>
                        <span class="text-right">Délai estimé</span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div class="grid grid-cols-2 px-4 py-2.5 text-sm">
                            <span class="text-gray-600">Dakar centre</span>
                            <span class="font-semibold text-dark text-right">30 – 60 min</span>
                        </div>
                        <div class="grid grid-cols-2 px-4 py-2.5 text-sm">
                            <span class="text-gray-600">Banlieue (Pikine, Guédiawaye…)</span>
                            <span class="font-semibold text-dark text-right">1h – 1h30</span>
                        </div>
                        <div class="grid grid-cols-2 px-4 py-2.5 text-sm">
                            <span class="text-gray-600">Zones périphériques</span>
                            <span class="font-semibold text-dark text-right">1h30 – 2h</span>
                        </div>
                    </div>
                </div>
                <p>Ces délais sont donnés à titre indicatif et dépendent du volume de commandes. Le Restaurant ne saurait être tenu responsable des retards dus à des cas de force majeure ou à une forte affluence.</p>
            </div>
            </div>

            {{-- Article 8 --}}
            @php cgvSection('article-8', '8', 'Hygiène et qualité') @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-sm text-gray-600 leading-relaxed space-y-3">
                <p>Le Restaurant s'engage à respecter les normes d'hygiène alimentaire en vigueur au Sénégal. Tous nos plats sont préparés avec des ingrédients frais et dans le respect des règles sanitaires.</p>
                <p>Les plats sont préparés à la commande et doivent être consommés dans un délai raisonnable après livraison. Le Restaurant ne saurait être tenu responsable en cas de mauvaise conservation par le Client après réception.</p>
                <p>Les allergènes présents dans nos plats sont indiqués dans la description lorsque disponible. En cas d'allergie ou d'intolérance alimentaire, le Client est prié de nous contacter avant de passer commande.</p>
            </div>
            </div>

            {{-- Article 9 --}}
            @php cgvSection('article-9', '9', 'Réclamations et remboursements') @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-sm text-gray-600 leading-relaxed space-y-3">
                <p>En cas de commande non conforme (plat manquant, erreur de préparation), le Client doit signaler le problème dans l'heure suivant la livraison par téléphone ou WhatsApp avec une photo à l'appui.</p>
                <p>Étant donné la nature périssable des denrées alimentaires, les retours ne sont pas acceptés. Toutefois, en cas de non-conformité avérée, le Restaurant s'engage à remplacer le plat ou à effectuer un remboursement.</p>
                <p>En cas de remboursement accepté, il sera effectué dans les 5 jours ouvrables par le même moyen de paiement utilisé lors de l'achat.</p>
            </div>
            </div>

            {{-- Article 10 --}}
            @php cgvSection('article-10', '10', 'Protection des données personnelles') @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-sm text-gray-600 leading-relaxed space-y-3">
                <p>Les informations collectées lors de la commande sont nécessaires au traitement et à la livraison de celle-ci. Elles ne sont jamais revendues à des tiers.</p>
                <p>Le Client dispose d'un droit d'accès, de rectification et de suppression de ses données en contactant le Vendeur à l'adresse email indiquée ci-dessus.</p>
            </div>
            </div>

            {{-- Article 11 --}}
            @php cgvSection('article-11', '11', 'Limitation de responsabilité') @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-sm text-gray-600 leading-relaxed">
                <p>Le Restaurant s'engage à mettre en œuvre tous les moyens nécessaires pour assurer la disponibilité du site et la qualité des plats. Cependant, sa responsabilité ne pourra être engagée en cas de force majeure, de dysfonctionnements du réseau Internet, ou de toute circonstance indépendante de sa volonté.</p>
            </div>
            </div>

            {{-- Article 12 --}}
            @php cgvSection('article-12', '12', 'Droit applicable et juridiction') @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-sm text-gray-600 leading-relaxed space-y-3">
                <p>Les présentes CGV sont soumises au droit sénégalais. En cas de litige, les parties s'efforceront de trouver une solution amiable. À défaut, le litige sera porté devant les tribunaux compétents de Dakar.</p>
                <p class="text-xs text-gray-400">
                    Dernière mise à jour : {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('D MMMM YYYY') }}
                </p>
            </div>
            </div>

            {{-- CTA --}}
            <div class="bg-gradient-to-r from-primary/10 to-secondary/10 border border-primary/15
                        rounded-2xl p-6 text-center">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-dark mb-1">Des questions sur nos CGV ?</p>
                <p class="text-sm text-gray-500 mb-4">Notre équipe est à votre disposition.</p>
                <a href="{{ route('shop.contact') }}"
                   class="inline-flex items-center gap-2 bg-primary text-white text-sm font-semibold
                          px-5 py-2.5 rounded-xl hover:bg-primary/90 transition-all shadow-sm shadow-primary/25">
                    Nous contacter
                </a>
            </div>

        </div>
    </div>
</div>
</div>
@endsection
