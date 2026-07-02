<x-mail::message>
# 🎉 Commande livrée — Merci pour votre confiance !

Bonjour **{{ $commande->nom_client }}**,

Votre commande **{{ $commande->reference }}** vient d'être marquée comme **livrée**. Nous espérons que tout vous a satisfait !

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

Votre avis compte beaucoup pour nous et pour nos futurs clients. Si vous êtes satisfait de vos achats, **laissez-nous un avis** sur chaque Produit — cela ne prend que quelques secondes !

<x-mail::button :url="route('compte.commandes')" color="success">
⭐ Laisser un avis 
</x-mail::button>

En cas de question ou de problème, n'hésitez pas à nous contacter. Nous sommes là pour vous !

Merci encore pour votre confiance et à bientôt,

**Mon Restaurant**
Dakar, Sénégal
</x-mail::message>
