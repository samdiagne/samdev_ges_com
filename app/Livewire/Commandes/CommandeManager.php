<?php

namespace App\Livewire\Commandes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Commande;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Models\Facture;
use App\Models\Client;
use App\Models\Parametre;
use App\Services\StockService;
use App\Mail\CommandeLivreeMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CommandeManager extends Component
{
    use WithPagination;

    public string $search        = '';
    public string $filtreStatut  = '';
    public ?int   $voirId        = null;

    public function updatingSearch(): void { $this->resetPage(); }

    public function view(int $id): void
    {
        $this->voirId = $id;
    }

    public function updateStatus(int $id, string $statut): void
    {
        $commande = Commande::findOrFail($id);
        $commande->update(['statut' => $statut]);

        if ($statut === 'livree' && $commande->email_client) {
            try {
                Mail::to($commande->email_client)->send(new CommandeLivreeMail($commande));
            } catch (\Exception $e) {
                Log::error('Mail livraison : ' . $e->getMessage());
            }
        }

        // Conversion auto si payée + livrée
        if ($statut === 'livree' && $commande->statut_paiement === 'paye' && ! $commande->vente_id) {
            $this->convertirEnVente($id);
            return;
        }

        session()->flash('success', 'Statut mis à jour !');
    }

    public function updatePaymentStatus(int $id, string $statutPaiement): void
    {
        $commande = Commande::findOrFail($id);
        $commande->update(['statut_paiement' => $statutPaiement]);

        if ($statutPaiement === 'paye' && $commande->statut === 'livree' && ! $commande->vente_id) {
            $this->convertirEnVente($id);
            return;
        }

        session()->flash('success', 'Statut paiement mis à jour !');
    }

    public function convertToSale(int $commandeId): void
    {
        $commande = Commande::with('lignes.produit')->findOrFail($commandeId);

        if ($commande->vente_id) {
            session()->flash('info', 'Cette commande a déjà été convertie en vente.');
            return;
        }

        if ($commande->statut !== 'livree' || $commande->statut_paiement !== 'paye') {
            session()->flash('error', 'La commande doit être livrée et payée pour être convertie.');
            return;
        }

        try {
            DB::transaction(function () use ($commande) {
                $parametres = Parametre::first();
                $stockSvc   = app(StockService::class);

                $client = Client::firstOrCreate(
                    ['telephone' => $commande->telephone_client],
                    ['nom' => $commande->nom_client, 'email' => $commande->email_client, 'telephone' => $commande->telephone_client]
                );

                $vente = Vente::create([
                    'reference'      => $this->generateRefVente(),
                    'client_id'      => $client->id,
                    'user_id' => auth()->id(),
                    'source'         => 'en_ligne',
                    'statut'         => 'completee',
                    'total'          => $commande->total,
                    'remise'         => 0,
                    'montant_recu'   => $commande->total,
                    'monnaie_rendue' => 0,
                    'mode_paiement'  => $commande->mode_paiement,
                    'notes'          => 'Commande en ligne ' . $commande->reference,
                    'vendu_le'       => $commande->updated_at,
                ]);

                foreach ($commande->lignes as $ligne) {
                    VenteLigne::create([
                        'vente_id'      => $vente->id,
                        'produit_id'    => $ligne->produit_id,
                        'nom_produit'   => $ligne->nom_produit,
                        'prix_unitaire' => $ligne->prix_unitaire,
                        'quantite'      => $ligne->quantite,
                        'remise'        => 0,
                        'total'         => $ligne->total,
                    ]);

                    if ($ligne->produit) {
                        $stockSvc->decrementerStock(
                            $ligne->produit, $ligne->quantite, 'vente', $vente->id,
                            'Vente en ligne #' . $commande->reference
                        );
                    }
                }

                Facture::create([
                    'vente_id'       => $vente->id,
                    'client_id'      => $client->id,
                    'numero_facture' => $this->generateInvoiceNumber($parametres),
                    'statut'         => 'emise',
                    'total'          => $commande->total,
                    'emise_le'       => now(),
                ]);

                $parametres?->increment('compteur_facture');
                $commande->update(['vente_id' => $vente->id]);
            });

            session()->flash('success', 'Commande convertie en vente et facture générée !');
        } catch (\Exception $e) {
            Log::error('Conversion commande→vente : ' . $e->getMessage());
            session()->flash('error', 'Erreur lors de la conversion : ' . $e->getMessage());
        }
    }

    private function generateRefVente(): string
    {
        $derniere = Vente::withTrashed()->latest()->first();
        $num      = $derniere ? intval(substr($derniere->reference, -4)) + 1 : 1;
        return 'VNT-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    private function generateInvoiceNumber(?Parametre $parametres): string
    {
        $prefixe  = $parametres?->prefixe_facture ?? 'FAC';
        $compteur = $parametres?->compteur_facture ?? 1;
        return $prefixe . '-' . date('Y') . '-' . str_pad($compteur, 4, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        $commandes = Commande::with(['lignes', 'vente'])
            ->when($this->search, fn($q) =>
                $q->where('reference', 'like', '%' . $this->search . '%')
                  ->orWhere('nom_client', 'like', '%' . $this->search . '%')
                  ->orWhere('telephone_client', 'like', '%' . $this->search . '%')
            )
            ->when($this->filtreStatut, fn($q) =>
                $q->where('statut', $this->filtreStatut)
            )
            ->latest()
            ->paginate(15);

        $stats = [
            'total'         => Commande::count(),
            'en_attente'    => Commande::where('statut', 'en_attente')->count(),
            'confirmee'     => Commande::where('statut', 'confirmee')->count(),
            'en_preparation'=> Commande::where('statut', 'en_preparation')->count(),
            'livree'        => Commande::where('statut', 'livree')->count(),
        ];

        $voir = $this->voirId
            ? Commande::with(['lignes', 'vente.facture'])->find($this->voirId)
            : null;

        return view('livewire.commandes.commande-manager',
            compact('commandes', 'stats', 'voir'));
    }
}
