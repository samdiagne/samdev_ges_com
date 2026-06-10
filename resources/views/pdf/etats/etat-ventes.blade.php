<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:10px; color:#2D2D2D; }
        .container { padding:25px 30px; }
        .header { display:table; width:100%; margin-bottom:20px;
                  padding-bottom:12px; border-bottom:3px solid #F27B20; }
        .header-left  { display:table-cell; width:60%; vertical-align:middle; }
        .header-right { display:table-cell; width:40%; vertical-align:middle; text-align:right; }
        .company-name { font-size:16px; font-weight:bold; color:#F27B20; }
        .company-info { font-size:9px; color:#666; line-height:1.6; margin-top:3px; }
        .doc-title  { font-size:18px; font-weight:bold; color:#2D2D2D; }
        .doc-period { font-size:10px; color:#666; margin-top:3px; }
        .stats-grid { display:table; width:100%; margin-bottom:18px; }
        .stat-box   { display:table-cell; background:#F8F9FC; border-radius:6px;
                      padding:10px 12px; text-align:center; }
        .stat-label { font-size:8px; color:#888; text-transform:uppercase; }
        .stat-value { font-size:15px; font-weight:bold; color:#F27B20; margin-top:2px; }
        table { width:100%; border-collapse:collapse; margin-bottom:15px; }
        thead tr { background:#F27B20; color:white; }
        thead th { padding:7px 8px; text-align:left; font-size:9px; font-weight:bold;
                   text-transform:uppercase; }
        thead th.right { text-align:right; }
        tbody tr { border-bottom:1px solid #f0f0f0; }
        tbody tr:nth-child(even) { background:#F8F9FC; }
        tbody td { padding:6px 8px; font-size:9px; }
        tbody td.right { text-align:right; }
        tfoot tr { background:#F27B20; color:white; }
        tfoot td { padding:7px 8px; font-size:9px; font-weight:bold; }
        tfoot td.right { text-align:right; }
        .footer { margin-top:15px; padding-top:8px; border-top:1px solid #e0e0e0;
                  font-size:8px; color:#aaa; text-align:center; }
        .pm-section { display:table; width:100%; margin-bottom:15px; border-collapse:separate; border-spacing:6px; }
        .pm-box { display:table-cell; background:#F8F9FC; border-radius:6px; padding:8px 10px; }
        .pm-label { font-size:8px; color:#888; }
        .pm-value { font-size:13px; font-weight:bold; color:#2D2D2D; margin-top:2px; }
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
            <div class="company-info">
                @if($parametres->ninea) NINEA : {{ $parametres->ninea }} &nbsp;@endif
                @if($parametres->rc) RC : {{ $parametres->rc }}@endif
            </div>
        </div>
        <div class="header-right">
            <div class="doc-title">ÉTAT DES VENTES</div>
            <div class="doc-period">
                Du {{ $from->format('d/m/Y') }} au {{ $to->format('d/m/Y') }}
            </div>
            <div style="font-size:8px; color:#aaa; margin-top:3px;">
                Généré le {{ now()->format('d/m/Y à H:i') }}
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-label">Nb ventes</div>
            <div class="stat-value">{{ $stats['count'] }}</div>
        </div>
        <div class="stat-box" style="margin:0 6px;">
            <div class="stat-label">Total</div>
            <div class="stat-value">{{ number_format($stats['total'], 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="stat-box" style="margin-right:6px;">
            <div class="stat-label">Panier moyen</div>
            <div class="stat-value">{{ number_format($stats['panier_moyen'], 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Nb articles</div>
            <div class="stat-value">
                {{ $ventes->sum(fn($v) => $v->lignes->count()) }}
            </div>
        </div>
    </div>

    {{-- Répartition paiements --}}
    <div style="margin-bottom:15px;">
        <p style="font-size:9px; font-weight:bold; color:#F27B20; margin-bottom:6px;
                  text-transform:uppercase; letter-spacing:0.5px;">
            Répartition par mode de paiement
        </p>
        <div class="pm-section">
            <div class="pm-box">
                <div class="pm-label">Cash</div>
                <div class="pm-value">{{ number_format($stats['cash'], 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="pm-box" style="margin:0 6px;">
                <div class="pm-label">Wave</div>
                <div class="pm-value">{{ number_format($stats['wave'], 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="pm-box">
                <div class="pm-label">Orange Money</div>
                <div class="pm-value">{{ number_format($stats['om'], 0, ',', ' ') }} FCFA</div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                <th style="width:12%">Référence</th>
                <th style="width:18%">Client</th>
                <th style="width:13%">Date</th>
                <th style="width:10%">Paiement</th>
                <th style="width:30%">Articles</th>
                <th class="right" style="width:17%">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventes as $vente)
                <tr>
                    <td><strong>{{ $vente->reference }}</strong></td>
                    <td>{{ $vente->client?->nom ?? 'Anonyme' }}</td>
                    <td>{{ $vente->vendu_le->format('d/m/Y H:i') }}</td>
                    <td style="text-transform:uppercase;">{{ $vente->mode_paiement }}</td>
                    <td style="color:#666;">
                        {{ $vente->lignes->map(fn($l) => $l->nom_plat . ' x' . $l->quantite)->join(', ') }}
                    </td>
                    <td class="right"><strong>{{ number_format($vente->total, 0, ',', ' ') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:15px; color:#aaa;">
                        Aucune vente sur cette période
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($ventes->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="5">TOTAL — {{ $stats['count'] }} vente(s)</td>
                    <td class="right">{{ number_format($stats['total'], 0, ',', ' ') }} FCFA</td>
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