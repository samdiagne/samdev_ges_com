<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\CommandeLigne;
use App\Models\Parametre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;
use App\Mail\NewOrderAdminMail;
use Paydunya\Setup;
use Paydunya\Checkout\Store;
use Paydunya\Checkout\CheckoutInvoice;

class CheckoutController extends Controller
{
    public function index()
    {
        $panier = session('panier', []);

        if (empty($panier)) {
            return redirect()->route('shop.panier')
                ->with('error', 'Votre panier est vide.');
        }

        $total      = collect($panier)->sum(fn($i) => $i['prix'] * $i['quantite']);
        $parametres = Parametre::first();

        return view('shop.checkout', compact('panier', 'total', 'parametres'));
    }

    public function store(Request $request)
    {
        $panier = session('panier', []);

        if (empty($panier)) {
            return redirect()->route('shop.panier');
        }

        $request->validate([
            'nom_client'        => 'required|string|min:2|max:255',
            'telephone_client'  => 'required|string|min:8|max:20',
            'email_client'      => 'nullable|email|max:255',
            'adresse_livraison' => 'required|string|min:5|max:500',
            'ville'             => 'required|string|max:100',
            'notes'             => 'nullable|string|max:500',
        ], [
            'nom_client.required'        => 'Votre nom est obligatoire.',
            'telephone_client.required'  => 'Votre téléphone est obligatoire.',
            'adresse_livraison.required' => 'L\'adresse de livraison est obligatoire.',
        ]);

        $sousTotal = collect($panier)->sum(fn($i) => $i['prix'] * $i['quantite']);
        $modePaiement = config('paydunya.enabled') ? 'paydunya' : 'whatsapp';

        $commande = DB::transaction(function () use ($request, $panier, $sousTotal, $modePaiement) {
            $commande = Commande::create([
                'reference'         => 'CMD-' . strtoupper(uniqid()),
                'user_id'           => auth()->id(),
                'nom_client'        => $request->nom_client,
                'email_client'      => $request->email_client,
                'telephone_client'  => $request->telephone_client,
                'adresse_livraison' => $request->adresse_livraison,
                'ville'             => $request->ville,
                'notes'             => $request->notes,
                'sous_total'        => $sousTotal,
                'frais_livraison'   => 0,
                'total'             => $sousTotal,
                'mode_paiement'     => $modePaiement,
                'statut_paiement'   => 'en_attente',
                'statut'            => 'en_attente',
            ]);

            foreach ($panier as $item) {
                $details = $item['details'] ?? null;
                $nom = $item['nom'];
                if ($details) {
                    $nom .= ' (' . $details . ')';
                }

                CommandeLigne::create([
                    'commande_id'   => $commande->id,
                    'produit_id'    => $item['produit_id'],
                    'nom_produit'   => $nom,
                    'details'       => $details,
                    'prix_unitaire' => $item['prix'],
                    'quantite'      => $item['quantite'],
                    'total'         => $item['prix'] * $item['quantite'],
                ]);
            }

            return $commande;
        });

        if (config('paydunya.enabled')) {
            return $this->initiatePaydunya($commande);
        }

        session()->forget('panier');
        $this->sendCommandeEmails($commande->fresh('lignes'));

        return redirect()
            ->route('shop.confirmation', $commande->reference)
            ->with('success', 'Votre commande a été enregistrée avec succès !');
    }

    private function sendCommandeEmails(Commande $commande): void
    {
        try {
            if ($commande->email_client) {
                Mail::to($commande->email_client)
                    ->send(new OrderConfirmationMail($commande));
            }
            Mail::to(config('mail.admin_address'))
                ->send(new NewOrderAdminMail($commande));
        } catch (\Throwable $e) {
            Log::error('Erreur envoi mail commande: ' . $e->getMessage());
        }
    }

    private function setupPaydunya(): void
    {
        Setup::setMasterKey(config('paydunya.master_key'));
        Setup::setPrivateKey(config('paydunya.private_key'));
        Setup::setToken(config('paydunya.token'));
        Setup::setMode(config('paydunya.mode', 'test'));

        Store::setName(config('paydunya.store.name'));
        Store::setTagline(config('paydunya.store.tagline'));
        Store::setPhoneNumber(config('paydunya.store.phone'));
        Store::setPostalAddress(config('paydunya.store.postal_addr'));
        Store::setWebsiteUrl(config('paydunya.store.website_url'));
        Store::setCancelUrl(route('shop.panier'));
        Store::setReturnUrl(route('shop.payment.callback'));
        Store::setCallbackUrl(route('shop.payment.webhook'));
    }

