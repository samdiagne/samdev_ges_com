<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Services\QuoteService;

class DevisController extends Controller
{
    public function __construct(private QuoteService $quoteService) {}

    public function download(Quote $quote)
    {
        $quote->load(['items', 'client']);
        return $this->quoteService->download($quote);
    }
}
