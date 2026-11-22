<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Ticket #{{ $vente->reference }}</title>
    <style>
        /* ── Reset & format ticket 80mm pour imprimante thermique ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, Helvetica, sans-serif;
            font-size: 13px;
            font-weight: 600; /* Graisse renforcée pour que la tête thermique chauffe bien chaque lettre */
            line-height: 1.35;
            width: 80mm;
            max-width: 80mm;
            margin: 0 auto;
            padding: 6px 8px;
            color: #000;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ── Éléments de typographie ── */
        .center   { text-align: center; }
        .right    { text-align: right; }
        .bold     { font-weight: 800; }
        .black    { font-weight: 900; }
        .big      { font-size: 17px; font-weight: 900; }
        .medium   { font-size: 14px; font-weight: 700; }
        .small    { font-size: 11px; font-weight: 700; }
        .tiny     { font-size: 10px; font-weight: 600; }

        .sep {
            border: none;
            border-top: 1.5px dashed #000;
            margin: 6px 0;
        }

        .double-sep {
            border: none;
            border-top: 2.5px solid #000;
            margin: 6px 0;
        }

        /* ── Tableau lignes ── */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            padding: 3px 0;
            vertical-align: top;
            font-weight: 600;
        }
        .col-qty   { width: 14%; font-weight: 800; font-size: 13px; }
        .col-name  { width: 54%; font-weight: 700; font-size: 13px; }
        .col-price { width: 32%; text-align: right; font-weight: 800; font-size: 13px; }

        .totals td {
            padding: 2px 0;
            font-size: 13px;
        }
        .totals .label {
            text-align: left;
            font-weight: 700;
        }
        .totals .value {
            text-align: right;
            font-weight: 800;
        }

        .grand-total .label,
        .grand-total .value {
            font-size: 16px;
            font-weight: 900;
            padding: 5px 0;
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
        }

        /* ── Impression ── */
        @media print {
            body {
                width: 80mm;
                padding: 4px 6px;
            }
            .no-print { display: none !important; }
        }

        /* ── Aperçu écran ── */
        @media screen {
            body {
                margin: 20px auto;
                border: 1px solid #ccc;
                box-shadow: 0 4px 15px rgba(0,0,0,0.12);
                border-radius: 6px;
                padding: 14px 16px;
            }
            .no-print {
                display: flex;
                gap: 8px;
                justify-content: center;
                margin-top: 16px;
            }
            .btn-print, .btn-close {
                padding: 9px 22px;
                border: none;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 800;
                cursor: pointer;
            }
            .btn-print {
                background: #F27B20;
                color: #fff;
            }
            .btn-close {
                background: #e5e7eb;
                color: #111;
            }
        }
    </style>
