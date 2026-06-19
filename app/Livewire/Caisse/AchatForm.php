<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use App\Models\Achat;
use App\Models\AchatLigne;
use App\Models\Fournisseur;
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
            'quantity'    => $l->quantite,
            'unit'        => $l->unite ?? '',
            'unit_price'  => (float) $l->prix_unitaire,
        ])->toArray();

        $this->recalcTotals();
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

        DB::transaction(function () {
            $data = [
                'fournisseur_id' => $this->fournisseur_id,
                'user_id' => Auth::id(),
                'statut'         => $this->statut,
                'montant_total'  => $this->montantTotal,
                'montant_paye'   => $this->montant_paye ?? 0,
                'montant_du'     => max(0, $this->montantDu),
                'mode_paiement'  => $this->mode_paiement ?: null,
                'date_achat'     => $this->date_achat,
                'notes'          => $this->notes ?: null,
            ];

            if ($this->isEditing) {
                $achat = Achat::findOrFail($this->achatId);
                $achat->update($data);
                $achat->lignes()->delete();
            } else {
                $data['reference'] = $this->generateRef();
                $achat             = Achat::create($data);
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

        session()->flash('success', $this->isEditing
            ? 'Achat modifié avec succès !'
            : 'Achat enregistré avec succès !'
        );

        $this->redirect(route('caisse.achats.index'));
    }

    private function generateRef(): string
    {
        $dernier = Achat::withTrashed()->latest()->first();
        $num     = $dernier ? intval(substr($dernier->reference, -4)) + 1 : 1;
        return 'ACH-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.caisse.achat-form', [
            'fournisseurs' => Fournisseur::where('est_actif', true)->orderBy('nom')->get(),
        ]);
    }
}