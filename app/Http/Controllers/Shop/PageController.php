<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function contact()
    {
        return view('shop.contact');
    }

    public function send(Request $request)
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'name.required'    => 'Votre nom est obligatoire.',
            'email.required'   => 'Votre email est obligatoire.',
            'email.email'      => 'Adresse email invalide.',
            'subject.required' => 'Le sujet est obligatoire.',
            'message.required' => 'Votre message est obligatoire.',
            'message.min'      => 'Le message doit contenir au moins 10 caractères.',
        ]);

        $settings = CompanySetting::getSettings();
        $to       = $settings->email ?? config('mail.from.address');

        if ($to) {
            Mail::raw(
                "Nouveau message de contact\n\n"
                . "Nom : {$request->name}\n"
                . "Email : {$request->email}\n"
                . "Téléphone : " . ($request->phone ?? 'Non renseigné') . "\n"
                . "Sujet : {$request->subject}\n\n"
                . "Message :\n{$request->message}",
                function ($mail) use ($to, $request, $settings) {
                    $mail->to($to)
                         ->replyTo($request->email, $request->name)
                         ->subject('[Contact] ' . $request->subject . ' — ' . ($settings->name ?? 'Boutique'));
                }
            );
        }

        return back()->with('success', 'Votre message a bien été envoyé ! Nous vous répondrons dans les plus brefs délais.');
    }

    public function faq()
    {
        return view('shop.faq');
    }

    public function cgv()
    {
        $settings = CompanySetting::getSettings();
        return view('shop.cgv', compact('settings'));
    }
}
