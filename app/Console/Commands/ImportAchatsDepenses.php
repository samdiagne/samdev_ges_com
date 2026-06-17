<?php

namespace App\Console\Commands;

use App\Models\Achat;
use App\Models\AchatLigne;
use App\Models\Depense;
use App\Models\DepenseCategorie;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportAchatsDepenses extends Command
{
    protected $signature = 'import:achats-depenses {fichier : Chemin vers le fichier Excel}';

    protected $description = 'Importe les achats et dépenses depuis un fichier Excel MAFFOOD';

    public function handle(): int
    {
        $fichier = $this->argument('fichier');

        if (! file_exists($fichier)) {
            $this->error("Fichier introuvable : {$fichier}");
            return self::FAILURE;
        }

        $spreadsheet = IOFactory::load($fichier);
        $rows = $spreadsheet->getActiveSheet()->toArray();

        $admin = User::role('admin')->first();
        if (! $admin) {
            $this->error('Aucun utilisateur admin trouvé.');
            return self::FAILURE;
        }

        DB::beginTransaction();

        try {
            $achatsCount = $this->importerAchats($rows, $admin->id);
            $depensesCount = $this->importerDepenses($rows, $admin->id);

            DB::commit();

            $this->info("Import terminé : {$achatsCount} lignes d'achat, {$depensesCount} dépenses.");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Erreur : {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    private function importerAchats(array $rows, int $userId): int
    {
        $lignesParDate = [];

        foreach (array_slice($rows, 2) as $row) {
            $libelle = trim($row[0] ?? '');
            $qteXpu = trim($row[1] ?? '');
            $total = $row[2] ?? null;
            $date = trim($row[3] ?? '');

            if ($libelle === '' || $date === '') {
                continue;
            }

            $dateCarbon = $this->parseDate($date);
            if (! $dateCarbon) {
                continue;
            }

            $dateKey = $dateCarbon->toDateString();

            [$quantite, $prixUnitaire] = $this->parseQuantitePu($qteXpu, $total);

            $lignesParDate[$dateKey][] = [
                'description' => $libelle,
                'quantite' => $quantite,
                'prix_unitaire' => $prixUnitaire,
                'total' => floatval($total),
            ];
        }

        $totalLignes = 0;

        foreach ($lignesParDate as $dateKey => $lignes) {
            $montantTotal = array_sum(array_column($lignes, 'total'));

            $achat = Achat::create([
                'reference' => $this->genererReference(),
                'fournisseur_id' => null,
                'user_id' => $userId,
                'statut' => 'recu',
                'montant_total' => $montantTotal,
                'montant_paye' => $montantTotal,
                'montant_du' => 0,
                'mode_paiement' => 'cash',
                'date_achat' => $dateKey,
            ]);

            foreach ($lignes as $ligne) {
                AchatLigne::create([
                    'achat_id' => $achat->id,
                    'description' => $ligne['description'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'quantite' => $ligne['quantite'],
                    'total' => $ligne['total'],
                ]);
                $totalLignes++;
            }
        }

        return $totalLignes;
    }

    private function importerDepenses(array $rows, int $userId): int
    {
        $count = 0;

        foreach (array_slice($rows, 2) as $row) {
            $libelle = trim($row[6] ?? '');
            $categorie = trim($row[7] ?? '');
            $montant = $row[8] ?? null;
            $date = trim($row[9] ?? '');

            if ($libelle === '' || $montant === null || $date === '') {
                continue;
            }

            $dateCarbon = $this->parseDate($date);
            if (! $dateCarbon) {
                continue;
            }

            $cat = DepenseCategorie::firstOrCreate(
                ['nom' => mb_strtoupper($categorie)],
            );

            Depense::create([
                'depense_categorie_id' => $cat->id,
                'user_id' => $userId,
                'description' => $libelle,
                'montant' => floatval($montant),
                'date_depense' => $dateCarbon,
                'mode_paiement' => 'cash',
            ]);

            $count++;
        }

        return $count;
    }

    private function parseQuantitePu(string $qteXpu, $total): array
    {
        $qteXpu = str_replace(' ', '', mb_strtolower($qteXpu));
        $qteXpu = str_replace(',', '.', $qteXpu);

        if (preg_match('/^([\d.]+)\s*[x×*]\s*([\d.]+)$/', $qteXpu, $m)) {
            return [floatval($m[1]), floatval($m[2])];
        }

        return [1, floatval($total)];
    }

    private function parseDate(string $date): ?Carbon
    {
        try {
            return Carbon::createFromFormat('n/j/Y', $date);
        } catch (\Throwable) {
            try {
                return Carbon::parse($date);
            } catch (\Throwable) {
                return null;
            }
        }
    }

    private function genererReference(): string
    {
        $dernier = Achat::withTrashed()->latest()->first();
        $num = $dernier ? intval(substr($dernier->reference, -4)) + 1 : 1;

        return 'ACH-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
