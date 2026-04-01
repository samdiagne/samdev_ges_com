<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Services\QuoteService;
use Illuminate\Http\Request;

class DevisController extends Controller
{
    public function __construct(private QuoteService $quoteService) {}

    public function download(Quote $quote)
    {
        $quote->load(['items', 'customer']);
        return $this->quoteService->download($quote);
    }

}