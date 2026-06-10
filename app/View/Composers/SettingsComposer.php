<?php

namespace App\View\Composers;

use App\Models\Parametre;
use Illuminate\View\View;

class SettingsComposer
{
    protected $settings;

    public function __construct()
    {
        $this->settings = Parametre::first();
    }

    public function compose(View $view): void
    {
        $view->with('settings', $this->settings);
    }
}