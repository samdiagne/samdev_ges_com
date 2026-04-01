<x-mail::message>
# ⭐ Nouvel avis client en attente de modération

**Produit :** {{ $review->product->name }}
**Date :** {{ $review->created_at->format('d/m/Y à H:i') }}

---

## Auteur

| | |
|:--|:--|
| **Nom** | {{ $review->author_name }} |
| **Email** | {{ $review->author_email ?? '—' }} |
| **Note** | {{ str_repeat('⭐', $review->rating) }} ({{ $review->rating }}/5) |

---

## Commentaire

{{ $review->comment }}

---

<x-mail::button :url="route('avis.index')">
Modérer l'avis
</x-mail::button>

**Al-Adab Tech**
</x-mail::message>
