<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use App\Models\Produit;
use App\Models\Client;
use App\Models\Categorie;
use App\Models\SessionCaisse;
use App\Services\VenteService;
use Illuminate\Support\Facades\Auth;

class PointOfSale extends Component
{
    public string $rechercherPlat        = '';
    public string $filtreCategorie       = '';
    public array  $produits               = [];
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

    public bool   $afficherModalPaiement    = false;
    public bool   $afficherModalSucces      = false;
    public bool   $afficherConfirmationVider = false;
    public string $erreurPaiement        = '';

    public ?int   $derniereVenteId          = null;
    public string $derniereReference        = '';
    public float  $monnaieRendue            = 0;
    public float  $monnaieRendueEspeces     = 0;
    public float  $monnaieRendueWave        = 0;
    public string $canalMonnaie             = 'especes'; // especes, wave, mixte
    public float  $partMonnaieWave          = 0;

    public float  $sousTotal                = 0;
    public float  $total                    = 0;

    // ── Session & Clôture de Caisse ──
    public ?int   $sessionCaisseId            = null;
    public bool   $afficherModalOuverture     = false;
    public string $fondCaisseOuverture        = '';
    public string $fondCaisseWaveOuverture    = '';

    public bool   $afficherModalCloture       = false;
    public string $montantReelEspeces         = '';
    public string $montantReelWave            = '';
    public string $notesCloture               = '';
    public ?array $resumeCloture              = null;
    public bool   $afficherModalSuccesCloture = false;
    public ?int   $derniereSessionClotureeId  = null;

    // ── Composition Menu Personnalisable / Fast-Food ──
    public bool   $afficherModalMenu   = false;
    public ?int   $menuProduitId       = null;
    public string $menuProduitNom      = '';
    public string $menuModePrix        = 'fixe'; // 'fixe' ou 'supplement'
    public float  $menuSupplementPrix  = 0;
    public float  $menuPrixTotal       = 0;
    public array  $menuEtapes          = [];
    public array  $menuSelections      = [];

    // ── Sélecteur de Boissons / Jus (250ml, 1L, Canettes) ─────
    public bool   $afficherModalBoisson       = false;
    public string $boissonType                = ''; // 'jus_250', 'jus_1l', 'gazeuse'
    public string $boissonTitre               = '';
    public string $boissonIcone               = '🥤';
    public float  $boissonPrix                = 0;
    public array  $listeOptionsBoisson        = [];

    public function mount(): void
    {
        $this->loadProducts();
        $this->dateVente = now()->toDateString();
        $this->verifierSession();
    }

