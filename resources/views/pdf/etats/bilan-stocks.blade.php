<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:9px; color:#2D2D2D; }
        .container { padding:20px 25px; }
        .header { display:table; width:100%; margin-bottom:15px;
                  padding-bottom:10px; border-bottom:3px solid #8B5CF6; }
        .header-left  { display:table-cell; width:60%; vertical-align:middle; }
        .header-right { display:table-cell; width:40%; vertical-align:middle; text-align:right; }
        .company-name { font-size:14px; font-weight:bold; color:#F27B20; }
        .doc-title  { font-size:16px; font-weight:bold; color:#8B5CF6; }
        .doc-period { font-size:9px; color:#666; margin-top:2px; }
        .stats-grid { display:table; width:100%; margin-bottom:15px; border-collapse:separate; border-spacing:5px; }
        .stat-box   { display:table-cell; background:#F5F3FF; border-radius:5px;
                      padding:8px 10px; text-align:center; }
        .stat-label { font-size:7px; color:#888; text-transform:uppercase; }
        .stat-value { font-size:13px; font-weight:bold; color:#8B5CF6; margin-top:2px; }
        .stat-value.green { color:#10B981; }
        .stat-value.red   { color:#EF4444; }
        table { width:100%; border-collapse:collapse; }
        thead tr { background:#8B5CF6; color:white; }
        thead th { padding:6px 7px; text-align:left; font-size:8px; font-weight:bold;
                   text-transform:uppercase; }
        thead th.right { text-align:right; }
        thead th.center { text-align:center; }
        tbody tr { border-bottom:1px solid #f0f0f0; }
        tbody tr:nth-child(even) { background:#F5F3FF; }
        tbody td { padding:5px 7px; font-size:8px; }
        tbody td.right { text-align:right; }
        tbody td.center { text-align:center; }
        tfoot tr { background:#8B5CF6; color:white; }
        tfoot td { padding:6px 7px; font-size:8px; font-weight:bold; }
        tfoot td.right { text-align:right; }
        .footer { margin-top:12px; padding-top:8px; border-top:1px solid #e0e0e0;
                  font-size:7px; color:#aaa; text-align:center; }
        .badge-ok  { color:#10B981; font-weight:bold; }
        .badge-low { color:#F97316; font-weight:bold; }
        .badge-out { color:#EF4444; font-weight:bold; }
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
            <div class="doc-title">BILAN DES STOCKS</div>
            <div class="doc-period">Au {{ now()->format('d/m/Y à H:i') }}</div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-label">Produits</div>
            <div class="stat-value">{{ $stats['total_produits'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Valeur du stock</div>
            <div class="stat-value green">{{ number_format($stats['valeur_vente'], 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Ruptures</div>
            <div class="stat-value red">{{ $stats['ruptures'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Stock bas</div>
            <div class="stat-value" style="color:#F97316;">{{ $stats['stock_bas'] }}</div>
        </div>
    </div>

    {{-- Table Produits --}}
    <table>
        <thead>
            <tr>
                <th style="width:30%">Produit</th>
                <th style="width:18%">Catégorie</th>
                <th class="right" style="width:18%">Prix</th>
                <th class="center" style="width:10%">Stock</th>
                <th class="right" style="width:14%">Val. Stock</th>
                <th class="center" style="width:10%">Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produits as $produit)
                <tr>
                    <td><strong>{{ $produit->nom }}</strong></td>
                    <td>{{ $produit->categorie?->nom ?? '—' }}</td>
                    <td class="right">{{ number_format($produit->prix_vente, 0, ',', ' ') }}</td>
                    <td class="center">
                        @if($produit->sur_commande)
                            <span class="badge-ok">—</span>
                        @elseif($produit->stock <= 0)
                            <span class="badge-out">{{ $produit->stock }}</span>
                        @elseif($produit->estStockFaible())
                            <span class="badge-low">{{ $produit->stock }}</span>
                        @else
                            <span class="badge-ok">{{ $produit->stock }}</span>
                        @endif
                    </td>
                    <td class="right">
                        {{ $produit->sur_commande ? '—' : number_format($produit->stock * $produit->prix_vente, 0, ',', ' ') }}
                    </td>
                    <td class="center">
                        @if($produit->sur_commande)
                            <span style="color:#3B82F6;font-weight:bold;">Sur cde</span>
                        @elseif($produit->stock <= 0)
                            <span class="badge-out">Rupture</span>
                        @elseif($produit->estStockFaible())
                            <span class="badge-low">Bas</span>
                        @else
                            <span class="badge-ok">OK</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">VALEUR TOTALE DU STOCK</td>
                <td class="right">
                    {{ number_format($stats['valeur_vente'], 0, ',', ' ') }} FCFA
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        {{ $parametres->nom }}
        @if($parametres->ninea) · NINEA : {{ $parametres->ninea }}@endif
        · Bilan généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
</div>
</body>
</html>