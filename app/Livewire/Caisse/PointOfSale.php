<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use App\Models\Plat;
use App\Models\Client;
use App\Models\Categorie;
use App\Services\VenteService;

class PointOfSale extends Component
{
    public string $rechercherPlat        = '';
    public string $filtreCategorie       = '';
    public array  $plats                 = [];
    public array  $panier                = [];

    public string $rechercherClient      = '';
    public array  $resultatsClients      = [];
    public ?int   $clientSelectionneId   = null;
    public string $clientSelectionneNom  = '';

    public string $clientRapideNom       = '';
    public string $clientRapideTel       = '';
    public bool   $utiliserClientRapide  = false;
    public ?array $doublonClient         = null;

    public string $modePaiement          = 'cash';
    public float  $montantRecu           = 0;
    public float  $remise                = 0;
    public string $notes                 = '';
    public string $dateVente             = '';

    public bool   $afficherModalPaiement = false;
    public bool   $afficherModalSucces   = false;

    public ?int   $derniereVenteId       = null;
    public string $derniereReference     = '';
    public float  $monnaieRendue         = 0;

    public float  $sousTotal             = 0;
    public float  $total                 = 0;

    public function mount(): void
    {
        $this->loadProducts();
        $this->dateVente = now()->toDateString();
    }

    public function loadProducts(): void
    {
        $this->plats = Plat::where('est_actif', true)
            ->where('stock', '>', 0)
            ->with('categorie')
            ->when($this->filtreCategorie, fn($q) =>
                $q->where('categorie_id', $this->filtreCategorie)
            )
            ->when($this->rechercherPlat, fn($q) =>
                $q->where('nom', 'like', '%' . $this->rechercherPlat . '%')
            )
            ->orderBy('nom')
            ->get()
            ->map(fn($p) => [
                'id'               => $p->id,
                'nom'              => $p->nom,
                'prix_vente'       => (float) $p->prix_vente,
                'stock'            => $p->stock,
                'image'            => $p->image_principale,
                'categorie'        => $p->categorie?->nom,
                'quantite_palier'  => $p->quantite_palier,
                'prix_palier'      => $p->prix_palier ? (float) $p->prix_palier : null,
            ])
            ->toArray();
    }

    public function updatedFiltreCategorie(): void { $this->loadProducts(); }
    public function updatedRechercherPlat(): void   { $this->loadProducts(); }

    public function togglePlat(int $platId): void
    {
        foreach ($this->panier as $cle => $item) {
            if ($item['plat_id'] === $platId) { $this->retirerDuPanier($cle); return; }
        }
        $this->ajouterAuPanier($platId);
    }

    public function addToCart(int $platId): void
    {
        $cleTrouvee = null;
        foreach ($this->panier as $cle => $item) {
            if ($item['plat_id'] === $platId) { $cleTrouvee = $cle; break; }
        }

        if ($cleTrouvee !== null) {
            $plat = Plat::find($platId);
            if ($this->panier[$cleTrouvee]['quantite'] >= $plat->stock) {
                session()->flash('pos_error', 'Stock insuffisant !'); return;
            }
            $this->panier[$cleTrouvee]['quantite']++;
            $this->recalcLine($cleTrouvee);
            $this->recalcTotals();
            return;
        } else {
            $plat = Plat::find($platId);
            if (! $plat || $plat->stock <= 0) {
                session()->flash('pos_error', 'Plat indisponible !'); return;
            }
            $this->panier[] = [
                'plat_id'          => $plat->id,
                'nom'              => $plat->nom,
                'prix_unitaire'    => (float) $plat->prix_vente,
                'quantite'         => 1,
                'remise'           => 0,
                'total'            => (float) $plat->prix_vente,
                'stock_max'        => $plat->stock,
                'quantite_palier'  => $plat->quantite_palier,
                'prix_palier'      => $plat->prix_palier ? (float) $plat->prix_palier : null,
                'prix_base'        => (float) $plat->prix_vente,
            ];
        }
        $this->recalcTotals();
    }

    public function updateQuantity(int $index, int $quantite): void
    {
        if ($quantite <= 0) { $this->retirerDuPanier($index); return; }
        if ($quantite > $this->panier[$index]['stock_max']) {
            $quantite = $this->panier[$index]['stock_max'];
        }
        $this->panier[$index]['quantite'] = $quantite;
        $this->recalcLine($index);
        $this->recalcTotals();
    }

    public function updateLineDiscount(int $index, float $remise): void
    {
        $this->panier[$index]['remise'] = max(0, $remise);
        $this->recalcLine($index);
        $this->recalcTotals();
    }

    public function removeFromCart(int $index): void
    {
        array_splice($this->panier, $index, 1);
        $this->panier = array_values($this->panier);
        $this->recalcTotals();
    }

