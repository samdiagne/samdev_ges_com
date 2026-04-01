<?php

namespace App\Livewire\Parametres;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CompanySetting;
use Illuminate\Support\Facades\Storage;

class ParametreManager extends Component
{
    use WithFileUploads;

    // Infos entreprise
    public string $name           = '';
    public string $ninea          = '';
    public string $rc             = '';
    public string $phone          = '';
    public string $whatsapp       = '';
    public string $email          = '';
    public string $address        = '';
    public string $facebook       = '';
    public string $instagram      = '';
    public string $tiktok         = '';
    public string $currency       = 'FCFA';
    public string $invoice_prefix = 'FAC';
    public float  $tva_rate       = 18;

    // Images
    public        $logo           = null;
    public ?string $currentLogo   = null;
    public        $stamp          = null;
    public ?string $currentStamp  = null;

    // Signature (base64 depuis canvas)
    public ?string $signatureData  = null;
    public ?string $currentSignature = null;
    public bool    $signatureCleared = false;

    // UI
    public string $activeTab = 'entreprise';
    public bool   $saved     = false;

    protected function rules(): array
    {
        return [
            'name'           => 'required|string|min:2|max:255',
            'ninea'          => 'nullable|string|max:50',
            'rc'             => 'nullable|string|max:50',
            'phone'          => 'nullable|string|max:100',
            'whatsapp'       => 'nullable|string|max:100',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:500',
            'tva_rate'       => 'required|numeric|min:0|max:100',
            'invoice_prefix' => 'required|string|max:10',
            'logo'           => 'nullable|image|max:2048',
            'stamp'          => 'nullable|image|max:2048',
        ];
    }

    public function mount(): void
    {
        $settings = CompanySetting::getSettings();

        $this->name             = $settings->name;
        $this->ninea            = $settings->ninea ?? '';
        $this->rc               = $settings->rc ?? '';
        $this->phone            = $settings->phone ?? '';
        $this->whatsapp         = $settings->whatsapp ?? '';
        $this->email            = $settings->email ?? '';
        $this->address          = $settings->address ?? '';
        $this->facebook         = $settings->facebook ?? '';
        $this->instagram        = $settings->instagram ?? '';
        $this->tiktok           = $settings->tiktok ?? '';
        $this->currency         = $settings->currency ?? 'FCFA';
        $this->invoice_prefix   = $settings->invoice_prefix ?? 'FAC';
        $this->tva_rate         = (float) ($settings->tva_rate ?? 18);
        $this->currentLogo      = $settings->logo;
        $this->currentStamp     = $settings->stamp_image;
        $this->currentSignature = $settings->signature_image;
    }

    public function saveEntreprise(): void
    {
        $this->validate([
            'name'     => 'required|string|min:2|max:255',
            'ninea'    => 'nullable|string|max:50',
            'rc'       => 'nullable|string|max:50',
            'phone'    => 'nullable|string|max:100',
            'whatsapp' => 'nullable|string|max:100',
            'email'    => 'nullable|email|max:255',
            'address'   => 'nullable|string|max:500',
            'facebook'  => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'tiktok'    => 'nullable|string|max:255',
            'tva_rate'  => 'required|numeric|min:0|max:100',
            'invoice_prefix' => 'required|string|max:10',
            'logo'  => 'nullable|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'stamp' => 'nullable|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $settings  = CompanySetting::getSettings();
        $logoPath = $this->currentLogo;

        if ($this->logo) {
            // Supprimer l'ancien si existe
            if ($this->currentLogo && Storage::disk('public')->exists($this->currentLogo)) {
                Storage::disk('public')->delete($this->currentLogo);
            }
            $logoPath = $this->logo->store('settings', 'public');
            $this->currentLogo = $logoPath;
            $this->logo = null;
        }

        CompanySetting::where('id', 1)->update([
            'name'           => $this->name,
            'ninea'          => $this->ninea ?: null,
            'rc'             => $this->rc ?: null,
            'phone'          => $this->phone ?: null,
            'whatsapp'       => $this->whatsapp ?: null,
            'email'          => $this->email ?: null,
            'address'        => $this->address ?: null,
            'facebook'       => $this->facebook ?: null,
            'instagram'      => $this->instagram ?: null,
            'tiktok'         => $this->tiktok ?: null,
            'tva_rate'       => $this->tva_rate,
            'invoice_prefix' => $this->invoice_prefix,
            'currency'       => $this->currency,
            'logo'           => $logoPath,
        ]);

        $this->currentLogo = $logoPath;
        $this->logo        = null;
        $this->showSaved();
    }

    public function saveSignature(): void
    {
        $settings   = CompanySetting::getSettings();
        $stampPath  = $this->currentStamp;

        // Upload cachet
        if ($this->stamp) {
            $this->validate(['stamp' => 'nullable|image|max:2048']);
            if ($this->currentStamp) {
                Storage::disk('public')->delete($this->currentStamp);
            }
            $stampPath = $this->stamp->store('settings', 'public');
        }

        // Signature dessinée
        $signaturePath = $this->currentSignature;
        if ($this->signatureData) {
            // Décoder base64 et sauvegarder
            $imageData = base64_decode(
                preg_replace('#^data:image/\w+;base64,#i', '', $this->signatureData)
            );
            $filename  = 'settings/signature_' . time() . '.png';
            Storage::disk('public')->put($filename, $imageData);
            $signaturePath = $filename;
        }

        if ($this->signatureCleared) {
            if ($this->currentSignature) {
                Storage::disk('public')->delete($this->currentSignature);
            }
            $signaturePath = null;
        }

        $settings->update([
            'stamp_image'     => $stampPath,
            'signature_image' => $signaturePath,
        ]);

        $this->currentStamp     = $stampPath;
        $this->currentSignature = $signaturePath;
        $this->stamp            = null;
        $this->signatureData    = null;
        $this->signatureCleared = false;

        $this->showSaved();
    }

    public function clearSignature(): void
    {
        $this->signatureCleared = true;
        $this->signatureData    = null;
        $this->dispatch('clearSignaturePad');
    }

    private function showSaved(): void
    {
        $this->saved = true;
        $this->dispatch('flash-saved');
    }

    public function render()
    {
        return view('livewire.parametres.parametre-manager', [
            'settings' => CompanySetting::getSettings(),
        ]);
    }
}