<x-mail::message>
# 🛒 Nouvelle commande reçue

**Référence :** {{ $order->reference }}
**Date :** {{ $order->created_at->format('d/m/Y à H:i') }}

---

## Client

| | |
|:--|:--|
| **Nom** | {{ $order->customer_name }} |
| **Téléphone** | {{ $order->customer_phone }} |
| **Email** | {{ $order->customer_email ?? '—' }} |
| **Ville** | {{ $order->city }} |
| **Adresse** | {{ $order->delivery_address }} |
@if($order->notes)
| **Instructions** | {{ $order->notes }} |
@endif

---

## Articles commandés

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

**Statut paiement :** {{ $order->payment_status === 'paid' ? '✅ Payé' : '⏳ En attente' }}

---

Contacter le client **{{ $order->customer_name }}** :

<x-mail::button :url="'https://wa.me/' . preg_replace('/[^0-9]/', '', $order->customer_phone) . '?text=Bonjour ' . urlencode($order->customer_name) . ', nous avons bien reçu votre commande ' . $order->reference . ' et nous vous contactons pour organiser la livraison.'" color="success">
💬 WhatsApp
</x-mail::button>

<x-mail::button :url="'tel:' . preg_replace('/[^0-9+]/', '', $order->customer_phone)" color="primary">
📞 Appeler — {{ $order->customer_phone }}
</x-mail::button>

**{{ $settings->name }}**
</x-mail::message>
