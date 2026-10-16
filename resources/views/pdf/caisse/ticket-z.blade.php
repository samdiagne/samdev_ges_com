<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <title>Clôture de Caisse - {{ $session->reference }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:10px; color:#2D2D2D; line-height: 1.4; }
        .container { padding:25px 30px; }
        .header { display:table; width:100%; margin-bottom:20px; padding-bottom:12px; border-bottom:3px solid #F27B20; }
        .header-left { display:table-cell; width:60%; vertical-align:middle; }
        .header-right { display:table-cell; width:40%; vertical-align:middle; text-align:right; }
        .company-name { font-size:16px; font-weight:bold; color:#F27B20; }
        .company-info { font-size:9px; color:#666; line-height:1.6; margin-top:3px; }
        .doc-title { font-size:16px; font-weight:bold; color:#2D2D2D; }
        .doc-sub { font-size:10px; color:#666; margin-top:2px; }

        .meta-box { background:#F8F9FC; border-radius:8px; padding:12px 16px; margin-bottom:18px; border:1px solid #EAEAEA; }
        .meta-row { display:table; width:100%; margin-bottom:4px; }
        .meta-label { display:table-cell; width:35%; font-weight:bold; color:#555; font-size:9px; }
        .meta-val { display:table-cell; width:65%; font-size:9px; color:#222; }

        .section-title { font-size:11px; font-weight:bold; color:#F27B20; text-transform:uppercase; margin-bottom:8px; border-bottom:1px solid #F27B20; padding-bottom:3px; }
        
        table { width:100%; border-collapse:collapse; margin-bottom:15px; }
        thead tr { background:#F27B20; color:white; }
        thead th { padding:6px 8px; text-align:left; font-size:9px; font-weight:bold; text-transform:uppercase; }
        thead th.right { text-align:right; }
        tbody tr { border-bottom:1px solid #f0f0f0; }
        tbody tr:nth-child(even) { background:#FAFAFA; }
        tbody td { padding:6px 8px; font-size:9px; }
        tbody td.right { text-align:right; font-weight:bold; }

        .summary-table { width:100%; border-collapse:collapse; margin-top:10px; margin-bottom:20px; }
        .summary-table td { padding:6px 8px; font-size:10px; border-bottom:1px solid #eee; }
        .summary-table td.strong { font-weight:bold; }
        .summary-table td.right { text-align:right; font-weight:bold; }
        .summary-table tr.total-row { background:#FFF4EC; font-size:11px; }
        .summary-table tr.total-row td { font-weight:bold; color:#F27B20; border-top:2px solid #F27B20; border-bottom:2px solid #F27B20; }
        
        .ecart-box { padding:10px 12px; border-radius:6px; font-weight:bold; font-size:11px; margin-bottom:15px; text-align:center; }
        .ecart-zero { background:#E8F8EE; color:#1B7A3D; border:1px solid #A6E3BC; }
        .ecart-pos { background:#EAF4FE; color:#1359B8; border:1px solid #99C5F8; }
        .ecart-neg { background:#FDEBEB; color:#C52222; border:1px solid #F8A9A9; }

        .signatures { display:table; width:100%; margin-top:35px; }
        .sig-box { display:table-cell; width:50%; text-align:center; vertical-align:top; }
        .sig-line { margin-top:40px; border-top:1px dashed #888; width:70%; margin-left:auto; margin-right:auto; }

        .footer { margin-top:20px; padding-top:8px; border-top:1px solid #e0e0e0; font-size:8px; color:#aaa; text-align:center; }
    </style>
</head>
<body>
<div class="container">

    {{-- En-tête --}}
    <div class="header">
        <div class="header-left">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" style="height:65px; width:auto; max-width:180px; margin-bottom:4px;"/>
            @endif
            <div class="company-name">{{ $parametres?->nom ?? "Maf'food" }}</div>
            <div class="company-info">
                {{ $parametres?->adresse }}<br>
                @if($parametres?->telephone) Tél : {{ $parametres->telephone }} &nbsp;@endif
                @if($parametres?->ninea) NINEA : {{ $parametres->ninea }}@endif
            </div>
        </div>
        <div class="header-right">
            <div class="doc-title">TICKET Z — CLÔTURE DE CAISSE</div>
            <div class="doc-sub">Réf : {{ $session->reference }}</div>
            <div style="font-size:8px; color:#888; margin-top:3px;">
                Émis le {{ now()->format('d/m/Y à H:i') }}
            </div>
        </div>
    </div>

    {{-- Informations de la session --}}
    <div class="meta-box">
        <div class="meta-row">
            <div class="meta-label">Caissier / Utilisateur :</div>
            <div class="meta-val"><strong>{{ $session->user->name }}</strong> ({{ $session->user->email }})</div>
        </div>
        <div class="meta-row">
            <div class="meta-label">Date & Heure d'ouverture :</div>
            <div class="meta-val">{{ $session->ouvert_le->format('d/m/Y à H:i:s') }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-label">Date & Heure de clôture :</div>
            <div class="meta-val">{{ $session->ferme_le ? $session->ferme_le->format('d/m/Y à H:i:s') : 'En cours' }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-label">Durée totale de la session :</div>
            <div class="meta-val"><strong>{{ $session->duree() }}</strong></div>
        </div>
    </div>

    {{-- Bilan des Ventes par Mode de Paiement --}}
    <div class="section-title">1. Répartition des Recettes Encaissées</div>
    <table>
        <thead>
            <tr>
                <th>Mode de Règlement</th>
                <th class="right">Montant Encaissé (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Espèces (Cash)</td>
                <td class="right">{{ number_format($session->total_ventes_cash, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>Wave</td>
                <td class="right">{{ number_format($session->total_ventes_wave, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>Orange Money (OM)</td>
                <td class="right">{{ number_format($session->total_ventes_om, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>Carte Bancaire</td>
                <td class="right">{{ number_format($session->total_ventes_carte, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr style="background:#FFF4EC;">
                <td style="font-weight:bold; color:#F27B20;">TOTAL CHIFFRE D'AFFAIRES ({{ $session->ventes()->count() }} ventes)</td>
                <td class="right" style="color:#F27B20; font-size:11px;">{{ number_format($session->total_ventes, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tbody>
    </table>

    {{-- Dépenses / Sorties de caisse --}}
    @if($session->depenses()->count() > 0)
        <div class="section-title">2. Dépenses & Sorties de Caisse</div>
        <table>
            <thead>
                <tr>
                    <th>Description / Catégorie</th>
                    <th>Mode</th>
                    <th class="right">Montant (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($session->depenses as $depense)
                    <tr>
                        <td>{{ $depense->description }} ({{ $depense->categorie?->nom ?? 'Général' }})</td>
                        <td>{{ strtoupper($depense->mode_paiement) }}</td>
                        <td class="right">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</td>
                    </tr>
                @endforeach
                <tr style="background:#FDEBEB;">
                    <td colspan="2" style="font-weight:bold; color:#C52222;">Total Sorties Espèces</td>
                    <td class="right" style="color:#C52222;">- {{ number_format($session->total_depenses_cash, 0, ',', ' ') }} FCFA</td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- Synthèse Tiroir Caisse & Écart --}}
    <div class="section-title">{{ $session->depenses()->count() > 0 ? '3' : '2' }}. Contrôle des Espèces (Tiroir Physique)</div>
    <table class="summary-table">
        <tr>
            <td>Fond de caisse initial (à l'ouverture)</td>
            <td class="right">{{ number_format($session->fond_caisse_ouverture, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td>+ Encaissements Espèces (Ventes Cash)</td>
            <td class="right">+ {{ number_format($session->total_ventes_cash, 0, ',', ' ') }} FCFA</td>
        </tr>
        @if(($session->total_monnaie_rendue_wave ?? 0) > 0)
            <tr style="background:#F0FDF4;">
                <td style="color:#15803d; font-weight:bold;">+ Monnaie compensée via Wave (gardée en espèces dans le tiroir)</td>
                <td class="right" style="color:#15803d;">+ {{ number_format($session->total_monnaie_rendue_wave, 0, ',', ' ') }} FCFA</td>
            </tr>
        @endif
        <tr>
            <td>- Dépenses décaissées en espèces</td>
            <td class="right">- {{ number_format($session->total_depenses_cash, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="total-row">
            <td>SOLDE THÉORIQUE ATTENDU EN ESPÈCES</td>
            <td class="right">{{ number_format($session->solde_theorique_especes, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td class="strong">Espèces Réelles Comptées par le caissier</td>
            <td class="right" style="font-size:11px;">{{ number_format($session->montant_reel_especes ?? 0, 0, ',', ' ') }} FCFA</td>
        </tr>
    </table>

    {{-- Bloc Écart Espèces --}}
    @php
        $ecart = (float) ($session->ecart ?? 0);
    @endphp
    @if($ecart == 0)
        <div class="ecart-box ecart-zero">
            ✓ ESPÈCES CONFORMES (Écart : 0 FCFA)
        </div>
    @elseif($ecart > 0)
        <div class="ecart-box ecart-pos">
            ▲ EXCÉDENT ESPÈCES : +{{ number_format($ecart, 0, ',', ' ') }} FCFA
        </div>
    @else
        <div class="ecart-box ecart-neg">
            ▼ MANQUANT ESPÈCES (DÉFICIT) : {{ number_format($ecart, 0, ',', ' ') }} FCFA
        </div>
    @endif

    {{-- Contrôle du Compte Wave --}}
    <div class="section-title">{{ $session->depenses()->count() > 0 ? '4' : '3' }}. Contrôle du Compte Wave (Caissier)</div>
    <table class="summary-table">
        <tr>
            <td>Solde Wave initial (à l'ouverture)</td>
            <td class="right">{{ number_format($session->fond_caisse_wave_ouverture ?? 0, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td>+ Encaissements Wave reçus</td>
            <td class="right">+ {{ number_format($session->total_ventes_wave, 0, ',', ' ') }} FCFA</td>
        </tr>
        @if(($session->total_monnaie_rendue_wave ?? 0) > 0)
            <tr style="background:#FEF2F2;">
                <td style="color:#b91c1c; font-weight:bold;">- Monnaies rendues transférées via Wave</td>
                <td class="right" style="color:#b91c1c;">- {{ number_format($session->total_monnaie_rendue_wave, 0, ',', ' ') }} FCFA</td>
            </tr>
        @endif
        <tr class="total-row" style="background:#F0F9FF; border-color:#0284c7;">
            <td style="color:#0369a1;">SOLDE THÉORIQUE ATTENDU SUR WAVE</td>
            <td class="right" style="color:#0369a1;">{{ number_format($session->solde_theorique_wave ?? 0, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td class="strong">Solde Réel Wave Observé par le caissier</td>
            <td class="right" style="font-size:11px;">{{ number_format($session->montant_reel_wave ?? 0, 0, ',', ' ') }} FCFA</td>
        </tr>
    </table>

    {{-- Bloc Écart Wave --}}
    @php
        $ecartWave = (float) ($session->ecart_wave ?? 0);
    @endphp
    @if($ecartWave == 0)
        <div class="ecart-box ecart-zero">
            ✓ SOLDE WAVE CONFORME (Écart : 0 FCFA)
        </div>
    @elseif($ecartWave > 0)
        <div class="ecart-box ecart-pos">
            ▲ EXCÉDENT WAVE : +{{ number_format($ecartWave, 0, ',', ' ') }} FCFA
        </div>
    @else
        <div class="ecart-box ecart-neg">
            ▼ MANQUANT WAVE (DÉFICIT) : {{ number_format($ecartWave, 0, ',', ' ') }} FCFA
        </div>
    @endif

    @if($session->notes_fermeture)
        <div style="background:#FBFBFB; border-left:3px solid #999; padding:8px 12px; margin-bottom:15px; font-size:9px; color:#555;">
            <strong>Observations / Justification :</strong> {{ $session->notes_fermeture }}
        </div>
    @endif

    {{-- Signatures --}}
    <div class="signatures">
        <div class="sig-box">
            <div>Signature du Caissier</div>
            <div class="sig-line"></div>
            <div style="font-size:8px; color:#888; margin-top:4px;">{{ $session->user->name }}</div>
        </div>
        <div class="sig-box">
            <div>Visa Responsable / Gérance</div>
            <div class="sig-line"></div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        {{ $parametres?->nom ?? "Maf'food" }} — Document comptable généré automatiquement — Page 1 / 1
    </div>

</div>
</body>
</html>
