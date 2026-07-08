<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:10px; color:#2D2D2D; }
        .container { padding:25px 30px; }
        .header { display:table; width:100%; margin-bottom:20px;
                  padding-bottom:12px; border-bottom:3px solid #F59E0B; }
        .header-left  { display:table-cell; width:60%; vertical-align:middle; }
        .header-right { display:table-cell; width:40%; vertical-align:middle; text-align:right; }
        .company-name { font-size:16px; font-weight:bold; color:#F27B20; }
        .doc-title  { font-size:18px; font-weight:bold; color:#F59E0B; }
        .doc-period { font-size:10px; color:#666; margin-top:3px; }
        .summary { display:table; width:100%; margin-bottom:18px; border-collapse:separate; border-spacing:6px; }
        .sum-box { display:table-cell; border-radius:6px; padding:12px 15px; text-align:center; }
        .sum-label { font-size:8px; text-transform:uppercase; letter-spacing:0.5px; }
        .sum-value { font-size:14px; font-weight:bold; margin-top:3px; }
        .sum-sub { font-size:8px; color:#888; margin-top:2px; }
        table { width:100%; border-collapse:collapse; margin-bottom:15px; }
        thead tr { background:#F59E0B; color:white; }
        thead th { padding:7px 8px; text-align:left; font-size:9px; font-weight:bold;
                   text-transform:uppercase; }
        thead th.right { text-align:right; }
        tbody tr { border-bottom:1px solid #f0f0f0; }
        tbody tr:nth-child(even) { background:#FFFBEB; }
        tbody td { padding:6px 8px; font-size:9px; }
        tbody td.right { text-align:right; }
        tfoot tr { background:#F59E0B; color:white; }
        tfoot td { padding:7px 8px; font-size:9px; font-weight:bold; }
        tfoot td.right { text-align:right; }
        .badge { display:inline-block; padding:2px 7px; border-radius:8px; font-size:8px; font-weight:bold; }
        .badge-achat   { background:#FFEDD5; color:#C2410C; }
        .badge-depense { background:#FEE2E2; color:#B91C1C; }
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
            <div class="doc-title">ACHATS &amp; DÉPENSES</div>
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
        <div class="sum-box" style="background:#FFEDD5;">
            <div class="sum-label" style="color:#9A3412;">Achats fournisseurs</div>
            <div class="sum-value" style="color:#C2410C;">
                {{ number_format($stats['achats_total'], 0, ',', ' ') }} FCFA
            </div>
            <div class="sum-sub">{{ $stats['achats_count'] }} achat(s)</div>
        </div>
        <div class="sum-box" style="background:#FEE2E2; margin:0 6px;">
            <div class="sum-label" style="color:#991b1b;">Dépenses</div>
            <div class="sum-value" style="color:#B91C1C;">
                {{ number_format($stats['depenses_total'], 0, ',', ' ') }} FCFA
            </div>
            <div class="sum-sub">{{ $stats['depenses_count'] }} dépense(s)</div>
        </div>
        <div class="sum-box" style="background:#FFFBEB; margin-right:6px;">
            <div class="sum-label" style="color:#9A3412;">Total sorties</div>
            <div class="sum-value" style="color:#F59E0B;">
                {{ number_format($stats['total_sorties'], 0, ',', ' ') }} FCFA
            </div>
            <div class="sum-sub">achats + dépenses</div>
        </div>
        <div class="sum-box"
             style="background:{{ $stats['achats_du'] > 0 ? '#FEE2E2' : '#F0FFF4' }};">
            <div class="sum-label"
                 style="color:{{ $stats['achats_du'] > 0 ? '#991b1b' : '#065f46' }};">
                Restant dû fournisseurs
            </div>
            <div class="sum-value"
                 style="color:{{ $stats['achats_du'] > 0 ? '#EF4444' : '#10B981' }};">
                {{ number_format($stats['achats_du'], 0, ',', ' ') }} FCFA
            </div>
            <div class="sum-sub">{{ $stats['achats_du'] > 0 ? 'à régler' : 'tout est soldé' }}</div>
        </div>
    </div>

    {{-- Table croisée --}}
    <table>
        <thead>
            <tr>
                <th style="width:12%">Date</th>
                <th style="width:11%">Type</th>
                <th style="width:39%">Libellé</th>
                <th style="width:12%">Mode</th>
                <th class="right" style="width:14%">Montant</th>
                <th class="right" style="width:12%">Restant dû</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mouvements as $mv)
                <tr>
                    <td><strong>{{ $mv['date']->format('d/m/Y') }}</strong></td>
                    <td>
                        <span class="badge {{ $mv['type'] === 'achat' ? 'badge-achat' : 'badge-depense' }}">
                            {{ $mv['type'] === 'achat' ? 'ACHAT' : 'DÉPENSE' }}
                        </span>
                    </td>
                    <td>{{ $mv['libelle'] }}</td>
                    <td>{{ strtoupper($mv['mode'] ?? '—') }}</td>
                    <td class="right" style="font-weight:bold;">
                        {{ number_format($mv['montant'], 0, ',', ' ') }}
                    </td>
                    <td class="right" style="color:{{ $mv['du'] > 0 ? '#EF4444' : '#aaa' }};">
                        {{ $mv['du'] > 0 ? number_format($mv['du'], 0, ',', ' ') : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:15px; color:#aaa;">
                        Aucun achat ni dépense sur cette période
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($mouvements) > 0)
            <tfoot>
                <tr>
                    <td colspan="4">TOTAL SORTIES</td>
                    <td class="right">{{ number_format($stats['total_sorties'], 0, ',', ' ') }} FCFA</td>
                    <td class="right">{{ number_format($stats['achats_du'], 0, ',', ' ') }} FCFA</td>
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
