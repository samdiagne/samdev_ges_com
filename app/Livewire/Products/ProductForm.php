<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\CompanySetting;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductForm extends Component
{
    use WithFileUploads;

    public bool  $isEditing  = false;
    public ?int  $productId  = null;

    // Infos générales
    public string  $name              = '';
    public string  $sku               = '';
    public string  $brand             = '';
    public string  $model             = '';
    public int     $category_id       = 0;
    public ?int    $supplier_id       = null;
    public string  $description       = '';
    public string  $short_description = '';
    public string  $unit              = 'unité';

    // Prix
    public ?float $purchase_price    = 0;
    public ?float $selling_price     = 0;
    public ?float $selling_price_ttc = 0;
    public ?float $old_price         = null;
    public bool  $has_tva           = true;

    // Stock
    public int $stock_quantity        = 0;
    public int $stock_alert_threshold = 5;

    // Visibilité
    public bool $is_active   = true;
    public bool $is_featured = false;

    // Spécifications techniques
    public array $specifications = [];

    // Images
    public        $mainImage        = null;
    public ?string $currentMainImage = null;
    public array  $additionalImages  = [];
    public array  $galleryImages     = [];

    protected function rules(): array
    {
        return [
            'name'                 => 'required|string|min:2|max:255',
            'sku'                  => 'nullable|string|max:100',
            'brand'                => 'nullable|string|max:100',
            'model'                => 'nullable|string|max:100',
            'category_id'          => 'required|exists:categories,id',
            'supplier_id'          => 'nullable|exists:suppliers,id',
            'description'          => 'nullable|string',
            'short_description'    => 'nullable|string|max:500',
            'unit'                 => 'required|string',
            'purchase_price'       => 'required|numeric|min:0',
            'selling_price'        => 'required|numeric|min:0',
            'old_price'            => 'nullable|numeric|min:0',
            'stock_quantity'       => 'required|integer|min:0',
            'stock_alert_threshold'=> 'required|integer|min:0',
            'specifications'          => 'nullable|array',
            'specifications.*.label' => 'required_with:specifications.*.value|string|max:100',
            'specifications.*.value' => 'nullable|string|max:255',
            'mainImage'              => 'nullable|image|max:3072',
            'galleryImages.*'        => 'nullable|image|max:3072',
        ];
    }

    protected $messages = [
        'name.required'       => 'Le nom du produit est obligatoire.',
        'category_id.required'=> 'Veuillez sélectionner une catégorie.',
        'category_id.exists'  => 'Catégorie invalide.',
        'purchase_price.required' => 'Le prix d\'achat est obligatoire.',
        'selling_price.required'  => 'Le prix de vente est obligatoire.',
        'mainImage.image'        => 'Le fichier doit être une image.',
        'mainImage.max'          => 'L\'image ne doit pas dépasser 3 Mo.',
        'galleryImages.*.image'  => 'Le fichier doit être une image.',
        'galleryImages.*.max'    => 'Les images de galerie ne doivent pas dépasser 3 Mo.',
    ];

    // Calcul TTC automatique quand prix ou TVA change
    public function updatedSellingPrice(): void
    {
        if ($this->selling_price !== null) {
            $this->calculateTTC();
        }
    }

    public function updatedHasTva(): void
    {
        $this->calculateTTC();
    }

        private function calculateTTC(): void
    {
        // Plus de TVA — prix vente = prix affiché
        $this->selling_price_ttc = $this->selling_price ?? 0;
    }

    public function mount(?int $productId = null): void
    {
        if ($productId) {
            $this->isEditing = true;
            $this->productId = $productId;
            $this->loadProduct($productId);
        }
    }

    private function loadProduct(int $id): void
    {
        $product = Product::findOrFail($id);

        $this->name               = $product->name;
        $this->sku                = $product->sku ?? '';
        $this->brand              = $product->brand ?? '';
        $this->model              = $product->model ?? '';
        $this->category_id        = $product->category_id;
        $this->supplier_id        = $product->supplier_id;
        $this->description        = $product->description ?? '';
        $this->short_description  = $product->short_description ?? '';
        $this->unit               = $product->unit;
        $this->purchase_price     = (float) $product->purchase_price;
        $this->selling_price      = (float) $product->selling_price;
        $this->selling_price_ttc  = (float) $product->selling_price_ttc;
        $this->old_price          = $product->old_price ? (float) $product->old_price : null;
        $this->has_tva            = $product->has_tva;
        $this->stock_quantity     = $product->stock_quantity;
        $this->stock_alert_threshold = $product->stock_alert_threshold;
        $this->is_active          = $product->is_active;
        $this->is_featured        = $product->is_featured;
        $this->currentMainImage   = $product->main_image;
        $this->specifications     = $product->specifications ?? [];
    }

    // ── Gestion specs ─────────────────────────────
    public function addSpec(string $label = ''): void
    {
        $this->specifications[] = ['label' => $label, 'value' => ''];
    }

    public function removeSpec(int $index): void
    {
        array_splice($this->specifications, $index, 1);
        $this->specifications = array_values($this->specifications);
    }

    public function removeGalleryImage(int $id): void
    {
        $image = ProductImage::find($id);
        if ($image && $image->product_id === $this->productId) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
    }

    public function removePendingGallery(int $index): void
    {
        array_splice($this->galleryImages, $index, 1);
    }

    public function save(): void
    {
        $this->validate();
        $this->calculateTTC();

        $mainImagePath = $this->currentMainImage;
        if ($this->mainImage) {
            // Supprimer l'ancienne image
            if ($this->currentMainImage) {
                Storage::disk('public')->delete($this->currentMainImage);
            }
            $mainImagePath = $this->mainImage->store('products', 'public');
        }

        $data = [
            'name'                 => $this->name,
            'sku'                  => $this->sku ?: null,
            'brand'                => $this->brand ?: null,
            'model'                => $this->model ?: null,
            'category_id'          => $this->category_id,
            'supplier_id'          => $this->supplier_id ?: null,
            'description'          => $this->description ?: null,
            'short_description'    => $this->short_description ?: null,
            'unit'                 => $this->unit,
            'purchase_price'       => $this->purchase_price,
            'selling_price'        => $this->selling_price,
            'selling_price_ttc'    => $this->selling_price_ttc,
            'old_price'            => ($this->old_price > 0) ? $this->old_price : null,
            'has_tva'              => false,
            'stock_quantity'       => $this->stock_quantity,
            'stock_alert_threshold'=> $this->stock_alert_threshold,
            'is_active'            => $this->is_active,
            'is_featured'          => $this->is_featured,
            'main_image'           => $mainImagePath,
            'specifications'       => array_values(array_filter(
                $this->specifications,
                fn($s) => !empty(trim($s['label'] ?? ''))
            )),
        ];

        if ($this->isEditing) {
            $product = Product::findOrFail($this->productId);
            $oldStock = $product->stock_quantity;
            $product->update($data);

            // Enregistrer mouvement de stock si changement
            if ($oldStock !== $this->stock_quantity) {
                StockMovement::create([
                    'product_id'      => $product->id,
                    'user_id'         => Auth::id(),
                    'type'            => $this->stock_quantity > $oldStock ? 'in' : 'out',
                    'source'          => 'adjustment',
                    'quantity_before' => $oldStock,
                    'quantity_change' => abs($this->stock_quantity - $oldStock),
                    'quantity_after'  => $this->stock_quantity,
                    'reason'          => 'Ajustement manuel',
                    'moved_at'        => now(),
                ]);
            }

            session()->flash('success', 'Produit modifié avec succès !');
        } else {
            $product = Product::create($data);

            // Mouvement de stock initial
            if ($this->stock_quantity > 0) {
                StockMovement::create([
                    'product_id'      => $product->id,
                    'user_id'         => Auth::id(),
                    'type'            => 'in',
                    'source'          => 'adjustment',
                    'quantity_before' => 0,
                    'quantity_change' => $this->stock_quantity,
                    'quantity_after'  => $this->stock_quantity,
                    'reason'          => 'Stock initial',
                    'moved_at'        => now(),
                ]);
            }

            session()->flash('success', 'Produit créé avec succès !');
        }

        // Sauvegarder les images de galerie
        foreach ($this->galleryImages as $img) {
            $path = $img->store('products/gallery', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'order'      => ProductImage::where('product_id', $product->id)->count(),
            ]);
        }

        $this->redirect(route('produits.index'));
    }

    public function render()
    {
        return view('livewire.products.product-form', [
            'categories'      => Category::where('is_active', true)->orderBy('name')->get(),
            'suppliers'       => Supplier::where('is_active', true)->orderBy('name')->get(),
            'tvaRate'         => CompanySetting::getSettings()->tva_rate ?? 18,
            'galleryExisting' => $this->productId
                ? ProductImage::where('product_id', $this->productId)->orderBy('order')->get()
                : collect(),
        ]);
    }
}