<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\EtatController;
use App\Http\Controllers\AvisController;

// Redirect racine
Route::get('/', fn() => redirect()->route('shop.home'));

Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])
    ->name('sitemap');

// Auth routes (on installera Breeze juste après)
require __DIR__.'/auth.php';

// ── Site E-commerce (public) ──────────────────
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/',                     [App\Http\Controllers\Shop\HomeController::class, 'index'])->name('home');
    Route::get('/catalogue',            [App\Http\Controllers\Shop\CatalogueController::class, 'index'])->name('catalogue');
    Route::get('/produit/{slug}',       [App\Http\Controllers\Shop\CatalogueController::class, 'show'])->name('produit');
    Route::get('/panier',               [App\Http\Controllers\Shop\PanierController::class, 'index'])->name('panier');
    Route::get('/checkout',             [App\Http\Controllers\Shop\CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout',            [App\Http\Controllers\Shop\CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/confirmation/{order}', [App\Http\Controllers\Shop\CheckoutController::class, 'confirmation'])->name('confirmation');

    Route::post('/panier/add',    [App\Http\Controllers\Shop\PanierController::class, 'add'])->name('panier.add');
    Route::post('/panier/update', [App\Http\Controllers\Shop\PanierController::class, 'update'])->name('panier.update');
    Route::post('/panier/remove', [App\Http\Controllers\Shop\PanierController::class, 'remove'])->name('panier.remove');

    Route::get('/payment/callback', [App\Http\Controllers\Shop\CheckoutController::class, 'callback'])->name('payment.callback');
    Route::post('/payment/webhook', [App\Http\Controllers\Shop\CheckoutController::class, 'webhook'])->name('payment.webhook');

    Route::get('/contact',       [App\Http\Controllers\Shop\PageController::class, 'contact'])->name('contact');
    Route::post('/contact',      [App\Http\Controllers\Shop\PageController::class, 'send'])->name('contact.send');
    Route::get('/faq',           [App\Http\Controllers\Shop\PageController::class, 'faq'])->name('faq');
    Route::get('/cgv',           [App\Http\Controllers\Shop\PageController::class, 'cgv'])->name('cgv');
});

// ── Auth client ───────────────────────────────
Route::prefix('compte')->name('compte.')->group(function () {
    Route::get('/connexion',    [App\Http\Controllers\Shop\AuthClientController::class, 'loginForm'])->name('login');
    Route::post('/connexion',   [App\Http\Controllers\Shop\AuthClientController::class, 'login'])->name('login.post');
    Route::get('/inscription',  [App\Http\Controllers\Shop\AuthClientController::class, 'registerForm'])->name('register');
    Route::post('/inscription', [App\Http\Controllers\Shop\AuthClientController::class, 'register'])->name('register.post');
    Route::post('/deconnexion', [App\Http\Controllers\Shop\AuthClientController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'active'])->group(function () {
        Route::get('/mes-commandes',         [App\Http\Controllers\Shop\EspaceClientController::class, 'index'])->name('commandes');
        Route::get('/commande/{order}',      [App\Http\Controllers\Shop\EspaceClientController::class, 'show'])->name('commande.show');
        Route::get('/profil',                [App\Http\Controllers\Shop\EspaceClientController::class, 'profil'])->name('profil');
        Route::put('/profil',                [App\Http\Controllers\Shop\EspaceClientController::class, 'updateProfil'])->name('profil.update');
        Route::put('/profil/mot-de-passe',   [App\Http\Controllers\Shop\EspaceClientController::class, 'updatePassword'])->name('profil.password');
        Route::delete('/compte',             [App\Http\Controllers\Shop\EspaceClientController::class, 'deleteAccount'])->name('delete');
    });

});

// Routes protégées
Route::middleware(['auth', 'active'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile',      fn() => view('profile.index'))->name('profile');
    Route::get('/profile/edit', fn() => view('profile.index'))->name('profile.edit');

    Route::prefix('caisse')->name('caisse.')->middleware('gestionnaire')->group(function () {
    Route::get('/pos', fn() => view('caisse.pos'))->name('pos');

    // Ventes
    Route::get('/ventes', fn() => view('caisse.ventes.index'))->name('ventes.index');

    // Achats
    Route::get('/achats', fn() => view('caisse.achats.index'))->name('achats.index');
    Route::get('/achats/create', fn() => view('caisse.achats.create'))->name('achats.create');
    Route::get('/achats/{purchase}/edit', function(\App\Models\Purchase $purchase) {
        return view('caisse.achats.edit', compact('purchase'));
    })->name('achats.edit');

    // Dépenses
    Route::get('/depenses', fn() => view('caisse.depenses.index'))->name('depenses.index');
    });
    
    // ── Produits ─────────────────────────────────
    Route::get('/produits',             fn() => view('produits.index'))->name('produits.index');
    Route::get('/produits/create',      fn() => view('produits.create'))->name('produits.create');
    Route::get('/produits/{product}/edit', function(\App\Models\Product $product) {
        return view('produits.edit', compact('product'));
    })->name('produits.edit');

    Route::get('/avis',                    [AvisController::class, 'index'])->name('avis.index');
    Route::post('/avis/{review}/approve',  [AvisController::class, 'approve'])->name('avis.approve');
    Route::delete('/avis/{review}',        [AvisController::class, 'destroy'])->name('avis.destroy');
        
    // ── Fournisseurs ──────────────────────────────
    Route::get('/fournisseurs', fn() => view('fournisseurs.index'))->name('fournisseurs.index');

    // ── Commandes en ligne ────────────────────────
    Route::get('/commandes', fn() => view('commandes.index'))->name('commandes.index');

    // ── Clients ───────────────────────────────────
    Route::get('/clients', fn() => view('clients.index'))->name('clients.index');

    // ── Factures ──────────────────────────────────
    Route::get('/factures', fn() => view('factures.index'))->name('factures.index');
    Route::get('/factures/{sale}',          [FactureController::class, 'show'])->name('factures.show');
    Route::get('/factures/{sale}/download', [FactureController::class, 'download'])->name('factures.download');
    
    // ── Rapports ──────────────────────────────────
    Route::get('/rapports', fn() => view('rapports.index'))->name('rapports.index');

    Route::get('/categories', fn() => view('categories.index'))->name('categories.index');

    // Devis
    Route::get('/devis',              fn() => view('devis.index'))->name('devis.index');
    Route::get('/devis/create',       fn() => view('devis.create'))->name('devis.create');
    Route::get('/devis/{quote}/edit', function(\App\Models\Quote $quote) {
        return view('devis.edit', compact('quote'));
    })->name('devis.edit');
    Route::get('/devis/{quote}/download',  [DevisController::class, 'download'])->name('devis.download');

    // États PDF
    Route::get('/etats', fn() => view('etats.index'))->name('etats.index');
    Route::get('/etats/ventes',     [EtatController::class, 'etatVentes'])->name('etats.ventes');
    Route::get('/etats/depenses',   [EtatController::class, 'etatDepenses'])->name('etats.depenses');
    Route::get('/etats/tresorerie', [EtatController::class, 'etatTresorerie'])->name('etats.tresorerie');
    Route::get('/etats/stocks',     [EtatController::class, 'bilanStocks'])->name('etats.stocks');

    // ── Admin only ────────────────────────────────
    Route::middleware('admin')->group(function () {
        Route::get('/parametres',   fn() => view('parametres.index'))->name('parametres.index');
        Route::get('/utilisateurs', fn() => view('utilisateurs.index'))->name('utilisateurs.index');
    });
});