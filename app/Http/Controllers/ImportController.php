<?php

namespace App\Http\Controllers;

use App\Imports\ProduitsImport;
use App\Imports\VentesImport;
use App\Imports\AchatsImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Writers\CsvWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    private array $templates = [
        'produits' => [
            'filename' => 'template_produits.xlsx',
            'headers'  => ['nom','categorie','fournisseur','prix_achat','prix_vente','stock','seuil_alerte','unite','marque','description'],
            'example'  => ['Samsung Galaxy A55','Téléphones','Samsung Sénégal','180000','250000','10','3','unité','Samsung','Smartphone 6.6 pouces'],
            'notes'    => [
                'nom'           => 'Obligatoire',
                'categorie'     => 'Obligatoire — créée automatiquement si inexistante',
                'fournisseur'   => 'Optionnel',
                'prix_achat'    => 'En FCFA, sans espace',
                'prix_vente'    => 'Obligatoire — en FCFA',
                'stock'         => 'Quantité initiale (défaut : 0)',
                'seuil_alerte'  => 'Alerte stock bas (défaut : 5)',
                'unite'         => 'Ex : unité, paire, lot, kg',
                'marque'        => 'Optionnel',
                'description'   => 'Optionnel',
            ],
        ],
        'ventes' => [
            'filename' => 'template_ventes.xlsx',
            'headers'  => ['date','client','telephone_client','mode_paiement','produit','quantite','prix_unitaire','remise'],
            'example'  => ['21/04/2026','Moussa Diallo','771234567','Wave','Samsung Galaxy A55','1','250000','0'],
            'notes'    => [
                'date'             => 'Obligatoire — format JJ/MM/AAAA',
                'client'           => 'Optionnel — créé automatiquement si nouveau',
                'telephone_client' => 'Optionnel',
                'mode_paiement'    => 'cash, wave, om, mix, card',
                'produit'          => 'Obligatoire — doit exister dans le système',
                'quantite'         => 'Défaut : 1',
                'prix_unitaire'    => 'Obligatoire — en FCFA',
                'remise'           => 'Remise en FCFA (défaut : 0)',
            ],
        ],
        'achats' => [
            'filename' => 'template_achats.xlsx',
            'headers'  => ['date','fournisseur','produit','quantite','prix_unitaire','montant_paye','mode_paiement','notes'],
            'example'  => ['21/04/2026','Samsung Sénégal','Samsung Galaxy A55','5','180000','900000','wave',''],
            'notes'    => [
                'date'          => 'Obligatoire — format JJ/MM/AAAA',
                'fournisseur'   => 'Obligatoire — créé automatiquement si nouveau',
                'produit'       => 'Obligatoire — doit exister dans le système',
                'quantite'      => 'Obligatoire',
                'prix_unitaire' => 'Obligatoire — en FCFA',
                'montant_paye'  => 'Optionnel — total si vide',
                'mode_paiement' => 'cash, wave, om, mix, card',
                'notes'         => 'Optionnel',
            ],
        ],
    ];

    public function template(string $type)
    {
        if (!isset($this->templates[$type])) abort(404);

        $tpl        = $this->templates[$type];
        $spreadsheet = new Spreadsheet();
        $sheet      = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Données');

        $primaryColor = 'FF1E6FBF';
        $lightColor   = 'FFE8F1FB';

        // En-têtes
        foreach ($tpl['headers'] as $col => $header) {
            $cell = $this->colLetter($col) . '1';
            $sheet->setCellValue($cell, strtoupper($header));
            $sheet->getStyle($cell)->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $primaryColor]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getColumnDimension($this->colLetter($col))->setWidth(20);
        }

        // Ligne exemple
        foreach ($tpl['example'] as $col => $value) {
            $cell = $this->colLetter($col) . '2';
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $lightColor]],
                'font' => ['italic' => true, 'color' => ['argb' => 'FF555555']],
            ]);
        }

        // Feuille notes
        $notesSheet = $spreadsheet->createSheet();
        $notesSheet->setTitle('Instructions');
        $notesSheet->setCellValue('A1', 'Colonne');
        $notesSheet->setCellValue('B1', 'Description');
        $notesSheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $primaryColor]],
        ]);
        $notesSheet->getColumnDimension('A')->setWidth(22);
        $notesSheet->getColumnDimension('B')->setWidth(55);

        $row = 2;
        foreach ($tpl['notes'] as $col => $note) {
            $notesSheet->setCellValue("A{$row}", $col);
            $notesSheet->setCellValue("B{$row}", $note);
            if ($row % 2 === 0) {
                $notesSheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $lightColor]],
                ]);
            }
            $row++;
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $filename = $tpl['filename'];

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function colLetter(int $index): string
    {
        return chr(65 + $index);
    }
}
