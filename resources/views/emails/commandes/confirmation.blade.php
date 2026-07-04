<x-mail::message>
# Merci pour votre commande !

Bonjour **{{ $commande->nom_client }}**,

Votre commande a bien été reçue et est en cours de traitement.

**Référence :** {{ $commande->reference }}

---

<x-mail::table>
| Produit | Qté | Prix |
|:--------|:---:|-----:|
@foreach($commande->lignes as $item)
| {{ $item->nom_produit }} | {{ $item->quantite }} | {{ number_format($item->total, 0, ',', ' ') }} FCFA |
@endforeach
| **Total** | | **{{ number_format($commande->total, 0, ',', ' ') }} FCFA** |
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

**Livraison à :** {{ $commande->ville }}

> 📦 Les frais de livraison vous seront communiqués par notre service client.

Notre équipe vous contactera au **{{ $commande->telephone_client }}** pour organiser la livraison.

---

Une question ? Contactez-nous :

@if($settings->whatsapp)
<x-mail::button :url="'https://wa.me/' . preg_replace('/[^0-9]/', '', $settings->whatsapp) . '?text=Bonjour, j\'ai une question concernant ma commande ' . $commande->reference" color="success">
💬 WhatsApp
</x-mail::button>
@endif

@if($settings->telephone)
📞 **{{ $settings->telephone }}**
@endif

Merci de nous faire confiance !

**{{ $settings->nom }}**
</x-mail::message>
