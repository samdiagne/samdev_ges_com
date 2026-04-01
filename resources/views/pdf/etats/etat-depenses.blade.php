<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:10px; color:#2D2D2D; }
        .container { padding:25px 30px; }
        .header { display:table; width:100%; margin-bottom:20px;
                  padding-bottom:12px; border-bottom:3px solid #EF4444; }
        .header-left  { display:table-cell; width:60%; vertical-align:middle; }
        .header-right { display:table-cell; width:40%; vertical-align:middle; text-align:right; }
        .company-name { font-size:16px; font-weight:bold; color:#1E6FBF; }
        .company-info { font-size:9px; color:#666; line-height:1.6; margin-top:3px; }
        .doc-title  { font-size:18px; font-weight:bold; color:#EF4444; }
        .doc-period { font-size:10px; color:#666; margin-top:3px; }
        .stats-grid { display:table; width:100%; margin-bottom:18px; }
        .stat-box   { display:table-cell; background:#FEF2F2; border-radius:6px;
                      padding:10px 12px; text-align:center; }
        .stat-label { font-size:8px; color:#888; text-transform:uppercase; }
        .stat-value { font-size:15px; font-weight:bold; color:#EF4444; margin-top:2px; }
        table { width:100%; border-collapse:collapse; margin-bottom:15px; }
        thead tr { background:#EF4444; color:white; }
        thead th { padding:7px 8px; text-align:left; font-size:9px; font-weight:bold;
                   text-transform:uppercase; }
        thead th.right { text-align:right; }
        tbody tr { border-bottom:1px solid #f0f0f0; }
        tbody tr:nth-child(even) { background:#FEF2F2; }
        tbody td { padding:6px 8px; font-size:9px; }
        tbody td.right { text-align:right; }
        tfoot tr { background:#EF4444; color:white; }
        tfoot td { padding:7px 8px; font-size:9px; font-weight:bold; }
        tfoot td.right { text-align:right; }
        .footer { margin-top:15px; padding-top:8px; border-top:1px solid #e0e0e0;
                  font-size:8px; color:#aaa; text-align:center; }
        .cat-section { margin-bottom:15px; }
        .cat-row { display:table; width:100%; margin-bottom:4px; }
        .cat-name  { display:table-cell; font-size:9px; }
        .cat-bar   { display:table-cell; width:50%; padding:0 8px; vertical-align:middle; }
        .cat-bar-inner { height:8px; background:#EF4444; border-radius:4px; opacity:0.7; }
        .cat-value { display:table-cell; font-size:9px; font-weight:bold;
                     text-align:right; white-space:nowrap; }
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
            <div class="company-name">{{ $settings->name }}</div>
            <div class="company-info">
                @if($settings->ninea) NINEA : {{ $settings->ninea }}@endif
            </div>
        </div>
        <div class="header-right">
            <div class="doc-title">ÉTAT DES DÉPENSES</div>
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
        <div class="stat-box" style="margin-right:6px;">
            <div class="stat-label">Nb dépenses</div>
            <div class="stat-value">{{ $stats['count'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Total dépensé</div>
            <div class="stat-value">{{ number_format($stats['total'], 0, ',', ' ') }} FCFA</div>
        </div>
    </div>

    {{-- Répartition par catégorie --}}
    @if($stats['by_category']->isNotEmpty())
        <div style="margin-bottom:15px;">
            <p style="font-size:9px; font-weight:bold; color:#EF4444; margin-bottom:8px;
                      text-transform:uppercase; letter-spacing:0.5px;">
                Répartition par catégorie
            </p>
            @php $maxCat = $stats['by_category']->max() ?: 1; @endphp
            @foreach($stats['by_category'] as $catName => $amount)
                <div class="cat-row">
                    <div class="cat-name">{{ $catName ?? 'Autre' }}</div>
                    <div class="cat-bar">
                        <div class="cat-bar-inner"
                             style="width:{{ round(($amount / $maxCat) * 100) }}%"></div>
                    </div>
                    <div class="cat-value">{{ number_format($amount, 0, ',', ' ') }} FCFA</div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                <th style="width:13%">Date</th>
                <th style="width:20%">Catégorie</th>
                <th style="width:35%">Description</th>
                <th style="width:12%">Paiement</th>
                <th class="right" style="width:20%">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
                <tr>
                    <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td>{{ $expense->category?->name ?? '—' }}</td>
                    <td>{{ $expense->description }}</td>
                    <td style="text-transform:uppercase;">{{ $expense->payment_method }}</td>
                    <td class="right">
                        <strong>{{ number_format($expense->amount, 0, ',', ' ') }}</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:15px; color:#aaa;">
                        Aucune dépense sur cette période
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($expenses->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="4">TOTAL — {{ $stats['count'] }} dépense(s)</td>
                    <td class="right">{{ number_format($stats['total'], 0, ',', ' ') }} FCFA</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        {{ $settings->name }}
        @if($settings->ninea) · NINEA : {{ $settings->ninea }}@endif
        · Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
</div>
</body>
</html>