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
            border-bottom: 3px solid #1E6FBF;
        }
        .header-left  { display: table-cell; width: 55%; vertical-align: top; }
        .header-right { display: table-cell; width: 45%; vertical-align: top; text-align: right; }

        .logo { height:95px; width:auto; max-width:220px; }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #1E6FBF;
            margin-bottom: 5px;
        }
        .company-info { color: #555; font-size: 10px; line-height: 1.7; }

        .invoice-title {
            font-size: 30px;
            font-weight: bold;
            color: #1E6FBF;
            letter-spacing: 3px;
        }
        .invoice-number { font-size: 14px; font-weight: bold; color: #2D2D2D; margin-top: 3px; }
        .invoice-date   { font-size: 10px; color: #666; margin-top: 2px; }

        .badge-paid {
            display: inline-block;
            background: #10B981;
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
            border-left: 3px solid #1E6FBF;
            border-radius: 6px;
            padding: 10px 12px;
            vertical-align: top;
        }
        .info-box-title {
            font-size: 9px;
            font-weight: bold;
            color: #1E6FBF;
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
        thead tr { background: #1E6FBF; color: white; }
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

        /* ── Totaux ── */
        .totals-wrap {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .totals-left  { display: table-cell; width: 55%; vertical-align: top; }
        .totals-right { display: table-cell; width: 45%; vertical-align: top; }

        .totals-box {
            background: #F8F9FC;
            border-radius: 8px;
            padding: 12px 15px;
        }
        .totals-row {
            display: table;
            width: 100%;
            padding: 4px 0;
            border-bottom: 1px solid #e8e8e8;
            font-size: 10px;
            color: #555;
        }
        .totals-row:last-child { border-bottom: none; }
        .totals-row-label  { display: table-cell; }
        .totals-row-value  { display: table-cell; text-align: right; font-weight: bold; }

        .totals-row.total {
            font-size: 13px;
            font-weight: bold;
            color: #2D2D2D;
            border-top: 2px solid #1E6FBF;
            padding-top: 8px;
            margin-top: 4px;
        }
        .totals-row.total .totals-row-value { color: #1E6FBF; font-size: 14px; }
        .totals-row.discount .totals-row-value { color: #F5A800; }

        /* ── Note paiement ── */
        .payment-note {
            background: #F0FFF4;
            border: 1px solid #10B981;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 20px;
            font-size: 10px;
            color: #065f46;
        }
        .payment-note strong { color: #047857; }

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

        .footer-signs {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
            text-align: right;
        }
        .signs-inner {
            display: inline-block;
        }
        .sign-block {
            display: inline-block;
            vertical-align: bottom;
            margin-left: 30px;
            text-align: center;
        }
        .sign-block img {
            display: block;
            margin: 0 auto 5px;
        }
        .sign-line {
            border-top: 1.5px solid #2D2D2D;
            padding-top: 4px;
            font-size: 9px;
            color: #555;
            min-width: 100px;
        }
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
            <div class="company-name">{{ $settings->name }}</div>
            <div class="company-info">
                @if($settings->address) Adresse : {{ $settings->address }}<br/>@endif
                @if($settings->phone)   Tél : {{ $settings->phone }}<br/>@endif
                @if($settings->email)   ✉ {{ $settings->email }}<br/>@endif
                @if($settings->ninea)   NINEA : {{ $settings->ninea }}<br/>@endif
                @if($settings->rc)      RC : {{ $settings->rc }}@endif
            </div>
        </div>
        <div class="header-right">
            <div class="invoice-title">FACTURE</div>
            <div class="invoice-number">{{ $invoice->invoice_number }}</div>
            <div class="invoice-date">Date : {{ $invoice->issued_at->format('d/m/Y') }}</div>
            <div><span class="badge-paid">✓ Payée</span></div>
        </div>
    </div>

    {{-- ── Infos Vendeur / Client / Paiement ── --}}
    <div class="info-section">
        <div class="info-box">
            <div class="info-box-title">Vendeur</div>
            <p class="bold">{{ $settings->name }}</p>
            @if($settings->address) <p>{{ $settings->address }}</p>@endif
            @if($settings->phone)   <p>Tél : {{ $settings->phone }}</p>@endif
            @if($settings->ninea)   <p>NINEA : {{ $settings->ninea }}</p>@endif
        </div>
        <div class="info-box">
            <div class="info-box-title">Client</div>
            @if($sale->customer)
                <p class="bold">{{ $sale->customer->name }}</p>
                @if($sale->customer->phone)   <p>Tél : {{ $sale->customer->phone }}</p>@endif
                @if($sale->customer->address) <p>{{ $sale->customer->address }}</p>@endif
                @if($sale->customer->ninea)   <p>NINEA : {{ $sale->customer->ninea }}</p>@endif
            @else
                <p class="bold">Client anonyme</p>
                <p style="color:#aaa;">Vente comptoir</p>
            @endif
        </div>
        <div class="info-box">
            <div class="info-box-title">Paiement</div>
            <p>Mode :
                <strong>
                    @switch($sale->payment_method)
                        @case('cash')  Espèces @break
                        @case('wave')  Wave @break
                        @case('om')    Orange Money @break
                        @case('mix')   Mixx by Yass @break
                        @case('djamo') Djamo @break
                        @case('card')  Carte bancaire @break
                        @default {{ ucfirst($sale->payment_method) }}
                    @endswitch
                </strong>
            </p>
            <p>Date : {{ $sale->sold_at->format('d/m/Y à H:i') }}</p>
            <p>Réf : {{ $sale->reference }}</p>
            @if($sale->change_given > 0)
                <p>Monnaie : <strong>{{ number_format($sale->change_given, 0, ',', ' ') }} FCFA</strong></p>
            @endif
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
            @foreach($sale->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $item->product_name }}</strong></td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">{{ number_format($item->unit_price_ht, 0, ',', ' ') }}</td>
                    <td class="right">
                        <strong>{{ number_format($item->total_ttc, 0, ',', ' ') }}</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── Totaux ── --}}
    <div class="totals-wrap">
        <div class="totals-left">
            {{-- Note acquittée --}}
            <div class="payment-note">
                <strong>✓ Facture acquittée</strong><br/>
                Montant reçu : <strong>{{ number_format($sale->amount_paid, 0, ',', ' ') }} FCFA</strong>
                — Réglé par
                <strong>
                    @switch($sale->payment_method)
                        @case('cash')  espèces @break
                        @case('wave')  Wave @break
                        @case('om')    Orange Money @break
                        @case('mix')   Mixx by Yass @break
                        @case('djamo') Djamo @break
                        @case('card')  carte bancaire @break
                        @default {{ $sale->payment_method }}
                    @endswitch
                </strong>
                le {{ $sale->sold_at->format('d/m/Y à H:i') }}.
            </div>
        </div>
    </div>

    {{-- ── Footer : masqué temporairement ── --}}
    @if(false)
    <div class="footer">
        <div class="footer-legal">
            <strong>{{ $settings->name }}</strong><br/>
            @if($settings->ninea) NINEA : {{ $settings->ninea }}<br/>@endif
            @if($settings->rc)    RC : {{ $settings->rc }}<br/>@endif
            <span style="color:#bbb; font-size:8px;">
                Document généré le {{ now()->format('d/m/Y à H:i') }} — Al-Adab Tech Store
            </span>
        </div>

        <div class="footer-signs">
            <div class="signs-inner">

                {{-- Signature --}}
                <div class="sign-block">
                    @if($signaturePath && file_exists($signaturePath))
                        <img src="{{ $signaturePath }}"
                             style="height:50px; object-fit:contain; margin-bottom:5px;"/>
                    @else
                        <div style="height:50px;"></div>
                    @endif
                    <div class="sign-line">Signature</div>
                </div>

                {{-- Cachet --}}
                <div class="sign-block">
                    @if($stampPath && file_exists($stampPath))
                        <img src="{{ $stampPath }}"
                             style="height:65px; object-fit:contain; opacity:0.88; margin-bottom:3px;"/>
                    @else
                        <div style="width:65px; height:65px; border:2px dashed #1E6FBF;
                                    border-radius:50%; margin-bottom:3px;"></div>
                    @endif
                    <div class="sign-line">Cachet officiel</div>
                </div>

            </div>
        </div>
    </div>
    @endif

</div>
</body>
</html>