    public function loadProducts(): void
    {
        $this->produits = Produit::where('est_actif', true)
            ->where(fn($q) => $q->where('stock', '>', 0)->orWhere('sur_commande', true))
            ->with('categorie')
            ->when(! $this->rechercherPlat, fn($q) =>
                $q->where('est_variante', false)
            )
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
                'sur_commande'     => $p->sur_commande,
                'image'            => $p->image_principale,
                'categorie'        => $p->categorie?->nom,
                'quantite_palier'  => $p->quantite_palier,
                'prix_palier'      => $p->prix_palier ? (float) $p->prix_palier : null,
            ])
            ->toArray();
    }

    public function updatedFiltreCategorie(): void { $this->loadProducts(); }
    public function updatedRechercherPlat(): void   { $this->loadProducts(); }

    public function toggleProduit(int $platId): void
    {
        foreach ($this->panier as $cle => $item) {
            if ($item['produit_id'] === $platId) { $this->removeFromCart($cle); return; }
        }
        $this->addToCart($platId);
    }

    public function addToCart(int $platId): void
    {
        $plat = Produit::find($platId);
        if (! $plat || (! $plat->sur_commande && $plat->stock <= 0)) {
            session()->flash('pos_error', 'Plat indisponible !'); return;
        }

        // Si le produit est un menu composé ou classé comme tel
        if ($plat->isMenu() || $plat->est_menu || $plat->composants()->exists()) {
            $this->ouvrirModalMenu($platId);
            return;
        }

        // Sélecteur Jus 250 ml (ID 44 ou 'Jus 250 ml')
        $nomBas = strtolower(trim($plat->nom));
        if ($plat->id == 44 || ($plat->categorie_id == 4 && str_contains($nomBas, '250') && ! str_contains($nomBas, 'pack') && ! $plat->est_variante)) {
            $this->ouvrirModalBoisson('jus_250');
            return;
        }

        // Sélecteur Jus 1 L
        if ($plat->slug === 'jus-1-l' || ($plat->categorie_id == 4 && in_array($nomBas, ['jus 1 l', 'jus 1l', 'jus (1 l)', 'jus 1 l']) && ! $plat->est_variante)) {
            $this->ouvrirModalBoisson('jus_1l');
            return;
        }

        // Sélecteur Boisson gazeuse (ID 71 ou 'Boisson gazeuse')
        if ($plat->id == 71 || ($plat->categorie_id == 5 && $nomBas === 'boisson gazeuse' && ! $plat->est_variante)) {
            $this->ouvrirModalBoisson('gazeuse');
            return;
        }

        $cleTrouvee = null;
        foreach ($this->panier as $cle => $item) {
            if (($item['produit_id'] === $platId) && ! ($item['is_menu'] ?? false)) {
                $cleTrouvee = $cle;
                break;
            }
        }

        if ($cleTrouvee !== null) {
            if (! $plat->sur_commande && $this->panier[$cleTrouvee]['quantite'] >= $plat->stock) {
                session()->flash('pos_error', 'Stock insuffisant !'); return;
            }
            $this->panier[$cleTrouvee]['quantite']++;
            $this->recalcLine($cleTrouvee);
            $this->recalcTotals();
            return;
        } else {
            $this->panier[] = [
                'produit_id'       => $plat->id,
                'nom'              => $plat->nom,
                'prix_unitaire'    => (float) $plat->prix_vente,
                'quantite'         => 1,
                'remise'           => 0,
                'total'            => (float) $plat->prix_vente,
                'stock_max'        => $plat->sur_commande ? null : $plat->stock,
                'quantite_palier'  => $plat->quantite_palier,
                'prix_palier'      => $plat->prix_palier ? (float) $plat->prix_palier : null,
                'prix_base'        => (float) $plat->prix_vente,
                'is_menu'          => false,
            ];
        }
        $this->recalcTotals();
    }

    // ── Composition Menu Personnalisable / Fast-Food ─────────

    public function ouvrirModalMenu(int $produitId): void
    {
        $produit = Produit::with(['composants.categorie', 'composants.produitFixe'])->find($produitId);
        if (! $produit) return;

        $this->menuProduitId      = $produit->id;
        $this->menuProduitNom     = $produit->nom;
        $this->menuModePrix       = $produit->mode_prix_menu ?? 'fixe';
        $this->menuSupplementPrix = (float) ($produit->supplement_menu ?? 0);
        $this->menuEtapes         = [];
        $this->menuSelections     = [];

        $composants = $produit->composants;

        // Si aucun composant configuré explicitement, proposer des étapes dynamiques intelligentes
        if ($composants->isEmpty()) {
            $nomLower = strtolower($produit->nom);
            $categoriesDispos = Categorie::where('est_active', true)->get();

            $catPlat = null;
            if (str_contains($nomLower, 'tacos')) {
                $catPlat = $categoriesDispos->first(fn($c) => str_contains(strtolower($c->nom), 'tacos'));
            } elseif (str_contains($nomLower, 'burger')) {
                $catPlat = $categoriesDispos->first(fn($c) => str_contains(strtolower($c->nom), 'burger'));
            }
            if (! $catPlat) {
                $catPlat = $categoriesDispos->first(fn($c) => str_contains(strtolower($c->nom), 'burger') || str_contains(strtolower($c->nom), 'tacos') || str_contains(strtolower($c->nom), 'plat'));
            }

            $catBoisson = $categoriesDispos->first(fn($c) => str_contains(strtolower($c->nom), 'jus') || str_contains(strtolower($c->nom), 'boisson'));

            $etapesDefaut = [];
            if ($catPlat) {
                $etapesDefaut[] = [
                    'titre'          => 'Choisir le sandwich / plat',
                    'type_selection' => 'categorie',
                    'categorie_id'   => $catPlat->id,
                ];
            }
            $etapesDefaut[] = [
                'titre'          => 'Accompagnement',
                'type_selection' => 'texte_fixe',
                'nom_fixe'       => '🍟 Frites croustillantes',
            ];
            if ($catBoisson) {
                $etapesDefaut[] = [
                    'titre'          => 'Choisir la boisson',
                    'type_selection' => 'categorie',
                    'categorie_id'   => $catBoisson->id,
                ];
            }
        } else {
            $etapesDefaut = $composants->map(fn($c) => [
                'titre'           => $c->titre,
                'type_selection'  => $c->type_selection,
                'categorie_id'    => $c->categorie_id,
                'produit_fixe_id' => $c->produit_fixe_id,
                'produit_fixe'    => $c->produitFixe ? [
                    'id'   => $c->produitFixe->id,
                    'nom'  => $c->produitFixe->nom,
                    'prix' => (float) $c->produitFixe->prix_vente,
                ] : null,
            ])->toArray();
        }

        foreach ($etapesDefaut as $index => $etape) {
            $options = [];
            if (($etape['type_selection'] ?? '') === 'categorie' && ! empty($etape['categorie_id'])) {
                $options = Produit::where('categorie_id', $etape['categorie_id'])
                    ->where('est_actif', true)
                    ->orderBy('nom')
                    ->get(['id', 'nom', 'prix_vente', 'stock', 'sur_commande'])
                    ->map(fn($p) => [
                        'id'           => $p->id,
                        'nom'          => $p->nom,
                        'prix'         => (float) $p->prix_vente,
                        'stock'        => $p->stock,
                        'sur_commande' => $p->sur_commande,
                    ])->toArray();
            } elseif (($etape['type_selection'] ?? '') === 'produit_fixe' && ! empty($etape['produit_fixe'])) {
                $options = [$etape['produit_fixe']];
            }

            $selectionId = ! empty($options) ? $options[0]['id'] : null;
            $this->menuSelections[$index] = $selectionId;

            $this->menuEtapes[$index] = [
                'titre'          => $etape['titre'],
                'type_selection' => $etape['type_selection'],
                'nom_fixe'       => $etape['nom_fixe'] ?? null,
                'options'        => $options,
            ];
        }

        $this->calculerPrixMenu();
        $this->afficherModalMenu = true;
    }

    public function selectMenuOption(int $stepIndex, int $produitId): void
    {
        $this->menuSelections[$stepIndex] = $produitId;
        $this->calculerPrixMenu();
    }

    public function calculerPrixMenu(): void
    {
        $produit = Produit::find($this->menuProduitId);
        if (! $produit) return;

        if ($this->menuModePrix === 'supplement') {
            $premierId = $this->menuSelections[0] ?? null;
            $premierProd = $premierId ? Produit::find($premierId) : null;
            $prixBase = $premierProd ? (float) $premierProd->prix_vente : (float) $produit->prix_vente;
            $this->menuPrixTotal = round($prixBase + $this->menuSupplementPrix, 2);
        } else {
            $this->menuPrixTotal = round((float) $produit->prix_vente, 2);
        }
    }

    public function validerAjoutMenu(): void
    {
        $produit = Produit::find($this->menuProduitId);
        if (! $produit) {
            session()->flash('pos_error', 'Menu introuvable.');
            return;
        }

        $detailsParts = [];
        $idsSelectionnes = [];

        foreach ($this->menuEtapes as $index => $etape) {
            if (($etape['type_selection'] ?? '') === 'texte_fixe') {
                $detailsParts[] = $etape['nom_fixe'];
                continue;
            }

            $selId = $this->menuSelections[$index] ?? null;
            if (! $selId) {
                session()->flash('pos_error', "Veuillez faire un choix pour : {$etape['titre']}");
                return;
            }

            $p = Produit::find($selId);
            if ($p) {
                $detailsParts[] = $p->nom;
                $idsSelectionnes[] = $p->id;
            }
        }

        $details = implode(' • ', $detailsParts);
        $nomLigne = $this->menuProduitNom;

        $cleTrouvee = null;
        foreach ($this->panier as $cle => $item) {
            if (
                ($item['is_menu'] ?? false) &&
                ($item['produit_id'] ?? null) === $this->menuProduitId &&
                ($item['details'] ?? null) === $details
            ) {
                $cleTrouvee = $cle;
                break;
            }
        }

        if ($cleTrouvee !== null) {
            $this->panier[$cleTrouvee]['quantite']++;
            $this->panier[$cleTrouvee]['total'] = round($this->panier[$cleTrouvee]['prix_unitaire'] * $this->panier[$cleTrouvee]['quantite'], 2);
        } else {
            $this->panier[] = [
                'produit_id'       => $this->menuProduitId,
                'nom'              => $nomLigne,
                'prix_unitaire'    => $this->menuPrixTotal,
                'quantite'         => 1,
                'remise'           => 0,
                'total'            => $this->menuPrixTotal,
                'stock_max'        => null,
                'quantite_palier'  => null,
                'prix_palier'      => null,
                'prix_base'        => $this->menuPrixTotal,
                'is_menu'          => true,
                'details'          => $details,
                'menu_selections'  => $idsSelectionnes,
            ];
        }

        $this->recalcTotals();
        $this->afficherModalMenu = false;
        session()->flash('pos_success', "{$nomLigne} ajouté au panier !");
    }

    // ── Sélecteur de Boissons / Jus ───────────────────────────

    public function ouvrirModalBoisson(string $type): void
    {
        $this->boissonType = $type;

        if ($type === 'jus_250') {
            $this->boissonTitre = 'Choisir le parfum — Jus 250 ml';
            $this->boissonIcone = '🥤';
            $this->boissonPrix  = 500;
            $this->listeOptionsBoisson = Produit::where('categorie_id', 4)
                ->where('est_actif', true)
                ->where('est_variante', true)
                ->where('nom', 'like', '%250%')
                ->where('nom', 'not like', '%pack%')
                ->orderBy('nom')
                ->get(['id', 'nom', 'prix_vente', 'stock', 'sur_commande', 'image_principale'])
                ->toArray();
        } elseif ($type === 'jus_1l') {
            $this->boissonTitre = 'Choisir le parfum — Jus 1 L';
            $this->boissonIcone = '🍾';
            $this->boissonPrix  = 2000;
            $this->listeOptionsBoisson = Produit::where('categorie_id', 4)
                ->where('est_actif', true)
                ->where('est_variante', true)
                ->where(function ($q) {
                    $q->where('nom', 'like', '%1 L%')
                      ->orWhere('nom', 'like', '%1L%');
                })
                ->where('nom', 'not like', '%pack%')
                ->orderBy('nom')
                ->get(['id', 'nom', 'prix_vente', 'stock', 'sur_commande', 'image_principale'])
                ->toArray();
        } elseif ($type === 'gazeuse') {
            $this->boissonTitre = 'Choisir la boisson gazeuse (33cl)';
            $this->boissonIcone = '🥤';
            $this->boissonPrix  = 500;
            $this->listeOptionsBoisson = Produit::where('categorie_id', 5)
                ->where('est_actif', true)
                ->where('est_variante', true)
                ->orderBy('nom')
                ->get(['id', 'nom', 'prix_vente', 'stock', 'sur_commande', 'image_principale'])
                ->toArray();
        }

        $this->afficherModalBoisson = true;
    }

    public function choisirOptionBoisson(int $produitOptionId): void
    {
        $option = Produit::find($produitOptionId);
        if (! $option) return;

        if (! $option->sur_commande && $option->stock <= 0) {
            session()->flash('pos_error', "{$option->nom} est épuisé !");
            return;
        }

        $nomLigne = match ($this->boissonType) {
            'jus_250' => "Jus 250 ml : {$option->nom}",
            'jus_1l'  => "Jus 1 L : {$option->nom}",
            'gazeuse' => "Boisson gazeuse : {$option->nom}",
            default   => $option->nom,
        };

        $prefix = match ($this->boissonType) {
            'jus_250' => '🥤 Jus 250 ml',
            'jus_1l'  => '🍾 Bouteille 1L',
            'gazeuse' => '🥤 Canette 33cl',
            default   => '🥤 Boisson',
        };

        $details = "{$prefix} • {$option->nom}";

        $cleTrouvee = null;
        foreach ($this->panier as $cle => $item) {
            if ($item['produit_id'] === $option->id && ($item['details'] ?? null) === $details) {
                $cleTrouvee = $cle;
                break;
            }
        }

        $prix = (float) $option->prix_vente;
        if ($prix <= 0) {
            $prix = $this->boissonPrix;
        }

        if ($cleTrouvee !== null) {
            if (! $option->sur_commande && $this->panier[$cleTrouvee]['quantite'] >= $option->stock) {
                session()->flash('pos_error', "Stock maximum disponible atteint pour {$option->nom} !");
                return;
            }
            $this->panier[$cleTrouvee]['quantite']++;
            $this->panier[$cleTrouvee]['total'] = round($this->panier[$cleTrouvee]['prix_unitaire'] * $this->panier[$cleTrouvee]['quantite'], 2);
        } else {
            $this->panier[] = [
                'produit_id'       => $option->id,
                'nom'              => $nomLigne,
                'prix_unitaire'    => $prix,
                'quantite'         => 1,
                'remise'           => 0,
                'total'            => $prix,
                'stock_max'        => $option->sur_commande ? null : $option->stock,
                'quantite_palier'  => $option->quantite_palier,
                'prix_palier'      => $option->prix_palier ? (float) $option->prix_palier : null,
                'prix_base'        => $prix,
                'is_menu'          => false,
                'details'          => $details,
            ];
        }

        $this->afficherModalBoisson = false;
        $this->recalcTotals();
        session()->flash('pos_success', "{$nomLigne} ajouté au panier !");
    }

    public function updateQuantity(int $index, $quantite): void
    {
        $quantite = (int) $quantite;
        if (! isset($this->panier[$index])) { return; }
        if ($quantite <= 0) { $this->removeFromCart($index); return; }
        $stockMax = $this->panier[$index]['stock_max'] ?? null;
        if ($stockMax !== null && $quantite > $stockMax) {
            $quantite = (int) $stockMax;
            session()->flash('pos_error', "Stock maximum disponible atteint ({$stockMax}) !");
        }
        $this->panier[$index]['quantite'] = $quantite;
        $this->recalcLine($index);
        $this->recalcTotals();
    }

    public function setQuantity(int $index, $quantite): void
    {
        $this->updateQuantity($index, $quantite);
    }

    public function updatedPanier($value, $key): void
    {
        $parts = explode('.', (string) $key);
        if (count($parts) === 2 && $parts[1] === 'quantite') {
            $this->updateQuantity((int) $parts[0], $value);
        }
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
        $this->panier                    = [];
        $this->sousTotal                 = 0;
        $this->total                     = 0;
        $this->remise                    = 0;
        $this->clientSelectionneId       = null;
        $this->clientSelectionneNom      = '';
        $this->notes                     = '';
        $this->afficherConfirmationVider = false;
    }

    private function recalcLine(int $index): void
    {
        $item = $this->panier[$index];

        if ($item['is_menu'] ?? false) {
            $this->panier[$index]['total'] = round(
                ($this->panier[$index]['prix_unitaire'] * $item['quantite']) - ($item['remise'] ?? 0), 2
            );
            return;
        }

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
        
        if (! $this->sessionCaisseId) {
            $this->verifierSession();
            if (! $this->sessionCaisseId) {
                session()->flash('pos_error', 'Veuillez ouvrir votre session de caisse avant d\'encaisser !');
                $this->afficherModalOuverture = true;
                return;
            }
        }

        $this->erreurPaiement        = '';
        $this->montantRecu           = $this->total;
        $this->canalMonnaie          = 'especes';
        $this->partMonnaieWave       = 0;
        $this->afficherModalPaiement = true;
    }

    public function updatedRemise(): void
    {
        $this->remise      = max(0, $this->remise);
        $this->total       = round($this->sousTotal - $this->remise, 2);
        $this->montantRecu = $this->total;
    }

    // ── Gestion des Sessions de Caisse ───────────────────────

    public function verifierSession(): void
    {
        $session = SessionCaisse::where('user_id', Auth::id())
            ->where('statut', 'ouverte')
            ->latest('id')
            ->first();

        if ($session) {
            $this->sessionCaisseId = $session->id;
            $this->afficherModalOuverture = false;
        } else {
            $this->sessionCaisseId = null;
            $this->afficherModalOuverture = true;
        }
    }

    public function ouvrirSession(): void
    {
        $this->validate([
            'fondCaisseOuverture'     => 'required|numeric|min:0',
            'fondCaisseWaveOuverture' => 'required|numeric|min:0',
        ], [
            'fondCaisseOuverture.required'     => 'Le fond de caisse espèces est obligatoire.',
            'fondCaisseOuverture.numeric'      => 'Le fond de caisse espèces doit être un nombre.',
            'fondCaisseOuverture.min'          => 'Le fond de caisse espèces ne peut pas être négatif.',
            'fondCaisseWaveOuverture.required' => 'Le solde de caisse Wave est obligatoire (saisir 0 si vide).',
            'fondCaisseWaveOuverture.numeric'  => 'Le solde de caisse Wave doit être un nombre.',
            'fondCaisseWaveOuverture.min'      => 'Le solde de caisse Wave ne peut pas être négatif.',
        ]);

        $tz = config('app.timezone', 'Africa/Dakar');
        $session = SessionCaisse::create([
            'reference'                  => SessionCaisse::genererReference(),
            'user_id'                    => Auth::id(),
            'fond_caisse_ouverture'      => (float) $this->fondCaisseOuverture,
            'fond_caisse_wave_ouverture' => (float) $this->fondCaisseWaveOuverture,
            'ouvert_le'                  => now($tz),
            'statut'                     => 'ouverte',
        ]);

        $this->sessionCaisseId = $session->id;
        $this->afficherModalOuverture = false;
        $this->fondCaisseOuverture = '';
        $this->fondCaisseWaveOuverture = '';
        session()->flash('pos_success', "Session {$session->reference} ouverte avec succès !");
    }

    public function preparerCloture(): void
    {
        $session = SessionCaisse::find($this->sessionCaisseId);
        if (! $session || ! $session->estOuverte()) {
            $this->verifierSession();
            return;
        }

        $totaux = $session->totaux_en_direct;
        $this->montantReelEspeces = (string) $totaux['theorique_cash'];
        $this->montantReelWave    = (string) $totaux['theorique_wave'];
        $this->notesCloture       = '';
        $this->calculerResumeCloture($session, $totaux);
        $this->afficherModalCloture = true;
    }

    public function updatedMontantReelEspeces(): void
    {
        $this->rafraichirResumeCloture();
    }

    public function updatedMontantReelWave(): void
    {
        $this->rafraichirResumeCloture();
    }

    private function rafraichirResumeCloture(): void
    {
        if ($this->sessionCaisseId) {
            $session = SessionCaisse::find($this->sessionCaisseId);
            if ($session) {
                $this->calculerResumeCloture($session, $session->totaux_en_direct);
            }
        }
    }

    private function calculerResumeCloture(SessionCaisse $session, array $totaux): void
    {
        $reelEspeces = is_numeric($this->montantReelEspeces) ? (float) $this->montantReelEspeces : 0.0;
        $theoriqueEspeces = (float) $totaux['theorique_cash'];
        $ecartEspeces = round($reelEspeces - $theoriqueEspeces, 2);

        $reelWave = is_numeric($this->montantReelWave) ? (float) $this->montantReelWave : 0.0;
        $theoriqueWave = (float) $totaux['theorique_wave'];
        $ecartWave = round($reelWave - $theoriqueWave, 2);

        $this->resumeCloture = array_merge($totaux, [
            'reference'         => $session->reference,
            'ouvert_le'         => $session->ouvert_le?->format('d/m/Y à H:i'),
            'duree'             => $session->duree(),
            'fond_initial'      => (float) $session->fond_caisse_ouverture,
            'fond_wave_initial' => (float) ($session->fond_caisse_wave_ouverture ?? 0),
            'montant_reel'      => $reelEspeces,
            'ecart'             => $ecartEspeces,
            'montant_reel_wave' => $reelWave,
            'ecart_wave'        => $ecartWave,
            'achats_cash'       => $totaux['achats_cash'] ?? 0,
            'achats_wave'       => $totaux['achats_wave'] ?? 0,
            'total_achats'      => $totaux['total_achats'] ?? 0,
        ]);
    }

    public function cloturerSession(): void
    {
        $this->validate([
            'montantReelEspeces' => 'required|numeric|min:0',
            'montantReelWave'    => 'required|numeric|min:0',
            'notesCloture'       => 'nullable|string|max:500',
        ], [
            'montantReelEspeces.required' => 'Le comptage physique des espèces est obligatoire.',
            'montantReelEspeces.min'      => 'Le montant espèces ne peut pas être négatif.',
            'montantReelWave.required'    => 'Le solde réel Wave est obligatoire.',
            'montantReelWave.min'         => 'Le solde Wave ne peut pas être négatif.',
        ]);

        $session = SessionCaisse::find($this->sessionCaisseId);
        if (! $session || ! $session->estOuverte()) {
            session()->flash('pos_error', 'Session déjà fermée ou introuvable.');
            $this->afficherModalCloture = false;
            $this->verifierSession();
            return;
        }

        $totaux = $session->totaux_en_direct;
        $reelEspeces = (float) $this->montantReelEspeces;
        $ecartEspeces = round($reelEspeces - $totaux['theorique_cash'], 2);

        $reelWave = (float) $this->montantReelWave;
        $ecartWave = round($reelWave - $totaux['theorique_wave'], 2);

        $tz  = config('app.timezone', 'Africa/Dakar');
        $session->update([
            'ferme_le'                  => now($tz),
            'statut'                    => 'fermee',
            'total_ventes_cash'         => $totaux['ventes_cash'],
            'total_ventes_wave'         => $totaux['ventes_wave'],
            'total_ventes_om'           => $totaux['ventes_om'],
            'total_ventes_carte'        => $totaux['ventes_carte'],
            'total_ventes'              => $totaux['total_ventes'],
            'total_monnaie_rendue_wave' => $totaux['monnaie_rendue_wave'],
            'total_depenses_cash'       => $totaux['depenses_cash'],
            'total_depenses'            => $totaux['total_depenses'],
            'total_achats_cash'         => $totaux['achats_cash'],
            'total_achats_wave'         => $totaux['achats_wave'],
            'total_achats'              => $totaux['total_achats'],
            'solde_theorique_especes'   => $totaux['theorique_cash'],
            'solde_theorique_wave'      => $totaux['theorique_wave'],
            'montant_reel_especes'      => $reelEspeces,
            'montant_reel_wave'         => $reelWave,
            'ecart'                     => $ecartEspeces,
            'ecart_wave'                => $ecartWave,
            'notes_fermeture'           => $this->notesCloture ?: null,
        ]);

        $this->derniereSessionClotureeId = $session->id;
        $this->sessionCaisseId = null;
        $this->afficherModalCloture = false;
        $this->afficherModalSuccesCloture = true;
    }

    public function processPayment($montantSaisi = null, $monnaieWaveSaisie = null): void
    {
        if (empty($this->panier)) return;
        
        if (! $this->sessionCaisseId) {
            $this->verifierSession();
            if (! $this->sessionCaisseId) {
                session()->flash('pos_error', 'Veuillez ouvrir votre session de caisse avant d\'encaisser !');
                $this->afficherModalPaiement = false;
                $this->afficherModalOuverture = true;
                return;
            }
        }

        if ($montantSaisi !== null && (float) $montantSaisi > 0) {
            $this->montantRecu = (float) $montantSaisi;
        }
        if ($this->modePaiement !== 'a_regler' && $this->montantRecu < $this->total) {
            $this->addError('montantRecu', 'Montant insuffisant !'); return;
        }

        // Calcul de la monnaie rendue via Wave
        $monnaieTotale = max(0, round($this->montantRecu - $this->total, 2));
        $monnaieWave = 0.0;
        if ($monnaieTotale > 0) {
            if ($monnaieWaveSaisie !== null) {
                $monnaieWave = min($monnaieTotale, max(0, (float) $monnaieWaveSaisie));
            } elseif ($this->canalMonnaie === 'wave') {
                $monnaieWave = $monnaieTotale;
            } elseif ($this->canalMonnaie === 'mixte') {
                $monnaieWave = min($monnaieTotale, max(0, (float) $this->partMonnaieWave));
            }
        }

        try {
            $venteService = app(VenteService::class);
            $clientId = $this->clientSelectionneId;
            if (! $clientId && $this->utiliserClientRapide && $this->clientRapideNom) {
                $client   = Client::create(['nom' => $this->clientRapideNom, 'telephone' => $this->clientRapideTel ?: null]);
                $clientId = $client->id;
            }
            $vente = $venteService->creerVente(
                panier: $this->panier,
                paiement: [
                    'mode_paiement'          => $this->modePaiement,
                    'montant_recu'           => $this->montantRecu,
                    'monnaie_rendue_wave'    => $monnaieWave,
                    'remise'                 => $this->remise,
                    'notes'                  => $this->notes,
                    'date_vente'             => $this->dateVente ?: now()->toDateString(),
                    'session_caisse_id'      => $this->sessionCaisseId,
                ],
                clientId: $clientId
            );
            $this->derniereVenteId          = $vente->id;
            $this->derniereReference        = $vente->reference;
            $this->monnaieRendue            = (float) $vente->monnaie_rendue;
            $this->monnaieRendueEspeces     = (float) $vente->monnaie_rendue_especes;
            $this->monnaieRendueWave        = (float) $vente->monnaie_rendue_wave;
            $this->afficherModalPaiement    = false;
            $this->afficherModalSucces      = true;
            $this->clearCart();
            $this->loadProducts();

            $this->dispatch('imprimer-ticket', url: route('caisse.ticket', $vente->id) . '?auto=1');
        } catch (\Exception $e) {
            $this->erreurPaiement = $e->getMessage();
        }
    }

    public function render()
    {
        $categories = Categorie::where('est_active', true)
            ->whereHas('produits', fn($q) => $q->where('est_actif', true)->where(fn($sq) => $sq->where('stock', '>', 0)->orWhere('sur_commande', true)))
            ->get(['id', 'nom']);

        $sessionActive = $this->sessionCaisseId ? SessionCaisse::find($this->sessionCaisseId) : null;

        return view('livewire.caisse.point-of-sale', compact('categories', 'sessionActive'));
    }
}
