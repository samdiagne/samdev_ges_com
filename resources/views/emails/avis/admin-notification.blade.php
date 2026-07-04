<x-mail::message>
# ⭐ Nouvel avis client à modérer

Un client vient de laisser un avis sur **{{ $avis->produit->nom }}**.

---

| | |
|:--|:--|
| **Produit** | {{ $avis->produit->nom }} |
| **Auteur** | {{ $avis->nom_auteur }} |
| **Note** | {{ str_repeat('⭐', $avis->note) }} ({{ $avis->note }}/5) |
| **Date** | {{ $avis->created_at->format('d/m/Y à H:i') }} |

**Commentaire :**

> {{ $avis->commentaire }}

---

Cet avis n'est **pas visible publiquement** tant que vous ne l'avez pas approuvé.

<x-mail::button :url="route('avis.index')" color="primary">
Modérer les avis
</x-mail::button>

**{{ $settings->nom }}**
</x-mail::message>
