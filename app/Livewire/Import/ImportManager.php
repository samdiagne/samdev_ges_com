<?php

namespace App\Livewire\Import;

use App\Imports\ProduitsImport;
use App\Imports\VentesImport;
use App\Imports\AchatsImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ImportManager extends Component
{
    use WithFileUploads;

    public string  $activeTab = 'produits';
    public         $file      = null;
    public bool    $done      = false;
    public array   $result    = [];
    public bool    $loading   = false;

    public function updatedActiveTab(): void
    {
        $this->reset('file', 'done', 'result');
        $this->resetErrorBag();
    }

    public function import(): void
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ], [
            'file.required' => 'Veuillez sélectionner un fichier.',
            'file.mimes'    => 'Seuls les fichiers Excel (.xlsx, .xls) sont acceptés.',
            'file.max'      => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        $importer = match($this->activeTab) {
            'produits' => new ProduitsImport(),
            'ventes'   => new VentesImport(),
            'achats'   => new AchatsImport(),
        };

        try {
            Excel::import($importer, $this->file->getRealPath());

            $this->result = match($this->activeTab) {
                'produits' => [
                    'created' => $importer->created,
                    'updated' => $importer->updated,
                    'errors'  => $importer->errors,
                ],
                default => [
                    'created' => $importer->created,
                    'errors'  => $importer->errors,
                ],
            };

            $this->done = true;
            $this->file = null;

        } catch (\Exception $e) {
            $this->addError('file', 'Erreur lors de la lecture du fichier : ' . $e->getMessage());
        }
    }

    public function reset2(): void
    {
        $this->reset('file', 'done', 'result');
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.import.import-manager');
    }
}
