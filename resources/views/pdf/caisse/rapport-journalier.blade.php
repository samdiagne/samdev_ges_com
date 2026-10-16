<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <title>Rapport Quotidien de Caisse - {{ $date->format('d/m/Y') }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:9.5px; color:#2D2D2D; line-height: 1.4; }
        .container { padding:22px 28px; }
        .header { display:table; width:100%; margin-bottom:18px; padding-bottom:10px; border-bottom:3px solid #F27B20; }
        .header-left { display:table-cell; width:55%; vertical-align:middle; }
        .header-right { display:table-cell; width:45%; vertical-align:middle; text-align:right; }
        .company-name { font-size:15px; font-weight:bold; color:#F27B20; }
        .company-info { font-size:8.5px; color:#666; line-height:1.5; margin-top:3px; }
        .doc-title { font-size:16px; font-weight:bold; color:#2D2D2D; }
        .doc-sub { font-size:10px; color:#F27B20; font-weight:bold; margin-top:2px; }

        .kpi-table { display:table; width:100%; margin-bottom:15px; border-spacing:6px; }
        .kpi-card { display:table-cell; width:25%; background:#F8F9FC; border-radius:6px; padding:10px 12px; text-align:center; border:1px solid #EAEAEA; }
        .kpi-title { font-size:8px; color:#777; text-transform:uppercase; font-weight:bold; }
        .kpi-value { font-size:13px; font-weight:bold; color:#F27B20; margin-top:3px; }

        .section-title { font-size:10.5px; font-weight:bold; color:#F27B20; text-transform:uppercase; margin-top:14px; margin-bottom:6px; border-bottom:1px solid #F27B20; padding-bottom:2px; }

        table { width:100%; border-collapse:collapse; margin-bottom:12px; font-size:8.5px; }
        thead tr { background:#F27B20; color:white; }
        thead th { padding:5px 6px; text-align:left; font-weight:bold; text-transform:uppercase; }
        thead th.right { text-align:right; }
        tbody tr { border-bottom:1px solid #eee; }
        tbody tr:nth-child(even) { background:#FAFAFA; }
        tbody td { padding:5px 6px; }
        tbody td.right { text-align:right; font-weight:bold; }
        tfoot tr { background:#FFF4EC; font-weight:bold; }
        tfoot td { padding:6px 6px; color:#F27B20; font-size:9px; border-top:1px solid #F27B20; }
        tfoot td.right { text-align:right; }

        .pm-grid { display:table; width:100%; margin-bottom:12px; border-spacing:6px; }
        .pm-col { display:table-cell; width:25%; background:#FAFAFA; border:1px solid #E0E0E0; border-radius:6px; padding:8px; text-align:center; }
        .pm-name { font-size:8px; color:#666; text-transform:uppercase; font-weight:bold; }
        .pm-amount { font-size:11px; font-weight:bold; color:#222; margin-top:2px; }

        .signatures { display:table; width:100%; margin-top:25px; }
        .sig-box { display:table-cell; width:50%; text-align:center; vertical-align:top; }
        .sig-line { margin-top:35px; border-top:1px dashed #888; width:65%; margin-left:auto; margin-right:auto; }

        .footer { margin-top:15px; padding-top:6px; border-top:1px solid #e0e0e0; font-size:7.5px; color:#aaa; text-align:center; }
    </style>
</head>
<body>
<div class="container">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" style="height:55px; width:auto; max-width:160px; margin-bottom:3px;"/>
            @endif
            <div class="company-name">{{ $parametres?->nom ?? "Maf'food" }}</div>
            <div class="company-info">
                {{ $parametres?->adresse }}<br>
                @if($parametres?->telephone) Tél : {{ $parametres->telephone }}@endif
            </div>
        </div>
        <div class="header-right">
            <div class="doc-title">RAPPORT QUOTIDIEN DE CAISSE</div>
            <div class="doc-sub">Journée du {{ $date->translatedFormat('l d F Y') }}</div>
            <div style="font-size:8px; color:#666; margin-top:4px;">
                Utilisateur : <strong>{{ $targetUser->name }}</strong> ({{ $targetUser->roles->first()?->name ?? 'caissier' }})
            </div>
            <div style="font-size:7.5px; color:#aaa; margin-top:2px;">
                Édité le {{ now()->format('d/m/Y à H:i') }}
            </div>
        </div>
    </div>

    {{-- Cartes de synthèse --}}
    <div class="kpi-table">
        <div class="kpi-card">
            <div class="kpi-title">Chiffre d'Affaires Total</div>
            <div class="kpi-value">{{ number_format($stats['total_ca'], 0, ',', ' ') }} F</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-title">Fonds de Caisse Déposés</div>
            <div class="kpi-value" style="color:#2B6CB0;">{{ number_format($stats['fond_total'], 0, ',', ' ') }} F</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-title">Sorties / Dépenses</div>
            <div class="kpi-value" style="color:#C53030;">{{ number_format($stats['total_depenses'], 0, ',', ' ') }} F</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-title">Écart Net Journalier</div>
            @php $ecartNet = $stats['ecart_net']; @endphp
            <div class="kpi-value" style="color: {{ $ecartNet < 0 ? '#C53030' : ($ecartNet > 0 ? '#2B6CB0' : '#276749') }};">
                {{ $ecartNet > 0 ? '+' : '' }}{{ number_format($ecartNet, 0, ',', ' ') }} F
            </div>
        </div>
    </div>

    {{-- Ventilation par mode de règlement --}}
    <div class="section-title">1. Répartition du Chiffre d'Affaires par Mode de Règlement</div>
    <div class="pm-grid">
        <div class="pm-col">
            <div class="pm-name">Espèces (Cash)</div>
            <div class="pm-amount">{{ number_format($stats['ca_cash'], 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="pm-col">
            <div class="pm-name">Wave</div>
            <div class="pm-amount">{{ number_format($stats['ca_wave'], 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="pm-col">
            <div class="pm-name">Orange Money</div>
            <div class="pm-amount">{{ number_format($stats['ca_om'], 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="pm-col">
            <div class="pm-name">Carte Bancaire</div>
            <div class="pm-amount">{{ number_format($stats['ca_carte'], 0, ',', ' ') }} FCFA</div>
        </div>
    </div>

    {{-- Détail des sessions de la journée --}}
    <div class="section-title">2. Détail des Sessions de Caisse ({{ $sessions->count() }})</div>
    @if($sessions->isEmpty())
        <div style="padding:15px; text-align:center; color:#888; background:#FAFAFA; border-radius:6px; margin-bottom:15px;">
            Aucune session de caisse enregistrée pour cet utilisateur à cette date.
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Réf. Session</th>
                    <th>Ouverture</th>
                    <th>Fermeture</th>
                    <th>Durée</th>
                    <th class="right">Fond Init.</th>
                    <th class="right">Espèces</th>
                    <th class="right">Wave / OM / CB</th>
                    <th class="right">Total CA</th>
                    <th class="right">Dépenses</th>
                    <th class="right">Écart</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                    @php
                        $totaux = $session->estOuverte() ? $session->totaux_en_direct : [
                            'total_ventes'   => (float) $session->total_ventes,
                            'ventes_cash'    => (float) $session->total_ventes_cash,
                            'ventes_wave'    => (float) $session->total_ventes_wave,
                            'ventes_om'      => (float) $session->total_ventes_om,
                            'ventes_carte'   => (float) $session->total_ventes_carte,
                            'total_depenses' => (float) $session->total_depenses,
                        ];
                        $autresPaiements = $totaux['ventes_wave'] + $totaux['ventes_om'] + $totaux['ventes_carte'];
                        $ecart = (float) ($session->ecart ?? 0);
                    @endphp
                    <tr>
                        <td style="font-weight:bold;">{{ $session->reference }}</td>
                        <td>{{ $session->ouvert_le->format('H:i') }}</td>
                        <td>{{ $session->ferme_le ? $session->ferme_le->format('H:i') : 'Ouverte' }}</td>
                        <td>{{ $session->duree() }}</td>
                        <td class="right">{{ number_format($session->fond_caisse_ouverture, 0, ',', ' ') }}</td>
                        <td class="right">{{ number_format($totaux['ventes_cash'], 0, ',', ' ') }}</td>
                        <td class="right">{{ number_format($autresPaiements, 0, ',', ' ') }}</td>
                        <td class="right" style="color:#F27B20;">{{ number_format($totaux['total_ventes'], 0, ',', ' ') }}</td>
                        <td class="right" style="color:#C53030;">{{ number_format($totaux['total_depenses'], 0, ',', ' ') }}</td>
                        <td class="right" style="color: {{ $ecart < 0 ? '#C53030' : ($ecart > 0 ? '#2B6CB0' : '#276749') }};">
                            {{ $session->ferme_le ? ($ecart > 0 ? '+' : '') . number_format($ecart, 0, ',', ' ') : '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">TOTAUX DE LA JOURNÉE</td>
                    <td class="right">{{ number_format($stats['fond_total'], 0, ',', ' ') }} F</td>
                    <td class="right">{{ number_format($stats['ca_cash'], 0, ',', ' ') }} F</td>
                    <td class="right">{{ number_format($stats['ca_wave'] + $stats['ca_om'] + $stats['ca_carte'], 0, ',', ' ') }} F</td>
                    <td class="right">{{ number_format($stats['total_ca'], 0, ',', ' ') }} F</td>
                    <td class="right" style="color:#C53030;">{{ number_format($stats['total_depenses'], 0, ',', ' ') }} F</td>
                    <td class="right" style="color: {{ $stats['ecart_net'] < 0 ? '#C53030' : ($stats['ecart_net'] > 0 ? '#2B6CB0' : '#276749') }};">
                        {{ $stats['ecart_net'] > 0 ? '+' : '' }}{{ number_format($stats['ecart_net'], 0, ',', ' ') }} F
                    </td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- Signatures --}}
    <div class="signatures">
        <div class="sig-box">
            <div>Signature du Caissier</div>
            <div class="sig-line"></div>
            <div style="font-size:8px; color:#888; margin-top:3px;">{{ $targetUser->name }}</div>
        </div>
        <div class="sig-box">
            <div>Visa Responsable / Direction</div>
            <div class="sig-line"></div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        {{ $parametres?->nom ?? "Maf'food" }} — Rapport de caisse quotidien généré automatiquement — Page 1 / 1
    </div>

</div>
</body>
</html>
