<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;

class StockService
{
    public function decrementStock(
        Product $product,
        int $quantity,
        string $source,
        int $sourceId,
        ?string $reason = null
    ): void {
        $before = $product->stock_quantity;
        $after  = $before - $quantity;

        $product->update(['stock_quantity' => $after]);

        StockMovement::create([
            'product_id'      => $product->id,
            'user_id'         => Auth::id(),
            'type'            => 'out',
            'source'          => $source,
            'source_id'       => $sourceId,
            'quantity_before' => $before,
            'quantity_change' => $quantity,
            'quantity_after'  => $after,
            'reason'          => $reason ?? 'Vente',
            'moved_at'        => now(),
        ]);
    }

    public function incrementStock(
        Product $product,
        int $quantity,
        string $source,
        int $sourceId,
        ?string $reason = null
    ): void {
        $before = $product->stock_quantity;
        $after  = $before + $quantity;

        $product->update(['stock_quantity' => $after]);

        StockMovement::create([
            'product_id'      => $product->id,
            'user_id'         => Auth::id(),
            'type'            => 'in',
            'source'          => $source,
            'source_id'       => $sourceId,
            'quantity_before' => $before,
            'quantity_change' => $quantity,
            'quantity_after'  => $after,
            'reason'          => $reason ?? 'Achat',
            'moved_at'        => now(),
        ]);
    }

    public function adjustStock(
        Product $product,
        int $newQuantity,
        ?string $reason = null
    ): void {
        $before = $product->stock_quantity;
        $change = abs($newQuantity - $before);
        $type   = $newQuantity > $before ? 'in' : 'out';

        $product->update(['stock_quantity' => $newQuantity]);

        StockMovement::create([
            'product_id'      => $product->id,
            'user_id'         => Auth::id(),
            'type'            => $type,
            'source'          => 'adjustment',
            'source_id'       => null,
            'quantity_before' => $before,
            'quantity_change' => $change,
            'quantity_after'  => $newQuantity,
            'reason'          => $reason ?? 'Ajustement manuel',
            'moved_at'        => now(),
        ]);
    }
}