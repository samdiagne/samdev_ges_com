<?php

namespace App\Livewire\Plats;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Plat;
use App\Models\PlatImage;
use App\Models\Categorie;
use App\Models\MouvementStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PlatForm extends Component
{
    use WithFileUploads;

    public bool   $isEditing  = false;
    public ?int   $platId     = null;

    // Infos générales
    public string $nom                = '';
    public int    $categorie_id       = 0;
    public string $description        = '';
    public string $description_courte = '';
    public array  $ingredients        = [];

    // Prix
    public ?float $prix_vente  = 0;

    // Prix dégressif
    public ?int   $quantite_palier = null;
    public ?float $prix_palier     = null;

    // Promotion
    public bool   $en_promo    = false;
    public ?float $ancien_prix = null;

    // Stock
    public int $stock                = 0;
    public int $seuil_alerte_stock   = 5;

    // Visibilité
    public bool $est_actif          = true;
    public bool $est_mis_en_avant   = false;

    // Images
    public        $imagePrincipale       = null;
    public ?string $imageActuelle        = null;
    public array  $imagesGalerie         = [];

    protected function rules(): array
    {
        $rules = [
            'nom'                  => 'required|string|min:2|max:255',
            'categorie_id'         => 'required|exists:categories,id',
            'description'          => 'nullable|string',
            'description_courte'   => 'nullable|string|max:500',
            'prix_vente'           => 'required|numeric|min:1',
            'stock'                => 'required|integer|min:0',
            'seuil_alerte_stock'   => 'required|integer|min:0',
            'quantite_palier'      => 'nullable|integer|min:2',
            'prix_palier'          => 'nullable|numeric|min:0|required_with:quantite_palier',
            'imagePrincipale'      => 'nullable|image|max:3072',
            'imagesGalerie.*'      => 'nullable|image|max:3072',
        ];

        if ($this->en_promo) {
            $rules['ancien_prix'] = 'required|numeric|gt:prix_vente';
        }

        return $rules;
    }

    protected $messages = [
        'nom.required'           => 'Le nom du plat est obligatoire.',
        'categorie_id.required'  => 'Veuillez sélectionner une catégorie.',
        'categorie_id.exists'    => 'Catégorie invalide.',
        'prix_vente.required'    => 'Le prix de vente est obligatoire.',
        'prix_vente.min'         => 'Le prix de vente doit être supérieur à 0.',
        'ancien_prix.required'   => 'Le prix avant promotion est obligatoire quand la promo est active.',
        'ancien_prix.gt'         => 'Le prix avant promotion doit être supérieur au prix de vente.',
        'imagePrincipale.image'  => 'Le fichier doit être une image.',
        'imagePrincipale.max'    => 'L\'image ne doit pas dépasser 3 Mo.',
    ];

    public function mount(?int $platId = null): void
    {
        if ($platId) {
            $this->isEditing = true;
            $this->platId    = $platId;
            $this->chargerPlat($platId);
        }
    }

    private function chargerPlat(int $id): void
    {
        $plat = Plat::findOrFail($id);

        $this->nom                = $plat->nom;
        $this->categorie_id       = $plat->categorie_id;
        $this->description        = $plat->description ?? '';
        $this->description_courte = $plat->description_courte ?? '';
        $this->ingredients        = $plat->ingredients ?? [];
        $this->prix_vente         = (float) $plat->prix_vente;
        $this->ancien_prix        = $plat->ancien_prix ? (float) $plat->ancien_prix : null;
        $this->quantite_palier    = $plat->quantite_palier;
        $this->prix_palier        = $plat->prix_palier ? (float) $plat->prix_palier : null;
        $this->en_promo           = $plat->ancien_prix !== null && $plat->ancien_prix > $plat->prix_vente;
        $this->stock              = $plat->stock;
        $this->seuil_alerte_stock = $plat->seuil_alerte_stock;
        $this->est_actif          = $plat->est_actif;
        $this->est_mis_en_avant   = $plat->est_mis_en_avant;
        $this->imageActuelle      = $plat->image_principale;
    }

    public function addIngredient(): void
    {
        $this->ingredients[] = '';
    }

    public function removeIngredient(int $index): void
    {
        array_splice($this->ingredients, $index, 1);
        $this->ingredients = array_values($this->ingredients);
    }

    public function removeGalleryImage(int $id): void
    {
        $image = PlatImage::find($id);
        if ($image && $image->plat_id === $this->platId) {
            Storage::disk('public')->delete($image->chemin);
            $image->delete();
        }
    }

    public function save(): void
    {
        $this->validate();

        $cheminImage = $this->imageActuelle;
        if ($this->imagePrincipale) {
            if ($this->imageActuelle) {
                Storage::disk('public')->delete($this->imageActuelle);
            }
            $cheminImage = $this->imagePrincipale->store('plats', 'public');
        }

        $data = [
            'nom'                => $this->nom,
            'categorie_id'       => $this->categorie_id,
            'description'        => $this->description ?: null,
            'description_courte' => $this->description_courte ?: null,
            'ingredients'        => array_values(array_filter($this->ingredients, fn($i) => trim($i) !== '')),
            'prix_vente'         => $this->prix_vente,
            'ancien_prix'        => ($this->en_promo && $this->ancien_prix > 0) ? $this->ancien_prix : null,
            'quantite_palier'    => $this->quantite_palier ?: null,
            'prix_palier'        => ($this->quantite_palier && $this->prix_palier > 0) ? $this->prix_palier : null,
            'stock'              => $this->stock,
            'seuil_alerte_stock' => $this->seuil_alerte_stock,
            'est_actif'          => $this->est_actif,
            'est_mis_en_avant'   => $this->est_mis_en_avant,
            'image_principale'   => $cheminImage,
        ];

        if ($this->isEditing) {
            $plat     = Plat::findOrFail($this->platId);
            $stockAvant = $plat->stock;
            $plat->update($data);

            if ($stockAvant !== $this->stock) {
                MouvementStock::create([
                    'plat_id'            => $plat->id,
                    'user_id'     => Auth::id(),
                    'type'               => $this->stock > $stockAvant ? 'entree' : 'sortie',
                    'source'             => 'ajustement',
                    'stock_avant'        => $stockAvant,
                    'quantite_changement'=> abs($this->stock - $stockAvant),
                    'stock_apres'        => $this->stock,
                    'motif'              => 'Ajustement manuel',
                    'effectue_le'        => now(),
                ]);
            }

            session()->flash('success', 'Plat modifié avec succès !');
        } else {
            $plat = Plat::create($data);

            if ($this->stock > 0) {
                MouvementStock::create([
                    'plat_id'            => $plat->id,
                    'user_id'     => Auth::id(),
                    'type'               => 'entree',
                    'source'             => 'ajustement',
                    'stock_avant'        => 0,
                    'quantite_changement'=> $this->stock,
                    'stock_apres'        => $this->stock,
                    'motif'              => 'Stock initial',
                    'effectue_le'        => now(),
                ]);
            }

            session()->flash('success', 'Plat créé avec succès !');
        }

        foreach ($this->imagesGalerie as $img) {
            $chemin = $img->store('plats/galerie', 'public');
            PlatImage::create([
                'plat_id' => $plat->id,
                'chemin'  => $chemin,
                'ordre'   => PlatImage::where('plat_id', $plat->id)->count(),
            ]);
        }

        $this->redirect(route('plats.index'));
    }

    public function render()
    {
        return view('livewire.plats.plat-form', [
            'categories'      => Categorie::where('est_active', true)->orderBy('nom')->get(),
            'galerieExistante' => $this->platId
                ? PlatImage::where('plat_id', $this->platId)->orderBy('ordre')->get()
                : collect(),
        ]);
    }
}
