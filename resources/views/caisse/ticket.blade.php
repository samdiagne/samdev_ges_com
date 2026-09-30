<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Ticket #{{ $vente->reference }}</title>
    <style>
        /* ── Reset & format ticket 80mm ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', 'Consolas', monospace;
            font-size: 12px;
            line-height: 1.4;
            width: 80mm;
            max-width: 80mm;
            margin: 0 auto;
            padding: 8px 10px;
            color: #000;
            background: #fff;
        }

        /* ── Éléments ── */
        .center  { text-align: center; }
        .right   { text-align: right; }
        .bold    { font-weight: bold; }
        .big     { font-size: 16px; }
        .small   { font-size: 10px; }

        .sep {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .double-sep {
            border: none;
            border-top: 2px solid #000;
            margin: 6px 0;
        }

        /* ── Tableau lignes ── */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .col-qty  { width: 15%; }
        .col-name { width: 55%; }
        .col-price { width: 30%; text-align: right; }

        .totals td { padding: 1px 0; }
        .totals .label { text-align: left; }
        .totals .value { text-align: right; font-weight: bold; }

        .grand-total .label,
        .grand-total .value {
            font-size: 14px;
            font-weight: bold;
            padding: 4px 0;
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
                border: 1px solid #ddd;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                border-radius: 4px;
                padding: 12px 14px;
            }
            .no-print {
                display: flex;
                gap: 8px;
                justify-content: center;
                margin-top: 16px;
            }
            .btn-print, .btn-close {
                padding: 8px 20px;
                border: none;
                border-radius: 6px;
                font-size: 13px;
                font-weight: bold;
                cursor: pointer;
            }
            .btn-print {
                background: #F27B20;
                color: #fff;
            }
            .btn-close {
                background: #e5e7eb;
                color: #333;
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
                 style="max-width: 120px; max-height: 50px; margin-bottom: 4px;"/>
            <br/>
        @endif
        <div class="bold big">{{ $settings?->nom ?? "Maf'Food" }}</div>
        @if($settings?->adresse)
            <div class="small">{{ $settings->adresse }}</div>
        @endif
        @if($settings?->telephone)
            <div class="small">Tél : {{ $settings->telephone }}</div>
        @endif
    </div>

    <hr class="double-sep"/>

    {{-- ═══════════════════════════════════════
         INFOS VENTE
    ═══════════════════════════════════════ --}}
    <div>
        <div><span class="bold">Ticket :</span> {{ $vente->reference }}</div>
        <div><span class="bold">Date :</span> {{ $vente->vendu_le->format('d/m/Y H:i') }}</div>
        <div><span class="bold">Caissier :</span> {{ $vente->user->name ?? '—' }}</div>
        @if($vente->client)
            <div><span class="bold">Client :</span> {{ $vente->client->nom }}</div>
        @endif
        <div><span class="bold">Paiement :</span> {{ ucfirst($vente->mode_paiement) }}</div>
    </div>

    <hr class="sep"/>

    {{-- ═══════════════════════════════════════
         LIGNES DE VENTE
    ═══════════════════════════════════════ --}}
    <table>
        <thead>
            <tr class="bold small">
                <td class="col-qty">Qté</td>
                <td class="col-name">Désignation</td>
                <td class="col-price">Montant</td>
            </tr>
        </thead>
        <tbody>
            @foreach($vente->lignes as $ligne)
                <tr>
                    <td class="col-qty">{{ $ligne->quantite }}x</td>
                    <td class="col-name">
                        {{ $ligne->nom_produit }}
                        @if($ligne->remise > 0)
                            <br/><span class="small">  remise -{{ number_format($ligne->remise, 0, ',', ' ') }}</span>
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
        @endif
    </table>

    <hr class="double-sep"/>

    {{-- ═══════════════════════════════════════
         PIED DE TICKET
    ═══════════════════════════════════════ --}}
    <div class="center small">
        <p class="bold">Merci pour votre visite !</p>
        <p>{{ $settings?->nom ?? "Maf'Food" }}</p>
        @if($settings?->facebook || $settings?->instagram)
            <p>
                @if($settings?->facebook) FB: {{ $settings->facebook }} @endif
                @if($settings?->instagram) IG: {{ $settings->instagram }} @endif
            </p>
        @endif
        <br/>
        <p>{{ $vente->reference }} — {{ $vente->vendu_le->format('d/m/Y H:i') }}</p>
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
