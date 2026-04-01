<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Mail\NewReviewAdminMail;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Mail;

class ProductReviews extends Component
{
    public Product $product;

    // Form
    public int    $rating      = 5;
    public string $comment     = '';
    public string $authorName  = '';
    public string $authorEmail = '';
    public bool   $submitted   = false;

    protected function rules(): array
    {
        return [
            'rating'      => 'required|integer|min:1|max:5',
            'comment'     => 'required|string|min:10|max:1000',
            'authorName'  => 'required|string|min:2|max:100',
            'authorEmail' => 'nullable|email|max:255',
        ];
    }

    protected $messages = [
        'rating.required'     => 'Veuillez sélectionner une note.',
        'comment.required'    => 'Le commentaire est obligatoire.',
        'comment.min'         => 'Le commentaire doit faire au moins 10 caractères.',
        'authorName.required' => 'Votre nom est obligatoire.',
    ];

    public function mount(Product $product): void
    {
        $this->product = $product;

        // Pré-remplir si connecté
        if (auth()->check()) {
            $this->authorName  = auth()->user()->name;
            $this->authorEmail = auth()->user()->email;
        }
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    public function submit(): void
    {
        $this->validate();

        // Vérifier si l'utilisateur a déjà laissé un avis
        if (auth()->check()) {
            $exists = ProductReview::where('product_id', $this->product->id)
                ->where('user_id', auth()->id())
                ->exists();

            if ($exists) {
                $this->addError('comment', 'Vous avez déjà laissé un avis pour ce produit.');
                return;
            }
        }

        $review = ProductReview::create([
            'product_id'   => $this->product->id,
            'user_id'      => auth()->id(),
            'author_name'  => $this->authorName,
            'author_email' => $this->authorEmail ?: null,
            'rating'       => $this->rating,
            'comment'      => $this->comment,
            'is_approved'  => false, // Modération manuelle
        ]);

        try {
            Mail::to(config('mail.admin_address'))->send(new NewReviewAdminMail($review));
        } catch (\Exception $e) {
            \Log::error('NewReviewAdminMail failed: ' . $e->getMessage());
        }

        $this->submitted = true;
        $this->reset(['comment', 'rating']);
        $this->rating = 5;
    }

    public function render()
    {
        $reviews = ProductReview::where('product_id', $this->product->id)
            ->where('is_approved', true)
            ->latest()
            ->get();

        $avgRating   = round($reviews->avg('rating') ?? 0, 1);
        $totalReviews = $reviews->count();

        $ratingCounts = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingCounts[$i] = $reviews->where('rating', $i)->count();
        }

        return view('livewire.shop.product-reviews',
            compact('reviews', 'avgRating', 'totalReviews', 'ratingCounts'));
    }
}