<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #2D2D2D;
            background: #ffffff;
        }

        .container { padding: 30px 35px; }

        /* ── Header ── */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #F27B20;
        }
        .header-left  { display: table-cell; width: 55%; vertical-align: top; }
        .header-right { display: table-cell; width: 45%; vertical-align: top; text-align: right; }

        .logo { height:95px; width:auto; max-width:220px; }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #F27B20;
            margin-bottom: 5px;
        }
        .company-info { color: #555; font-size: 10px; line-height: 1.7; }

        .invoice-title {
            font-size: 30px;
            font-weight: bold;
            color: #F27B20;
            letter-spacing: 3px;
        }
        .invoice-number { font-size: 14px; font-weight: bold; color: #2D2D2D; margin-top: 3px; }
        .invoice-date   { font-size: 10px; color: #666; margin-top: 2px; }

        .badge-unpaid {
            display: inline-block;
            background: #DC2626;
            color: white;
            font-size: 9px;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 6px;
        }

        /* ── Info boxes ── */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 8px 0;
        }
        .info-box {
            display: table-cell;
            width: 33.33%;
            background: #F8F9FC;
            border-left: 3px solid #F27B20;
            border-radius: 6px;
            padding: 10px 12px;
            vertical-align: top;
        }
        .info-box-title {
            font-size: 9px;
            font-weight: bold;
            color: #F27B20;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .info-box p     { font-size: 10px; line-height: 1.6; color: #333; }
        .info-box .bold { font-weight: bold; font-size: 12px; color: #2D2D2D; }

        /* ── Table articles ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        thead tr { background: #F27B20; color: white; }
        thead th {
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        thead th.right { text-align: right; }
        thead th.center { text-align: center; }

        tbody tr { border-bottom: 1px solid #f0f0f0; }
        tbody tr:nth-child(even) { background: #F8F9FC; }
        tbody td { padding: 8px 10px; font-size: 10px; color: #333; }
        tbody td.right  { text-align: right; }
        tbody td.center { text-align: center; }

        /* ── Note paiement (à régler) ── */
        .payment-note-due {
            background: #FDECEC;
            border: 1px solid #F5B8B8;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 20px;
            font-size: 10px;
            color: #7f1d1d;
        }
        .payment-note-due strong { color: #B91C1C; }

        /* ── Footer ── */
        .footer {
            display: table;
            width: 100%;
            border-top: 1px solid #e0e0e0;
            padding-top: 15px;
        }
        .footer-legal {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
            font-size: 9px;
            color: #888;
            line-height: 1.7;
        }
        .footer-legal strong { color: #555; }
    </style>
</head>
<body>
<div class="container">

    {{-- ── Header ── --}}
    <div class="header">
        <div class="header-left">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" class="logo"/>
            @endif
            <div class="company-name">{{ $parametres->nom }}</div>
            <div class="company-info">
                @if($parametres->adresse) Adresse : {{ $parametres->adresse }}<br/>@endif
                @if($parametres->telephone)   Tél : {{ $parametres->telephone }}<br/>@endif
                @if($parametres->email)   Email : {{ $parametres->email }}<br/>@endif
                @if($parametres->ninea)   NINEA : {{ $parametres->ninea }}<br/>@endif
                @if($parametres->rc)      RC : {{ $parametres->rc }}@endif
            </div>
        </div>
        <div class="header-right">
            <div class="invoice-title">FACTURE</div>
            <div class="invoice-number">{{ $facture->numero_facture }}</div>
            <div class="invoice-date">Date : {{ $facture->emise_le->format('d/m/Y') }}</div>
            <div><span class="badge-unpaid">À régler</span></div>
        </div>
    </div>

    {{-- ── Infos Vendeur / Client / Paiement ── --}}
    <div class="info-section">
        <div class="info-box">
            <div class="info-box-title">Vendeur</div>
            <p class="bold">{{ $parametres->nom }}</p>
            @if($parametres->adresse) <p>{{ $parametres->adresse }}</p>@endif
            @if($parametres->telephone)   <p>Tél : {{ $parametres->telephone }}</p>@endif
            @if($parametres->ninea)   <p>NINEA : {{ $parametres->ninea }}</p>@endif
        </div>
        <div class="info-box">
            <div class="info-box-title">Client</div>
            @if($vente->client)
                <p class="bold">{{ $vente->client->nom }}</p>
                @if($vente->client->telephone)   <p>Tél : {{ $vente->client->telephone }}</p>@endif
                @if($vente->client->adresse) <p>{{ $vente->client->adresse }}</p>@endif
                @if($vente->client->ninea)   <p>NINEA : {{ $vente->client->ninea }}</p>@endif
            @else
                <p class="bold">Client anonyme</p>
                <p style="color:#aaa;">Vente comptoir</p>
            @endif
        </div>
        <div class="info-box">
            <div class="info-box-title">Paiement</div>
            <p>Mode : <strong>À définir</strong></p>
            <p>Date : {{ $vente->vendu_le->format('d/m/Y à H:i') }}</p>
            <p>Réf : {{ $vente->reference }}</p>
        </div>
    </div>

    {{-- ── Articles ── --}}
    <table>
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:50%">Désignation</th>
                <th class="center" style="width:10%">Qté</th>
                <th class="right"  style="width:17%">P.U</th>
                <th class="right"  style="width:18%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vente->lignes as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $item->nom_produit }}</strong></td>
                    <td class="center">{{ $item->quantite }}</td>
                    <td class="right">{{ number_format($item->prix_unitaire, 0, ',', ' ') }}</td>
                    <td class="right">
                        <strong>{{ number_format($item->total, 0, ',', ' ') }}</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── Note à régler ── --}}
    <div class="payment-note-due">
        <strong>Facture à régler</strong><br/>
        Montant dû : <strong>{{ number_format($vente->montant_recu, 0, ',', ' ') }} FCFA</strong>
        — À régler.
    </div>

    {{-- ── Footer ── --}}
    <div class="footer">
        <div class="footer-legal">
            <strong>{{ $parametres->nom }}</strong><br/>
            @if($parametres->ninea) NINEA : {{ $parametres->ninea }}<br/>@endif
            @if($parametres->rc)    RC : {{ $parametres->rc }}<br/>@endif
            <span style="color:#bbb; font-size:8px;">
                Document généré le {{ now()->format('d/m/Y à H:i') }} — {{ $parametres->nom }}
            </span>
        </div>
    </div>

</div>
</body>
</html>