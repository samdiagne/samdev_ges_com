<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:10px; color:#2D2D2D; }
        .container { padding:25px 30px; }
        .header { display:table; width:100%; margin-bottom:20px;
                  padding-bottom:12px; border-bottom:3px solid #F97316; }
        .header-left  { display:table-cell; width:60%; vertical-align:middle; }
        .header-right { display:table-cell; width:40%; vertical-align:middle; text-align:right; }
        .company-name { font-size:16px; font-weight:bold; color:#F27B20; }
        .doc-title  { font-size:18px; font-weight:bold; color:#F97316; }
        .doc-period { font-size:10px; color:#666; margin-top:3px; }
        .summary { display:table; width:100%; margin-bottom:18px; border-collapse:separate; border-spacing:6px; }
        .sum-box { display:table-cell; border-radius:6px; padding:12px 15px; text-align:center; }
        .sum-label { font-size:8px; text-transform:uppercase; letter-spacing:0.5px; }
        .sum-value { font-size:14px; font-weight:bold; margin-top:3px; }
        .sum-sub { font-size:8px; color:#888; margin-top:2px; }
        .four-row { display:table; width:100%; margin-bottom:15px; }
        .four-name  { display:table-cell; width:30%; font-size:9px; padding:3px 0; }
        .four-bar   { display:table-cell; width:50%; vertical-align:middle; }
        .four-bar-bg { background:#f3f4f6; border-radius:4px; height:8px; width:100%; }
        .four-bar-inner { background:#F97316; border-radius:4px; height:8px; }
        .four-value { display:table-cell; width:20%; text-align:right; font-size:9px; font-weight:bold; }
        table { width:100%; border-collapse:collapse; margin-bottom:15px; }
        thead tr { background:#F97316; color:white; }
        thead th { padding:7px 8px; text-align:left; font-size:9px; font-weight:bold;
                   text-transform:uppercase; }
        thead th.right { text-align:right; }
        tbody tr { border-bottom:1px solid #f0f0f0; }
        tbody tr:nth-child(even) { background:#FFF7ED; }
        tbody td { padding:6px 8px; font-size:9px; }
        tbody td.right { text-align:right; }
        tfoot tr { background:#F97316; color:white; }
        tfoot td { padding:7px 8px; font-size:9px; font-weight:bold; }
        tfoot td.right { text-align:right; }
        .badge { display:inline-block; padding:2px 7px; border-radius:8px; font-size:8px; font-weight:bold; }
        .badge-recu     { background:#DCFCE7; color:#166534; }
        .badge-commande { background:#DBEAFE; color:#1E40AF; }
        .badge-partiel  { background:#FFEDD5; color:#C2410C; }
        .badge-annule   { background:#FEE2E2; color:#B91C1C; }
        .footer { margin-top:15px; padding-top:8px; border-top:1px solid #e0e0e0;
                  font-size:8px; color:#aaa; text-align:center; }
    </style>
</head>
<body>
<div class="container">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
           @if($logoBase64)
                <img src="{{ $logoBase64 }}"
                    style="height:95px; width:auto; max-width:220px;"/>
            @endif
            <div class="company-name">{{ $parametres->nom }}</div>
        </div>
        <div class="header-right">
            <div class="doc-title">ÉTAT DES ACHATS</div>
            <div class="doc-period">
                Du {{ $from->format('d/m/Y') }} au {{ $to->format('d/m/Y') }}
            </div>
            <div style="font-size:8px; color:#aaa; margin-top:3px;">
                Généré le {{ now()->format('d/m/Y à H:i') }}
            </div>
        </div>
    </div>

    {{-- Résumé --}}
    <div class="summary">
        <div class="sum-box" style="background:#FFF7ED;">
            <div class="sum-label" style="color:#9A3412;">Nb achats</div>
            <div class="sum-value" style="color:#F97316;">{{ $stats['count'] }}</div>
        </div>
        <div class="sum-box" style="background:#FFEDD5; margin:0 6px;">
            <div class="sum-label" style="color:#9A3412;">Total achats</div>
            <div class="sum-value" style="color:#C2410C;">
                {{ number_format($stats['total'], 0, ',', ' ') }} FCFA
            </div>
        </div>
        <div class="sum-box" style="background:#F0FFF4; margin-right:6px;">
            <div class="sum-label" style="color:#065f46;">Montant payé</div>
            <div class="sum-value" style="color:#10B981;">
                {{ number_format($stats['paye'], 0, ',', ' ') }} FCFA
            </div>
        </div>
        <div class="sum-box"
             style="background:{{ $stats['du'] > 0 ? '#FEE2E2' : '#F0FFF4' }};">
            <div class="sum-label"
                 style="color:{{ $stats['du'] > 0 ? '#991b1b' : '#065f46' }};">
                Restant dû
            </div>
            <div class="sum-value"
                 style="color:{{ $stats['du'] > 0 ? '#EF4444' : '#10B981' }};">
                {{ number_format($stats['du'], 0, ',', ' ') }} FCFA
            </div>
            <div class="sum-sub">{{ $stats['du'] > 0 ? 'à régler' : 'tout est soldé' }}</div>
        </div>
    </div>

    {{-- Répartition par fournisseur --}}
    @if($stats['par_fournisseur']->isNotEmpty())
        <div style="margin-bottom:15px;">
            <p style="font-size:9px; font-weight:bold; color:#F97316; margin-bottom:8px;
                      text-transform:uppercase; letter-spacing:0.5px;">
                Répartition par fournisseur
            </p>
            @php $maxF = $stats['par_fournisseur']->max() ?: 1; @endphp
            @foreach($stats['par_fournisseur'] as $fName => $amount)
                <div class="four-row">
                    <div class="four-name">{{ $fName }}</div>
                    <div class="four-bar">
                        <div class="four-bar-bg">
                            <div class="four-bar-inner"
                                 style="width:{{ round(($amount / $maxF) * 100) }}%"></div>
                        </div>
                    </div>
                    <div class="four-value">{{ number_format($amount, 0, ',', ' ') }} FCFA</div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                <th style="width:11%">Date</th>
                <th style="width:14%">Référence</th>
                <th style="width:23%">Fournisseur</th>
                <th style="width:12%">Statut</th>
                <th class="right" style="width:14%">Total</th>
                <th class="right" style="width:13%">Payé</th>
                <th class="right" style="width:13%">Restant dû</th>
            </tr>
        </thead>
        <tbody>
            @forelse($achats as $achat)
                @php
                    $badges = [
                        'recu'     => ['badge-recu', 'Reçu'],
                        'commande' => ['badge-commande', 'Commandé'],
                        'partiel'  => ['badge-partiel', 'Partiel'],
                        'annule'   => ['badge-annule', 'Annulé'],
                    ];
                    [$badgeClass, $badgeLabel] = $badges[$achat->statut] ?? ['badge-commande', ucfirst($achat->statut)];
                @endphp
                <tr>
                    <td><strong>{{ $achat->date_achat->format('d/m/Y') }}</strong></td>
                    <td>{{ $achat->reference }}</td>
                    <td>{{ $achat->fournisseur?->nom ?? 'Sans fournisseur' }}</td>
                    <td><span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span></td>
                    <td class="right" style="font-weight:bold;">
                        {{ number_format($achat->montant_total, 0, ',', ' ') }}
                    </td>
                    <td class="right" style="color:#10B981;">
                        {{ number_format($achat->montant_paye, 0, ',', ' ') }}
                    </td>
                    <td class="right" style="color:{{ $achat->montant_du > 0 ? '#EF4444' : '#aaa' }};">
                        {{ $achat->montant_du > 0 ? number_format($achat->montant_du, 0, ',', ' ') : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:15px; color:#aaa;">
                        Aucun achat sur cette période
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($achats->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="4">TOTAL</td>
                    <td class="right">{{ number_format($stats['total'], 0, ',', ' ') }} FCFA</td>
                    <td class="right">{{ number_format($stats['paye'], 0, ',', ' ') }} FCFA</td>
                    <td class="right">{{ number_format($stats['du'], 0, ',', ' ') }} FCFA</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        {{ $parametres->nom }}
        @if($parametres->ninea) · NINEA : {{ $parametres->ninea }}@endif
        · Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
</div>
</body>
</html>