</head>
<body>

    {{-- ═══════════════════════════════════════
         EN-TÊTE ENTREPRISE
    ═══════════════════════════════════════ --}}
    <div class="center">
        @if($settings?->logo)
            <img src="{{ asset('storage/' . $settings->logo) }}"
                 alt="{{ $settings->nom }}"
                 style="max-width: 130px; max-height: 55px; margin-bottom: 4px; filter: grayscale(100%) contrast(160%);"/>
            <br/>
        @endif
        <div class="big">{{ $settings?->nom ?? "Maf'Food" }}</div>
        @if($settings?->adresse)
            <div class="small" style="margin-top: 1px;">{{ $settings->adresse }}</div>
        @endif
        @if($settings?->telephone)
            <div class="small bold">Tél : {{ $settings->telephone }}</div>
        @endif
    </div>

    <hr class="double-sep"/>

    {{-- ═══════════════════════════════════════
         INFOS VENTE
    ═══════════════════════════════════════ --}}
    <div style="font-size: 12px; font-weight: 600; line-height: 1.45;">
        <div><span class="bold">Ticket :</span> {{ $vente->reference }}</div>
        <div><span class="bold">Date :</span> {{ $vente->vendu_le->format('d/m/Y H:i') }}</div>
        <div><span class="bold">Caissier :</span> {{ $vente->user->name ?? '—' }}</div>
        @if($vente->client)
            <div><span class="bold">Client :</span> {{ $vente->client->nom }}</div>
        @endif
        <div><span class="bold">Paiement :</span> {{ $vente->mode_paiement_libelle }}
            @if($vente->mode_paiement === 'multiple')
                <div style="font-size: 11px; color: #333; margin-top: 2px; padding-left: 4px;">
                    @if($vente->montant_cash > 0) <div>• Espèces : {{ number_format($vente->montant_cash, 0, ',', ' ') }} {{ $devise }}</div> @endif
                    @if($vente->montant_wave > 0) <div>• Wave : {{ number_format($vente->montant_wave, 0, ',', ' ') }} {{ $devise }}</div> @endif
                    @if($vente->montant_om > 0) <div>• OM : {{ number_format($vente->montant_om, 0, ',', ' ') }} {{ $devise }}</div> @endif
                    @if($vente->montant_carte > 0) <div>• Carte : {{ number_format($vente->montant_carte, 0, ',', ' ') }} {{ $devise }}</div> @endif
                </div>
            @endif
        </div>
    </div>

    <hr class="sep"/>

    {{-- ═══════════════════════════════════════
         LIGNES DE VENTE
    ═══════════════════════════════════════ --}}
    <table>
        <thead>
            <tr style="border-bottom: 1.5px solid #000; font-size: 12px;">
                <td class="col-qty bold">Qté</td>
                <td class="col-name bold">Désignation</td>
                <td class="col-price bold">Montant</td>
            </tr>
        </thead>
        <tbody>
            @foreach($vente->lignes as $ligne)
                <tr style="border-bottom: 1px dotted #bbb;">
                    <td class="col-qty bold">{{ $ligne->quantite }}x</td>
                    <td class="col-name">
                        <span class="bold">{{ $ligne->nom_produit }}</span>
                        @if(! empty($ligne->details))
                            <br/><span style="font-size: 11px; font-weight: 600; color: #444; line-height: 1.2;">{{ $ligne->details }}</span>
                        @endif
                        @if($ligne->remise > 0)
                            <br/><span class="small">Remise : -{{ number_format($ligne->remise, 0, ',', ' ') }}</span>
                        @endif
                    </td>
                    <td class="col-price">{{ number_format($ligne->total, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr class="sep"/>

    {{-- ═══════════════════════════════════════
         TOTAUX
    ═══════════════════════════════════════ --}}
    <table class="totals">
        <tr>
            <td class="label">Sous-total</td>
            <td class="value">{{ number_format($vente->total + $vente->remise, 0, ',', ' ') }} {{ $devise }}</td>
        </tr>

        @if($vente->remise > 0)
            <tr>
                <td class="label">Remise</td>
                <td class="value">-{{ number_format($vente->remise, 0, ',', ' ') }} {{ $devise }}</td>
            </tr>
        @endif

        <tr class="grand-total">
            <td class="label">TOTAL</td>
            <td class="value">{{ number_format($vente->total, 0, ',', ' ') }} {{ $devise }}</td>
        </tr>

        <tr>
            <td class="label">Reçu</td>
            <td class="value">{{ number_format($vente->montant_recu, 0, ',', ' ') }} {{ $devise }}</td>
        </tr>

        @if($vente->monnaie_rendue > 0)
            <tr>
                <td class="label bold">Monnaie</td>
                <td class="value">{{ number_format($vente->monnaie_rendue, 0, ',', ' ') }} {{ $devise }}</td>
            </tr>
            @if(($vente->monnaie_rendue_wave ?? 0) > 0)
                <tr style="font-size: 11px;">
                    <td class="label" style="padding-left: 8px; color: #555;">↳ dont par Wave :</td>
                    <td class="value" style="color: #0284c7;">{{ number_format($vente->monnaie_rendue_wave, 0, ',', ' ') }} {{ $devise }}</td>
                </tr>
                @if(($vente->monnaie_rendue_especes ?? 0) > 0)
                    <tr style="font-size: 11px;">
                        <td class="label" style="padding-left: 8px; color: #555;">↳ dont Espèces :</td>
                        <td class="value">{{ number_format($vente->monnaie_rendue_especes, 0, ',', ' ') }} {{ $devise }}</td>
                    </tr>
                @endif
            @endif
        @endif
    </table>

    <hr class="double-sep"/>

    {{-- ═══════════════════════════════════════
         PIED DE TICKET
    ═══════════════════════════════════════ --}}
    <div class="center" style="font-size: 11px; font-weight: 700; line-height: 1.4;">
        <p class="bold" style="font-size: 12px; margin-bottom: 2px;">Merci pour votre visite !</p>
        <p>{{ $settings?->nom ?? "Maf'Food" }}</p>
        @if($settings?->facebook || $settings?->instagram)
            <p style="margin-top: 2px;">
                @if($settings?->facebook) FB: {{ $settings->facebook }} @endif
                @if($settings?->instagram) IG: {{ $settings->instagram }} @endif
            </p>
        @endif
        <p style="margin-top: 4px; font-size: 10px; font-weight: 600;">
            {{ $vente->reference }} — {{ $vente->vendu_le->format('d/m/Y H:i') }}
        </p>
    </div>

    {{-- ═══════════════════════════════════════
         BOUTONS (écran uniquement)
    ═══════════════════════════════════════ --}}
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Imprimer</button>
        <button class="btn-close" onclick="window.close()">Fermer</button>
    </div>

    {{-- Auto-print si paramètre ?auto=1 --}}
    @if(request('auto'))
        <script>
            window.addEventListener('load', function () {
                setTimeout(function () { window.print(); }, 400);
            });
        </script>
    @endif

</body>
</html>
