<x-mail::message>
# 🎉 Commande livrée — Merci pour votre confiance !

Bonjour **{{ $order->customer_name }}**,

Votre commande **{{ $order->reference }}** vient d'être marquée comme **livrée**. Nous espérons que tout vous a satisfait !

---

<x-mail::table>
| Produit | Qté | Prix |
|:--------|:---:|-----:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | {{ number_format($item->total, 0, ',', ' ') }} FCFA |
@endforeach
| **Total** | | **{{ number_format($order->total, 0, ',', ' ') }} FCFA** |
</x-mail::table>

---

Votre avis compte beaucoup pour nous et pour nos futurs clients. Si vous êtes satisfait de vos achats, **laissez-nous un avis** sur chaque produit — cela ne prend que quelques secondes !

<x-mail::button :url="route('compte.commandes')" color="success">
⭐ Laisser un avis 
</x-mail::button>

En cas de question ou de problème, n'hésitez pas à nous contacter. Nous sommes là pour vous !

Merci encore pour votre confiance et à bientôt,

**Al-Adab Tech Store**
Dakar, Sénégal
</x-mail::message>
