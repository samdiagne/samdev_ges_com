<x-mail::message>
# 🛒 Nouvelle commande reçue

**Référence :** {{ $commande->reference }}
**Date :** {{ $commande->created_at->format('d/m/Y à H:i') }}

---

## Client

| | |
|:--|:--|
| **Nom** | {{ $commande->nom_client }} |
| **Téléphone** | {{ $commande->telephone_client }} |
| **Email** | {{ $commande->email_client ?? '—' }} |
| **Ville** | {{ $commande->ville }} |
| **Adresse** | {{ $commande->adresse_livraison }} |
@if($commande->notes)
| **Instructions** | {{ $commande->notes }} |
@endif

---

## Articles commandés

<x-mail::table>
| Produit | Qté | Prix |
|:--------|:---:|-----:|
@foreach($commande->lignes as $item)
| {{ $item->nom_produit }} | {{ $item->quantite }} | {{ number_format($item->total, 0, ',', ' ') }} FCFA |
@endforeach
| **Total HT** | | **{{ number_format($commande->total, 0, ',', ' ') }} FCFA** |
</x-mail::table>

---

**Mode de paiement :**
{{ match($commande->mode_paiement) {
    'cash'     => 'Espèces',
    'wave'     => 'Wave',
    'om'       => 'Orange Money',
    'carte'    => 'Carte bancaire',
    'paydunya' => 'Paiement en ligne (PayDunya)',
    default    => ucfirst($commande->mode_paiement ?? '—'),
} }}

**Statut paiement :** {{ $commande->statut_paiement === 'paye' ? '✅ Payé' : '⏳ En attente' }}

---

Contacter le client **{{ $commande->nom_client }}** :

<x-mail::button :url="'https://wa.me/' . preg_replace('/[^0-9]/', '', $commande->telephone_client) . '?text=Bonjour ' . urlencode($commande->nom_client) . ', nous avons bien reçu votre commande ' . $commande->reference . ' et nous vous contactons pour organiser la livraison.'" color="success">
💬 WhatsApp
</x-mail::button>

<x-mail::button :url="'tel:' . preg_replace('/[^0-9+]/', '', $commande->telephone_client)" color="primary">
📞 Appeler — {{ $commande->telephone_client }}
</x-mail::button>

**{{ $settings->nom }}**
</x-mail::message>
