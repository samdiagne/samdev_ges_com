<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Product;
use App\Models\CompanySetting;

class FicheProduit extends Component
{
    public Product $product;
    public int     $quantity    = 1;
    public int     $activeImage = 0;
    public bool    $added       = false;

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function incrementQty(): void
    {
        if ($this->quantity < $this->product->stock_quantity) {
            $this->quantity++;
        }
    }

    public function decrementQty(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function setImage(int $index): void
    {
        $this->activeImage = $index;
    }

    public function addToCart(): void
    {
        if ($this->product->stock_quantity <= 0) return;

        $cart = session('cart', []);
        $key  = 'product_' . $this->product->id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $this->quantity;
        } else {
            $cart[$key] = [
                'product_id' => $this->product->id,
                'name'       => $this->product->name,
                'price'      => $this->product->selling_price,
                'image'      => $this->product->main_image,
                'slug'       => $this->product->slug,
                'quantity'   => $this->quantity,
                'stock'      => $this->product->stock_quantity,
            ];
        }

        // Limiter au stock disponible
        if ($cart[$key]['quantity'] > $this->product->stock_quantity) {
            $cart[$key]['quantity'] = $this->product->stock_quantity;
        }

        session(['cart' => $cart]);
        $this->added = true;
        $this->dispatch('cart-updated');
        $this->dispatch('open-cart');

        // Reset après 2 secondes
        $this->dispatch('reset-added');
    }

    public function render()
    {
        return view('livewire.shop.fiche-produit', [
            'settings' => CompanySetting::getSettings(),
        ]);
    }
}