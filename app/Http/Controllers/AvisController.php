<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;

class AvisController extends Controller
{
    public function index()
    {
        $pending  = ProductReview::with('product')
            ->where('is_approved', false)->latest()->get();
        $approved = ProductReview::with('product')
            ->where('is_approved', true)->latest()->limit(20)->get();

        return view('avis.index', compact('pending', 'approved'));
    }

    public function approve(ProductReview $review)
    {
        $review->update(['is_approved' => true]);
        return back()->with('success', 'Avis approuvé !');
    }

    public function destroy(ProductReview $review)
    {
        $review->delete();
        return back()->with('success', 'Avis supprimé !');
    }
}