<x-mail::message>
# Merci pour votre commande !

Bonjour **{{ $order->customer_name }}**,

Votre commande a bien été reçue et est en cours de traitement.

**Référence :** {{ $order->reference }}

---

<x-mail::table>
| Produit | Qté | Prix |
|:--------|:---:|-----:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | {{ number_format($item->total, 0, ',', ' ') }} FCFA |
@endforeach
| **Total HT** | | **{{ number_format($order->total, 0, ',', ' ') }} FCFA** |
</x-mail::table>

---

**Mode de paiement :**
{{ match($order->payment_method) {
    'wave'     => 'Wave',
    'om'       => 'Orange Money',
    'mix'      => 'Mix by Yass',
    'djamo'    => 'Djamo',
    'delivery' => 'Paiement à la livraison',
    'card'     => 'Carte bancaire',
    default    => $order->payment_method,
} }}

**Livraison à :** {{ $order->city }}

> 📦 Les frais de livraison vous seront communiqués par notre service client.

Notre équipe vous contactera au **{{ $order->customer_phone }}** pour organiser la livraison.

---

Une question ? Contactez-nous :

@if($settings->whatsapp)
<x-mail::button :url="'https://wa.me/' . preg_replace('/[^0-9]/', '', $settings->whatsapp) . '?text=Bonjour, j\'ai une question concernant ma commande ' . $order->reference" color="success">
💬 WhatsApp
</x-mail::button>
@endif

@if($settings->phone)
📞 **{{ $settings->phone }}**
@endif

Merci de nous faire confiance !

**{{ $settings->name }}**
</x-mail::message>
