<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CompanySetting;
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
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.panier')
                ->with('error', 'Votre panier est vide.');
        }

        $total    = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $settings = CompanySetting::getSettings();

        return view('shop.checkout', compact('cart', 'total', 'settings'));
    }

    public function store(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.panier');
        }

        $request->validate([
            'customer_name'    => 'required|string|min:2|max:255',
            'customer_phone'   => 'required|string|min:8|max:20',
            'customer_email'   => 'nullable|email|max:255',
            'delivery_address' => 'required|string|min:5|max:500',
            'city'             => 'required|string|max:100',
            'notes'            => 'nullable|string|max:500',
        ], [
            'customer_name.required'    => 'Votre nom est obligatoire.',
            'customer_phone.required'   => 'Votre téléphone est obligatoire.',
            'delivery_address.required' => 'L\'adresse de livraison est obligatoire.',
        ]);

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

        $order = DB::transaction(function () use ($request, $cart, $subtotal) {
            $order = Order::create([
                'reference'        => Order::generateReference(),
                'user_id'          => auth()->id(),
                'customer_name'    => $request->customer_name,
                'customer_email'   => $request->customer_email,
                'customer_phone'   => $request->customer_phone,
                'delivery_address' => $request->delivery_address,
                'city'             => $request->city,
                'notes'            => $request->notes,
                'subtotal'         => $subtotal,
                'delivery_fee'     => 0,
                'total'            => $subtotal,
                'payment_method'   => 'paydunya',
                'payment_status'   => 'pending',
                'status'           => 'pending',
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['name'],
                    'unit_price'   => $item['price'],
                    'quantity'     => $item['quantity'],
                    'total'        => $item['price'] * $item['quantity'],
                ]);
            }

            return $order;
        });

        return $this->initiatePaydunya($order);
    }

    // ── Envoi des emails de confirmation ────────────────────────────────────

    private function sendOrderEmails(Order $order): void
    {
        try {
            // Email au client (si email fourni)
            if ($order->customer_email) {
                Mail::to($order->customer_email)
                    ->send(new OrderConfirmationMail($order));
            }

            // Email à l'admin
            Mail::to(config('mail.admin_address'))
                ->send(new NewOrderAdminMail($order));

        } catch (\Exception $e) {
            Log::error('Mail sending error: ' . $e->getMessage());
        }
    }

    // ── Configuration PayDunya ───────────────────────────────────────────────

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

    // ── Initiation du paiement ───────────────────────────────────────────────

    private function initiatePaydunya(Order $order)
    {
        try {
            $this->setupPaydunya();

            $invoice = new CheckoutInvoice();
            $invoice->addItem(
                'Commande ' . $order->reference,
                1,
                $order->total,
                $order->total,
                'Al-Adab Tech Store'
            );
            $invoice->setTotalAmount($order->total);
            $invoice->addCustomData('order_reference', $order->reference);

            if ($invoice->create()) {
                $order->update([
                    'paydunya_token'         => $invoice->token,
                    'paydunya_invoice_token' => $invoice->token,
                ]);

                session()->forget('cart');
                return redirect($invoice->getInvoiceUrl());
            }

            Log::error('PayDunya create failed: ' . $invoice->response_text);
            return back()->with('error', 'Erreur lors de l\'initialisation du paiement. Réessayez.');

        } catch (\Exception $e) {
            Log::error('PayDunya error: ' . $e->getMessage());
            return back()->with('error', 'Erreur de paiement. Veuillez réessayer.');
        }
    }

    // ── Callback (retour client après paiement) ──────────────────────────────

    public function callback(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('shop.home')
                ->with('error', 'Token de paiement invalide.');
        }

        try {
            $this->setupPaydunya();

            $invoice = new CheckoutInvoice();

            if ($invoice->confirm($token)) {
                $order = Order::where('paydunya_token', $token)->first();

                if ($order) {
                    $order->update([
                        'payment_status' => 'paid',
                        'status'         => 'pending',
                    ]);

                    $this->sendOrderEmails($order->fresh('items'));

                    return redirect()
                        ->route('shop.confirmation', $order->reference)
                        ->with('success', 'Paiement reçu ! Votre commande est en cours de traitement.');
                }
            }

        } catch (\Exception $e) {
            Log::error('PayDunya callback error: ' . $e->getMessage());
        }

        return redirect()->route('shop.home')
            ->with('error', 'Erreur de vérification du paiement.');
    }

    // ── Webhook IPN (notification serveur PayDunya) ──────────────────────────

    public function webhook(Request $request)
    {
        // Vérification de la signature PayDunya
        $hash = $request->header('X-Paydunya-Signature')
            ?? $request->header('Paydunya-Signature')
            ?? null;

        $masterKey = config('services.paydunya.master_key', '');

        if (!$hash || $hash !== hash_hmac('sha256',
            $request->getContent(), $masterKey)) {
            \Log::warning('PayDunya webhook: signature invalide', [
                'ip'   => $request->ip(),
                'hash' => $hash,
            ]);
            return response()->json(['error' => 'Signature invalide'], 401);
        }

        $data   = $request->json()->all();
        $token  = $data['data']['bill']['token'] ?? null;
        $status = $data['data']['bill']['status'] ?? null;

        if (!$token) {
            return response()->json(['error' => 'Token manquant'], 400);
        }

        $order = \App\Models\Order::where('paydunya_token', $token)->first();

        if (!$order) {
            return response()->json(['error' => 'Commande introuvable'], 404);
        }

        if ($status === 'completed') {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'pending',
            ]);
            \Log::info('PayDunya webhook: paiement confirmé', [
                'order' => $order->reference,
            ]);
        } elseif ($status === 'cancelled') {
            $order->update([
                'payment_status' => 'failed',
                'status'         => 'cancelled',
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    // ── Page de confirmation ─────────────────────────────────────────────────

    public function confirmation(string $reference)
    {
        $order = Order::with('items')
            ->where('reference', $reference)
            ->firstOrFail();

        $settings = CompanySetting::getSettings();

        return view('shop.confirmation', compact('order', 'settings'));
    }
}