    public function clearCart(): void
    {
        $this->panier               = [];
        $this->sousTotal            = 0;
        $this->total                = 0;
        $this->remise               = 0;
        $this->clientSelectionneId  = null;
        $this->clientSelectionneNom = '';
        $this->notes                = '';
    }

    private function recalcLine(int $index): void
    {
        $item = $this->panier[$index];

        $palierQte = $item['quantite_palier'] ?? null;
        $palierPrix = $item['prix_palier'] ?? null;
        $prixBase = $item['prix_base'] ?? $item['prix_unitaire'];

        if ($palierQte && $palierPrix && $item['quantite'] >= $palierQte) {
            $this->panier[$index]['prix_unitaire'] = $palierPrix;
        } else {
            $this->panier[$index]['prix_unitaire'] = $prixBase;
        }

        $this->panier[$index]['total'] = round(
            ($this->panier[$index]['prix_unitaire'] * $item['quantite']) - $item['remise'], 2
        );
    }

    private function recalcTotals(): void
    {
        $this->sousTotal   = round(array_sum(array_column($this->panier, 'total')), 2);
        $this->total       = round($this->sousTotal - $this->remise, 2);
        $this->montantRecu = $this->total;
    }

    public function updatedRechercherClient(): void
    {
        if (strlen($this->rechercherClient) < 2) { $this->resultatsClients = []; return; }
        $this->resultatsClients = Client::where(fn($q) =>
            $q->where('nom', 'like', '%' . $this->rechercherClient . '%')
              ->orWhere('telephone', 'like', '%' . $this->rechercherClient . '%')
        )->limit(5)->get(['id', 'nom', 'telephone'])->toArray();
    }

    public function selectClient(int $id, string $nom): void
    {
        $this->clientSelectionneId  = $id;
        $this->clientSelectionneNom = $nom;
        $this->rechercherClient     = '';
        $this->resultatsClients     = [];
        $this->utiliserClientRapide = false;
        $this->doublonClient        = null;
    }

    public function updatedClientRapideTel(): void
    {
        $this->doublonClient = null;
        if (strlen($this->clientRapideTel) >= 6) {
            $trouve = Client::where('telephone', $this->clientRapideTel)->first();
            if ($trouve) {
                $this->doublonClient = ['id' => $trouve->id, 'nom' => $trouve->nom, 'telephone' => $trouve->telephone];
            }
        }
    }

    public function clearClient(): void
    {
        $this->clientSelectionneId  = null;
        $this->clientSelectionneNom = '';
        $this->utiliserClientRapide = false;
        $this->clientRapideNom      = '';
        $this->clientRapideTel      = '';
        $this->doublonClient        = null;
    }

    public function openPaymentModal(): void
    {
        if (empty($this->panier)) { session()->flash('pos_error', 'Le panier est vide !'); return; }
        $this->montantRecu           = $this->total;
        $this->afficherModalPaiement = true;
    }

    public function updatedRemise(): void
    {
        $this->remise      = max(0, $this->remise);
        $this->total       = round($this->sousTotal - $this->remise, 2);
        $this->montantRecu = $this->total;
    }

    public function processPayment(VenteService $venteService): void
    {
        if (empty($this->panier)) return;
        if ($this->montantRecu < $this->total) {
            $this->addError('montantRecu', 'Montant insuffisant !'); return;
        }
        try {
            $clientId = $this->clientSelectionneId;
            if (! $clientId && $this->utiliserClientRapide && $this->clientRapideNom) {
                $client   = Client::create(['nom' => $this->clientRapideNom, 'telephone' => $this->clientRapideTel ?: null]);
                $clientId = $client->id;
            }
            $vente = $venteService->creerVente(
                panier: $this->panier,
                paiement: [
                    'mode_paiement' => $this->modePaiement,
                    'montant_recu'  => $this->montantRecu,
                    'remise'        => $this->remise,
                    'notes'         => $this->notes,
                    'date_vente'    => $this->dateVente ?: now()->toDateString(),
                ],
                clientId: $clientId
            );
            $this->derniereVenteId       = $vente->id;
            $this->derniereReference     = $vente->reference;
            $this->monnaieRendue         = $vente->monnaie_rendue;
            $this->afficherModalPaiement = false;
            $this->afficherModalSucces   = true;
            $this->clearCart();
        } catch (\Exception $e) {
            session()->flash('pos_error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function render()
    {
        $categories = Categorie::where('est_active', true)
            ->whereHas('plats', fn($q) => $q->where('est_actif', true)->where('stock', '>', 0))
            ->get(['id', 'nom']);
        return view('livewire.caisse.point-of-sale', compact('categories'));
    }
}
