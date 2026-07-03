<?php

namespace App\Livewire\Parametres;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Parametre;
use Illuminate\Support\Facades\Storage;

class ParametreManager extends Component
{
    use WithFileUploads;

    public string  $nom             = '';
    public string  $ninea           = '';
    public string  $rc              = '';
    public string  $telephone       = '';
    public string  $whatsapp        = '';
    public string  $email           = '';
    public string  $adresse         = '';
    public string  $facebook        = '';
    public string  $instagram       = '';
    public string  $tiktok          = '';
    public string  $lien_whatsapp   = '';
    public string  $devise          = 'FCFA';
    public string  $prefixe_facture = 'FAC';

    public         $logo            = null;
    public ?string $logoActuel      = null;
    public         $imageHero       = null;
    public ?string $imageHeroActuelle = null;
    public         $cachet          = null;
    public ?string $cachetActuel    = null;

    public ?string $signatureData    = null;
    public ?string $signatureActuelle = null;
    public bool    $signatureEffacee  = false;

    public string  $ongletActif = 'entreprise';
    public bool    $sauvegarde  = false;

    protected function rules(): array
    {
        return [
            'nom'             => 'required|string|min:2|max:255',
            'ninea'           => 'nullable|string|max:50',
            'rc'              => 'nullable|string|max:50',
            'telephone'       => 'nullable|string|max:100',
            'whatsapp'        => 'nullable|string|max:100',
            'email'           => 'nullable|email|max:255',
            'adresse'         => 'nullable|string|max:500',
            'prefixe_facture' => 'required|string|max:10',
            'logo'            => 'nullable|image|max:2048',
            'cachet'          => 'nullable|image|max:2048',
        ];
    }

    public function mount(): void
    {
        $parametres = Parametre::first();
        if (! $parametres) return;

        $this->nom              = $parametres->nom;
        $this->ninea            = $parametres->ninea ?? '';
        $this->rc               = $parametres->rc ?? '';
        $this->telephone        = $parametres->telephone ?? '';
        $this->whatsapp         = $parametres->whatsapp ?? '';
        $this->email            = $parametres->email ?? '';
        $this->adresse          = $parametres->adresse ?? '';
        $this->facebook         = $parametres->facebook ?? '';
        $this->instagram        = $parametres->instagram ?? '';
        $this->tiktok           = $parametres->tiktok ?? '';
        $this->lien_whatsapp    = $parametres->lien_whatsapp ?? '';
        $this->devise           = $parametres->devise ?? 'FCFA';
        $this->prefixe_facture  = $parametres->prefixe_facture ?? 'FAC';
        $this->logoActuel       = $parametres->logo;
        $this->imageHeroActuelle = $parametres->image_hero;
        $this->cachetActuel     = $parametres->cachet;
        $this->signatureActuelle = $parametres->signature;
    }

    public function saveRestaurant(): void
    {
        $this->validate([
            'nom'             => 'required|string|min:2|max:255',
            'ninea'           => 'nullable|string|max:50',
            'rc'              => 'nullable|string|max:50',
            'telephone'       => 'nullable|string|max:100',
            'whatsapp'        => 'nullable|string|max:100',
            'email'           => 'nullable|email|max:255',
            'adresse'         => 'nullable|string|max:500',
            'facebook'        => 'nullable|string|max:255',
            'instagram'       => 'nullable|string|max:255',
            'tiktok'          => 'nullable|string|max:255',
            'lien_whatsapp'   => 'nullable|string|max:255',
            'prefixe_facture' => 'required|string|max:10',
            'logo'            => 'nullable|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'imageHero'       => 'nullable|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $logoPath = $this->logoActuel;
        if ($this->logo) {
            if ($this->logoActuel && Storage::disk('public')->exists($this->logoActuel)) {
                Storage::disk('public')->delete($this->logoActuel);
            }
            $logoPath = $this->logo->store('parametres', 'public');
            $this->logoActuel = $logoPath;
            $this->logo = null;
        }

        $imageHeroPath = $this->imageHeroActuelle;
        if ($this->imageHero) {
            if ($this->imageHeroActuelle && Storage::disk('public')->exists($this->imageHeroActuelle)) {
                Storage::disk('public')->delete($this->imageHeroActuelle);
            }
            $imageHeroPath = $this->imageHero->store('parametres', 'public');
            $this->imageHeroActuelle = $imageHeroPath;
            $this->imageHero = null;
        }

        Parametre::updateOrCreate(['id' => 1], [
            'nom'             => $this->nom,
            'ninea'           => $this->ninea ?: null,
            'rc'              => $this->rc ?: null,
            'telephone'       => $this->telephone ?: null,
            'whatsapp'        => $this->whatsapp ?: null,
            'email'           => $this->email ?: null,
            'adresse'         => $this->adresse ?: null,
            'facebook'        => $this->facebook ?: null,
            'instagram'       => $this->instagram ?: null,
            'tiktok'          => $this->tiktok ?: null,
            'lien_whatsapp'   => $this->lien_whatsapp ?: null,
            'devise'          => $this->devise,
            'prefixe_facture' => $this->prefixe_facture,
            'logo'            => $logoPath,
            'image_hero'      => $imageHeroPath,
        ]);

        $this->showSaved();
    }

    public function saveSignature(): void
    {
        $parametres = Parametre::first();
        $cachetPath = $this->cachetActuel;

        if ($this->cachet) {
            $this->validate(['cachet' => 'nullable|image|max:2048']);
            if ($this->cachetActuel) Storage::disk('public')->delete($this->cachetActuel);
            $cachetPath = $this->cachet->store('parametres', 'public');
        }

        $signaturePath = $this->signatureActuelle;
        if ($this->signatureData) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->signatureData));
            $filename  = 'parametres/signature_' . time() . '.png';
            Storage::disk('public')->put($filename, $imageData);
            $signaturePath = $filename;
        }
        if ($this->signatureEffacee) {
            if ($this->signatureActuelle) Storage::disk('public')->delete($this->signatureActuelle);
            $signaturePath = null;
        }

        $parametres?->update(['cachet' => $cachetPath, 'signature' => $signaturePath]);

        $this->cachetActuel      = $cachetPath;
        $this->signatureActuelle = $signaturePath;
        $this->cachet            = null;
        $this->signatureData     = null;
        $this->signatureEffacee  = false;

        $this->showSaved();
    }

    public function clearSignature(): void
    {
        $this->signatureEffacee = true;
        $this->signatureData    = null;
        $this->dispatch('clearSignaturePad');
    }

    private function showSaved(): void
    {
        $this->sauvegarde = true;
        $this->dispatch('flash-saved');
    }

    public function render()
    {
        return view('livewire.parametres.parametre-manager', [
            'parametres' => Parametre::first(),
        ]);
    }
}
