<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use App\Models\Achat;
use App\Models\AchatLigne;
use App\Models\AchatReglement;
use App\Models\Fournisseur;
use App\Models\SessionCaisse;
use App\Services\AchatService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AchatForm extends Component
{
    public bool   $isEditing   = false;
    public ?int   $achatId     = null;

    public ?int   $fournisseur_id   = null;
    public string $date_achat       = '';
    public string $statut           = 'recu';
    public string $mode_paiement    = 'cash';
    public ?float $montant_paye     = 0;
    public string $notes            = '';

    public array  $lignes           = [];
    public float  $montantTotal     = 0;
    public float  $montantDu        = 0;
    public float  $detteFournisseur = 0;

    protected function rules(): array
    {
        return [
            'fournisseur_id'            => 'nullable|exists:fournisseurs,id',
            'date_achat'                => 'required|date',
            'statut'                    => 'required|in:commande,recu,partiel,annule',
            'mode_paiement'             => 'nullable|string',
            'montant_paye'              => 'nullable|numeric|min:0',
            'lignes'                    => 'required|array|min:1',
            'lignes.*.description'      => 'required|string|max:255',
            'lignes.*.quantity'         => 'required|integer|min:1',
            'lignes.*.unit_price'       => 'required|numeric|min:0',
        ];
    }

    protected $messages = [
        'date_achat.required'           => 'La date d\'achat est obligatoire.',
        'lignes.required'               => 'Ajoutez au moins une ligne.',
        'lignes.min'                    => 'Ajoutez au moins une ligne.',
        'lignes.*.description.required' => 'La description est obligatoire.',
        'lignes.*.quantity.required'    => 'La quantité est obligatoire.',
        'lignes.*.unit_price.required'  => 'Le montant est obligatoire.',
    ];

    public function mount(?int $achatId = null): void
    {
        $this->date_achat = now()->format('Y-m-d');

        if ($achatId) {
            $this->isEditing = true;
            $this->achatId   = $achatId;
            $this->chargerAchat($achatId);
        } else {
            $this->addLine();
        }
    }

    private function chargerAchat(int $id): void
    {
        $achat = Achat::with('lignes')->findOrFail($id);

        $this->fournisseur_id = $achat->fournisseur_id;
        $this->date_achat     = $achat->date_achat->format('Y-m-d');
        $this->statut         = $achat->statut;
        $this->mode_paiement  = $achat->mode_paiement ?? 'cash';
        $this->montant_paye   = (float) $achat->montant_paye;
        $this->notes          = $achat->notes ?? '';

        $this->lignes = $achat->lignes->map(fn($l) => [
            'description' => $l->description,
            'quantity'    => (int) $l->quantite,
            'unit'        => $l->unite ?? '',
            'unit_price'  => (float) $l->prix_unitaire,
        ])->toArray();

        $this->recalcTotals();
        $this->rafraichirDetteFournisseur();
    }

    public function updatedFournisseurId(): void
    {
        $this->rafraichirDetteFournisseur();
    }

    private function rafraichirDetteFournisseur(): void
    {
        if ($this->fournisseur_id) {
            $this->detteFournisseur = app(AchatService::class)
                ->soldeDuFournisseur((int) $this->fournisseur_id, $this->isEditing ? $this->achatId : null);
        } else {
            $this->detteFournisseur = 0;
        }
    }

    public function addLine(): void
    {
        $this->lignes[] = [
            'description' => '',
            'quantity'    => 1,
            'unit'        => '',
            'unit_price'  => 0,
        ];
    }

    public function updateLine(int $index, string $champ, $valeur): void
    {
        if ($champ === 'quantity')    $this->lignes[$index]['quantity']    = max(1, (int) $valeur);
        if ($champ === 'unit_price')  $this->lignes[$index]['unit_price']  = max(0, (float) $valeur);
        if ($champ === 'description') $this->lignes[$index]['description'] = $valeur;
        if ($champ === 'unit')        $this->lignes[$index]['unit']        = $valeur;
        $this->recalcTotals();
    }

    public function removeLine(int $index): void
    {
        array_splice($this->lignes, $index, 1);
        if (empty($this->lignes)) $this->addLine();
        $this->lignes = array_values($this->lignes);
        $this->recalcTotals();
    }

    public function recalcTotals(): void
    {
        $this->montantTotal = round(array_sum(array_map(
            fn($l) => ($l['unit_price'] ?? 0) * ($l['quantity'] ?? 1),
            $this->lignes
        )), 2);
        $this->montantDu = round($this->montantTotal - ($this->montant_paye ?? 0), 2);
    }

    public function updatedMontantPaye(): void { $this->recalcTotals(); }

    public function save(): void
    {
        $this->validate();
        $this->rafraichirDetteFournisseur();

        $montantPayeSaisi = max(0, (float) ($this->montant_paye ?? 0));
        $maxAutorise = $this->montantTotal + (!$this->isEditing && $this->fournisseur_id ? $this->detteFournisseur : 0);

        if ($montantPayeSaisi > $maxAutorise + 0.01) {
            $this->addError(
                'montant_paye',
                'Le montant versé (' . number_format($montantPayeSaisi, 0, ',', ' ') . ' F) dépasse le total à payer (' . number_format($maxAutorise, 0, ',', ' ') . ' F).'
            );
            return;
        }

        $resultatFifo = null;

        DB::transaction(function () use ($montantPayeSaisi, &$resultatFifo) {
            // Récupérer la session de caisse active du caissier connecté (ou toute session active)
            $sessionActive = SessionCaisse::where('user_id', Auth::id())
                ->where('statut', 'ouverte')
                ->latest('ouvert_le')
                ->first()
                ?? SessionCaisse::where('statut', 'ouverte')->latest('ouvert_le')->first();

            $payeSurCetAchat = min($montantPayeSaisi, $this->montantTotal);
            $montantDuInitial = max(0, round($this->montantTotal - $payeSurCetAchat, 2));

            $data = [
                'fournisseur_id'   => $this->fournisseur_id ?: null,
                'user_id'          => Auth::id(),
                'session_caisse_id'=> $sessionActive?->id,
                'statut'           => $this->statut,
                'montant_total'    => $this->montantTotal,
                'montant_paye'     => $payeSurCetAchat,
                'montant_du'       => $montantDuInitial,
                'mode_paiement'    => $this->mode_paiement ?: null,
                'date_achat'       => $this->date_achat,
                'notes'            => $this->notes ?: null,
            ];

            if ($this->isEditing) {
                $achat = Achat::with('reglements')->findOrFail($this->achatId);
                if ($achat->session_caisse_id) {
                    $data['session_caisse_id'] = $achat->session_caisse_id;
                }
                $achat->update($data);
                $achat->lignes()->delete();

                // Gestion des règlements en modification
                $nbReglements = $achat->reglements()->count();
                if ($nbReglements === 0 && $payeSurCetAchat > 0) {
                    AchatReglement::create([
                        'reference'         => AchatReglement::genererReference(),
                        'achat_id'          => $achat->id,
                        'session_caisse_id' => $sessionActive?->id,
                        'user_id'           => Auth::id(),
                        'montant'           => $payeSurCetAchat,
                        'mode_paiement'     => $this->mode_paiement ?: 'cash',
                        'date_reglement'    => now(),
                        'notes'             => 'Paiement initial',
                    ]);
                } elseif ($nbReglements === 1) {
                    $regl = $achat->reglements()->first();
                    if ($payeSurCetAchat > 0) {
                        $regl->update([
                            'montant'       => $payeSurCetAchat,
                            'mode_paiement' => $this->mode_paiement ?: 'cash',
                        ]);
                    } else {
                        $regl->delete();
                    }
                }
                $achat->recalculerPaiement();
            } else {
                $data['reference'] = $this->generateRef();

                // Si versement global supérieur à l'achat du jour avec un fournisseur ayant une dette
                if ($montantPayeSaisi > $this->montantTotal && $this->fournisseur_id && $this->detteFournisseur > 0) {
                    $data['montant_paye'] = 0;
                    $data['montant_du']   = $this->montantTotal;
                    $achat = Achat::create($data);

                    $resultatFifo = app(AchatService::class)->reglerFournisseurFIFO(
                        fournisseurId: (int) $this->fournisseur_id,
                        montantVerse: $montantPayeSaisi,
                        modePaiement: $this->mode_paiement ?: 'cash',
                        session: $sessionActive,
                        notes: "Versement global lors de l'achat {$achat->reference}",
                        prioriteAchatId: $achat->id
                    );
                } else {
                    $achat = Achat::create($data);

                    // Si un paiement a été effectué à la création, créer le règlement correspondant
                    if ($payeSurCetAchat > 0) {
                        AchatReglement::create([
                            'reference'         => AchatReglement::genererReference(),
                            'achat_id'          => $achat->id,
                            'session_caisse_id' => $sessionActive?->id,
                            'user_id'           => Auth::id(),
                            'montant'           => $payeSurCetAchat,
                            'mode_paiement'     => $this->mode_paiement ?: 'cash',
                            'date_reglement'    => now(),
                            'notes'             => 'Paiement initial lors de l\'enregistrement',
                        ]);
                        $achat->recalculerPaiement();
                    }
                }
            }

            foreach ($this->lignes as $ligne) {
                AchatLigne::create([
                    'achat_id'      => $achat->id,
                    'description'   => $ligne['description'],
                    'quantite'      => $ligne['quantity'],
                    'unite'         => $ligne['unit'] ?: null,
                    'prix_unitaire' => $ligne['unit_price'],
                    'total'         => $ligne['unit_price'] * $ligne['quantity'],
                ]);
            }
        });

        if ($resultatFifo && count($resultatFifo['details']) > 1) {
            $nbAnciennes = count($resultatFifo['details']) - 1;
            $surplus = $montantPayeSaisi - $this->montantTotal;
            session()->flash(
                'success',
                'Achat enregistré ! ' . number_format($this->montantTotal, 0, ',', ' ') . ' F ont soldé cet achat et '
                . number_format($surplus, 0, ',', ' ') . " F ont été imputés sur {$nbAnciennes} ancienne(s) facture(s) du fournisseur."
            );
        } else {
            session()->flash('success', $this->isEditing
                ? 'Achat modifié avec succès !'
                : 'Achat enregistré avec succès !'
            );
        }

        $this->redirect(route('caisse.achats.index'));
    }

    private function generateRef(): string
    {
        $annee   = date('Y');
        $dernier = Achat::withTrashed()
            ->where('reference', 'like', "ACH-{$annee}-%")
            ->orderBy('reference', 'desc')
            ->value('reference');
        $num = $dernier ? intval(substr($dernier, -4)) + 1 : 1;
        return 'ACH-' . $annee . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.caisse.achat-form', [
            'fournisseurs' => Fournisseur::where('est_actif', true)->orderBy('nom')->get(),
        ]);
    }
}