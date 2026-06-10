<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:10px; color:#2D2D2D; }
        .container { padding:25px 30px; }
        .header { display:table; width:100%; margin-bottom:20px;
                  padding-bottom:12px; border-bottom:3px solid #10B981; }
        .header-left  { display:table-cell; width:60%; vertical-align:middle; }
        .header-right { display:table-cell; width:40%; vertical-align:middle; text-align:right; }
        .company-name { font-size:16px; font-weight:bold; color:#F27B20; }
        .doc-title  { font-size:18px; font-weight:bold; color:#10B981; }
        .doc-period { font-size:10px; color:#666; margin-top:3px; }
        .summary { display:table; width:100%; margin-bottom:18px; border-collapse:separate; border-spacing:6px; }
        .sum-box { display:table-cell; border-radius:6px; padding:12px 15px; text-align:center; }
        .sum-label { font-size:8px; text-transform:uppercase; letter-spacing:0.5px; }
        .sum-value { font-size:16px; font-weight:bold; margin-top:3px; }
        table { width:100%; border-collapse:collapse; margin-bottom:15px; }
        thead tr { background:#10B981; color:white; }
        thead th { padding:7px 8px; text-align:left; font-size:9px; font-weight:bold;
                   text-transform:uppercase; }
        thead th.right { text-align:right; }
        tbody tr { border-bottom:1px solid #f0f0f0; }
        tbody tr:nth-child(even) { background:#F0FFF4; }
        tbody td { padding:6px 8px; font-size:9px; }
        tbody td.right { text-align:right; }
        tfoot tr { background:#10B981; color:white; }
        tfoot td { padding:7px 8px; font-size:9px; font-weight:bold; }
        tfoot td.right { text-align:right; }
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
            <div class="doc-title">ÉTAT DE TRÉSORERIE</div>
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
        <div class="sum-box" style="background:#F0FFF4;">
            <div class="sum-label" style="color:#065f46;">Recettes</div>
            <div class="sum-value" style="color:#10B981;">
                {{ number_format($stats['recettes'], 0, ',', ' ') }} FCFA
            </div>
        </div>
        <div class="sum-box" style="background:#FEF2F2; margin:0 6px;">
            <div class="sum-label" style="color:#991b1b;">Achats</div>
            <div class="sum-value" style="color:#EF4444;">
                {{ number_format($stats['achats'], 0, ',', ' ') }} FCFA
            </div>
        </div>
        <div class="sum-box" style="background:#FEF2F2; margin-right:6px;">
            <div class="sum-label" style="color:#991b1b;">Dépenses</div>
            <div class="sum-value" style="color:#EF4444;">
                {{ number_format($stats['depenses'], 0, ',', ' ') }} FCFA
            </div>
        </div>
        <div class="sum-box"
             style="background:{{ $stats['solde'] >= 0 ? '#F0FFF4' : '#FEF2F2' }};">
            <div class="sum-label"
                 style="color:{{ $stats['solde'] >= 0 ? '#065f46' : '#991b1b' }};">
                 Solde net
            </div>
            <div class="sum-value"
                 style="color:{{ $stats['solde'] >= 0 ? '#10B981' : '#EF4444' }};">
                {{ number_format($stats['solde'], 0, ',', ' ') }} FCFA
            </div>
        </div>
    </div>

    {{-- Table mouvements --}}
    <table>
        <thead>
            <tr>
                <th style="width:15%">Date</th>
                <th class="right" style="width:25%">Recettes</th>
                <th class="right" style="width:22%">Achats</th>
                <th class="right" style="width:22%">Dépenses</th>
                <th class="right" style="width:16%">Solde jour</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mouvements as $mv)
                <tr>
                    <td><strong>{{ $mv['date'] }}</strong></td>
                    <td class="right" style="color:#10B981;">
                        {{ $mv['recette'] > 0 ? number_format($mv['recette'], 0, ',', ' ') : '—' }}
                    </td>
                    <td class="right" style="color:#EF4444;">
                        {{ $mv['achat'] > 0 ? number_format($mv['achat'], 0, ',', ' ') : '—' }}
                    </td>
                    <td class="right" style="color:#EF4444;">
                        {{ $mv['depense'] > 0 ? number_format($mv['depense'], 0, ',', ' ') : '—' }}
                    </td>
                    <td class="right"
                        style="font-weight:bold; color:{{ $mv['solde'] >= 0 ? '#10B981' : '#EF4444' }}">
                        {{ number_format($mv['solde'], 0, ',', ' ') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:15px; color:#aaa;">
                        Aucun mouvement sur cette période
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($mouvements) > 0)
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td class="right">{{ number_format($stats['recettes'], 0, ',', ' ') }} FCFA</td>
                    <td class="right">{{ number_format($stats['achats'], 0, ',', ' ') }} FCFA</td>
                    <td class="right">{{ number_format($stats['depenses'], 0, ',', ' ') }} FCFA</td>
                    <td class="right">{{ number_format($stats['solde'], 0, ',', ' ') }} FCFA</td>
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