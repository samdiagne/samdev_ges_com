<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\WithPagination;
use App\Mail\NewReviewAdminMail;
use App\Models\Avis;
use App\Models\Produit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProduitReviews extends Component
{
    use WithPagination;

    public Produit $produit;

    public int    $rating      = 5;
    public string $comment     = '';
    public string $authorName  = '';
    public string $authorEmail = '';
    public bool   $submitted   = false;

    protected function rules(): array
    {
        return [
            'rating'      => 'required|integer|min:1|max:5',
            'comment'     => 'required|string|min:5|max:1000',
            'authorName'  => 'required|string|min:2|max:100',
            'authorEmail' => 'nullable|email|max:255',
        ];
    }

    public function mount(Produit $produit): void
    {
        $this->produit = $produit;
        if (auth()->check()) {
            $this->authorName  = auth()->user()->name;
            $this->authorEmail = auth()->user()->email ?? '';
        }
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    public function submit(): void
    {
        $this->validate();

        $avis = Avis::create([
            'produit_id'     => $this->produit->id,
            'user_id'        => auth()->id(),
            'nom_auteur'     => $this->authorName,
            'note'           => $this->rating,
            'commentaire'    => $this->comment,
            'est_approuve'   => false,
        ]);

        try {
            Mail::to(config('mail.admin_address'))->send(new NewReviewAdminMail($avis));
        } catch (\Throwable $e) {
            Log::error('Mail nouvel avis : ' . $e->getMessage());
        }

        $this->submitted   = true;
        $this->comment     = '';
        $this->rating      = 5;
        $this->resetErrorBag();
    }

    public function render()
    {
        $avis = Avis::where('produit_id', $this->produit->id)
            ->where('est_approuve', true)
            ->latest()
            ->paginate(5);

        $noteMoyenne    = round($this->produit->avisApprouves()->avg('note') ?? 0, 1);
        $totalApprouves = $this->produit->avisApprouves()->count();

        $ratingCounts = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingCounts[$i] = Avis::where('produit_id', $this->produit->id)
                ->where('est_approuve', true)
                ->where('note', $i)
                ->count();
        }

        return view('livewire.shop.produit-reviews',
            compact('avis', 'noteMoyenne', 'totalApprouves', 'ratingCounts'));
    }
}
