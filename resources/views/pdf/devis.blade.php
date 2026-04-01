<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:11px; color:#2D2D2D; }
        .container { padding:30px 35px; }

        .header { display:table; width:100%; margin-bottom:25px;
                  padding-bottom:15px; border-bottom:3px solid #F5A800; }
        .header-left  { display:table-cell; width:55%; vertical-align:top; }
        .header-right { display:table-cell; width:45%; vertical-align:top; text-align:right; }

        .company-name { font-size:20px; font-weight:bold; color:#1E6FBF; margin-bottom:5px; }
        .company-info { color:#555; font-size:10px; line-height:1.7; }

        .quote-title  { font-size:30px; font-weight:bold; color:#F5A800; letter-spacing:3px; }
        .quote-number { font-size:14px; font-weight:bold; color:#2D2D2D; margin-top:3px; }
        .quote-date   { font-size:10px; color:#666; margin-top:2px; }

        .badge-status {
            display:inline-block; font-size:9px; font-weight:bold;
            padding:3px 10px; border-radius:20px; text-transform:uppercase;
            letter-spacing:1px; margin-top:6px;
        }
        .badge-draft    { background:#e5e7eb; color:#6b7280; }
        .badge-sent     { background:#dbeafe; color:#1d4ed8; }
        .badge-accepted { background:#d1fae5; color:#065f46; }
        .badge-refused  { background:#fee2e2; color:#991b1b; }

        .validity {
            background:#FFF8E7; border:1px solid #F5A800;
            border-radius:6px; padding:8px 12px; margin-bottom:15px;
            font-size:10px; color:#92400e;
        }

        .info-section { display:table; width:100%; margin-bottom:20px; }
        .info-box {
            display:table-cell; width:50%;
            background:#F8F9FC; border-left:3px solid #F5A800;
            border-radius:6px; padding:10px 12px; vertical-align:top;
        }
        .info-box:first-child { margin-right:8px; }
        .info-box-title {
            font-size:9px; font-weight:bold; color:#F5A800;
            text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;
        }
        .info-box p    { font-size:10px; line-height:1.6; color:#333; }
        .info-box .bold { font-weight:bold; font-size:12px; color:#2D2D2D; }

        table { width:100%; border-collapse:collapse; margin-bottom:15px; }
        thead tr { background:#F5A800; color:white; }
        thead th {
            padding:8px 10px; text-align:left; font-size:10px;
            font-weight:bold; text-transform:uppercase;
        }
        thead th.right { text-align:right; }
        thead th.center { text-align:center; }
        tbody tr { border-bottom:1px solid #f0f0f0; }
        tbody tr:nth-child(even) { background:#F8F9FC; }
        tbody td { padding:8px 10px; font-size:10px; color:#333; }
        tbody td.right { text-align:right; }
        tbody td.center { text-align:center; }
        .free-line { color:#666; font-style:italic; }

        .totals-wrap  { display:table; width:100%; margin-bottom:15px; }
        .totals-left  { display:table-cell; width:55%; vertical-align:top; }
        .totals-right { display:table-cell; width:45%; vertical-align:top; }
        .totals-box   { background:#F8F9FC; border-radius:8px; padding:12px 15px; }
        .totals-row   {
            display:table; width:100%; padding:4px 0;
            border-bottom:1px solid #e8e8e8; font-size:10px; color:#555;
        }
        .totals-row:last-child { border-bottom:none; }
        .totals-row-label { display:table-cell; }
        .totals-row-value { display:table-cell; text-align:right; font-weight:bold; }
        .totals-row.total {
            font-size:13px; font-weight:bold; color:#2D2D2D;
            border-top:2px solid #F5A800; padding-top:8px; margin-top:4px;
        }
        .totals-row.total .totals-row-value { color:#F5A800; font-size:14px; }

        .notes-box {
            background:#f9fafb; border-left:3px solid #e5e7eb;
            border-radius:6px; padding:10px 12px; margin-bottom:15px;
            font-size:10px; color:#555;
        }

        .footer { display:table; width:100%; border-top:1px solid #e0e0e0; padding-top:15px; }
        .footer-legal {
            display:table-cell; width:50%; vertical-align:bottom;
            font-size:9px; color:#888; line-height:1.7;
        }
        .footer-signs { display:table-cell; width:50%; vertical-align:bottom; text-align:right; }
        .sign-block { display:inline-block; vertical-align:bottom; margin-left:30px; text-align:center; }
        .sign-block img { display:block; margin:0 auto 5px; }
        .sign-line {
            border-top:1.5px solid #2D2D2D; padding-top:4px;
            font-size:9px; color:#555; min-width:100px;
        }
    </style>
</head>
<body>
<div class="container">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" style="height:95px; width:auto; max-width:220px;"/>
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
            <div class="quote-title">DEVIS</div>
            <div class="quote-number">{{ $quote->reference }}</div>
            <div class="quote-date">Date : {{ $quote->quote_date->format('d/m/Y') }}</div>
            <div>
                <span class="badge-status badge-{{ $quote->status }}">
                    @switch($quote->status)
                        @case('draft')    Brouillon @break
                        @case('sent')     Envoyé @break
                        @case('accepted') Accepté @break
                        @case('refused')  Refusé @break
                        @default {{ $quote->status }}
                    @endswitch
                </span>
            </div>
        </div>
    </div>

    {{-- Validité --}}
    @if($quote->valid_until)
        <div class="validity">
            ⏳ Ce devis est valable jusqu'au
            <strong>{{ $quote->valid_until->format('d/m/Y') }}</strong>.
        </div>
    @endif

    {{-- Infos --}}
    <div class="info-section">
        <div class="info-box" style="margin-right:8px;">
            <div class="info-box-title">Émis par</div>
            <p class="bold">{{ $settings->name }}</p>
            @if($settings->address) <p>{{ $settings->address }}</p>@endif
            @if($settings->phone)   <p>Tél : {{ $settings->phone }}</p>@endif
            @if($settings->ninea)   <p>NINEA : {{ $settings->ninea }}</p>@endif
        </div>
        <div class="info-box">
            <div class="info-box-title">Destinataire</div>
            <p class="bold">{{ $quote->client_name }}</p>
            @if($quote->customer?->phone ?? $quote->customer_phone)
                <p>Tél : {{ $quote->customer?->phone ?? $quote->customer_phone }}</p>
            @endif
            @if($quote->customer?->address)
                <p>{{ $quote->customer->address }}</p>
            @endif
            @if($quote->customer?->ninea)
                <p>NINEA : {{ $quote->customer->ninea }}</p>
            @endif
        </div>
    </div>

    {{-- Articles --}}
    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:42%">Désignation</th>
                <th class="center" style="width:8%">Qté</th>
                <th class="right"  style="width:15%">P.U</th>
                <th class="right"  style="width:10%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="{{ $item->is_free_line ? 'free-line' : '' }}">
                        <strong>{{ $item->description }}</strong>
                    </td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">{{ number_format($item->unit_price_ht, 0, ',', ' ') }}</td>
                    <td class="right">
                        <strong>{{ number_format($item->total_ttc, 0, ',', ' ') }}</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totaux --}}
    <div class="totals-wrap">
        <div class="totals-left">
            @if($quote->notes)
                <div class="notes-box">
                    <strong>Notes :</strong><br/>{{ $quote->notes }}
                </div>
            @endif
            <div style="font-size:9px; color:#888; margin-top:8px;">
                Pour accepter ce devis, veuillez nous contacter :<br/>
                @if($settings->phone)   Tél : {{ $settings->phone }}<br/>@endif
                @if($settings->whatsapp)WhatshApp : {{ $settings->whatsapp }}<br/>@endif
                @if($settings->email)   ✉ {{ $settings->email }}@endif
            </div>
        </div>
    </div>

    {{-- Footer --}}
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
            <div class="sign-block">
                @if($signaturePath && file_exists($signaturePath))
                    <img src="{{ $signaturePath }}"
                         style="height:50px; object-fit:contain; margin-bottom:5px;"/>
                @else
                    <div style="height:50px;"></div>
                @endif
                <div class="sign-line">Signature</div>
            </div>
            <div class="sign-block">
                @if($stampPath && file_exists($stampPath))
                    <img src="{{ $stampPath }}"
                         style="height:65px; object-fit:contain; opacity:0.88; margin-bottom:3px;"/>
                @else
                    <div style="width:65px; height:65px; border:2px dashed #F5A800;
                                border-radius:50%; margin-bottom:3px;"></div>
                @endif
                <div class="sign-line">Cachet officiel</div>
            </div>
        </div>
    </div>
    @endif

</div>
</body>
</html>