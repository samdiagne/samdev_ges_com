<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Avis;
use App\Models\Plat;

class PlatReviews extends Component
{
    use WithPagination;

    public Plat $plat;

    // Variables alignées sur la vue
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

    public function mount(Plat $plat): void
    {
        $this->plat = $plat;
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

        Avis::create([
            'plat_id'        => $this->plat->id,
            'user_id' => auth()->id(),
            'nom_auteur'     => $this->authorName,
            'note'           => $this->rating,
            'commentaire'    => $this->comment,
            'est_approuve'   => false,
        ]);

        $this->submitted   = true;
        $this->comment     = '';
        $this->rating      = 5;
        $this->resetErrorBag();
    }

    public function render()
    {
        $avis = Avis::where('plat_id', $this->plat->id)
            ->where('est_approuve', true)
            ->latest()
            ->paginate(5);

        $noteMoyenne    = round($this->plat->avisApprouves()->avg('note') ?? 0, 1);
        $totalApprouves = $this->plat->avisApprouves()->count();

        // Répartition par note (pour les étoiles)
        $ratingCounts = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingCounts[$i] = Avis::where('plat_id', $this->plat->id)
                ->where('est_approuve', true)
                ->where('note', $i)
                ->count();
        }

        return view('livewire.shop.plat-reviews',
            compact('avis', 'noteMoyenne', 'totalApprouves', 'ratingCounts'));
    }
}
