<?php

namespace App\Http\Controllers;

use App\Models\Avis;

class AvisController extends Controller
{
    public function index()
    {
        $enAttente = Avis::with('plat')
            ->where('est_approuve', false)
            ->latest()
            ->get();

        $approuves = Avis::with('plat')
            ->where('est_approuve', true)
            ->latest()
            ->limit(20)
            ->get();

        return view('avis.index', compact('enAttente', 'approuves'));
    }

    public function approve(Avis $avis)
    {
        $avis->update(['est_approuve' => true]);
        return back()->with('success', 'Avis approuvé !');
    }

    public function destroy(Avis $avis)
    {
        $avis->delete();
        return back()->with('success', 'Avis supprimé !');
    }
}
