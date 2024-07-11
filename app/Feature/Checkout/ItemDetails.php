<?php

namespace App\Feature\Checkout;

use App\Models\Item;

class ItemDetails
{
    private float $totalPrice;

    public function __construct(public Item $item, public ?int $quantity)
    {
        $this->totalPrice = $this->calculateTotalPrice();
    }

    private function calculateTotalPrice(): float
    {
        return $this->quantity * $this->item->price;
    }
}