    private function initiatePaydunya(Commande $commande)
    {
        try {
            $this->setupPaydunya();

            $invoice = new CheckoutInvoice();
            $invoice->addItem(
                'Commande ' . $commande->reference,
                1,
                $commande->total,
                $commande->total,
                Parametre::first()?->nom ?? 'Restaurant'
            );
            $invoice->setTotalAmount($commande->total);
            $invoice->addCustomData('commande_reference', $commande->reference);

            if ($invoice->create()) {
                $commande->update([
                    'paydunya_token'         => $invoice->token,
                    'paydunya_invoice_token' => $invoice->token,
                ]);
                session()->forget('panier');
                return redirect($invoice->getInvoiceUrl());
            }

            Log::error('PayDunya create failed: ' . $invoice->response_text);
            return back()->with('error', 'Erreur lors de l\'initialisation du paiement. Réessayez.');

        } catch (\Exception $e) {
            Log::error('PayDunya error: ' . $e->getMessage());
            return back()->with('error', 'Erreur de paiement. Veuillez réessayer.');
        }
    }

    public function callback(Request $request)
    {
        $token = $request->query('token');

        if (! $token) {
            return redirect()->route('shop.home')
                ->with('error', 'Token de paiement invalide.');
        }

        try {
            $this->setupPaydunya();
            $invoice = new CheckoutInvoice();

            if ($invoice->confirm($token)) {
                $commande = Commande::where('paydunya_token', $token)->first();

                if ($commande) {
                    $dejaPaye = $commande->statut_paiement === 'paye';
                    $commande->update([
                        'statut_paiement' => 'paye',
                        'statut'          => 'en_attente',
                    ]);
                    if (! $dejaPaye) {
                        $this->sendCommandeEmails($commande->fresh('lignes'));
                    }

                    return redirect()
                        ->route('shop.confirmation', $commande->reference)
                        ->with('success', 'Paiement reçu ! Votre commande est en cours de traitement.');
                }
            }
        } catch (\Exception $e) {
            Log::error('PayDunya callback error: ' . $e->getMessage());
        }

        return redirect()->route('shop.home')
            ->with('error', 'Erreur de vérification du paiement.');
    }

    public function webhook(Request $request)
    {
        $hash      = $request->header('X-Paydunya-Signature')
            ?? $request->header('Paydunya-Signature')
            ?? null;
        $masterKey = config('paydunya.master_key', '');

        if (! $masterKey) {
            Log::error('PayDunya webhook: master key non configurée');
            return response()->json(['error' => 'Configuration invalide'], 500);
        }

        if (! $hash || ! hash_equals(hash_hmac('sha256', $request->getContent(), $masterKey), $hash)) {
            Log::warning('PayDunya webhook: signature invalide', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Signature invalide'], 401);
        }

        $data   = $request->json()->all();
        $token  = $data['data']['bill']['token'] ?? null;
        $statut = $data['data']['bill']['status'] ?? null;

        if (! $token) {
            return response()->json(['error' => 'Token manquant'], 400);
        }

        $commande = Commande::where('paydunya_token', $token)->first();

        if (! $commande) {
            return response()->json(['error' => 'Commande introuvable'], 404);
        }

        if ($statut === 'completed') {
            $dejaPaye = $commande->statut_paiement === 'paye';
            $commande->update(['statut_paiement' => 'paye', 'statut' => 'en_attente']);
            if (! $dejaPaye) {
                $this->sendCommandeEmails($commande->fresh('lignes'));
            }
        } elseif ($statut === 'cancelled') {
            $commande->update(['statut_paiement' => 'echoue', 'statut' => 'annulee']);
        }

        return response()->json(['status' => 'ok']);
    }

    public function confirmation(string $reference)
    {
        $commande = Commande::with('lignes')
            ->where('reference', $reference)
            ->firstOrFail();

        $parametres = Parametre::first();

        return view('shop.confirmation', compact('commande', 'parametres'));
    }
}
