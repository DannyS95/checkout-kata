<?php

namespace App\Feature\Cart;

use App\Models\Item;

class ItemDetails
{
    public float $totalPrice;

    public int $quantityUsedForSpecialOffers;

    public function __construct(public Item $item, public ?int $quantity)
    {
        $this->calculateTotalPrice();
        $this->quantityUsedForSpecialOffers = 0;
    }

    public function calculateTotalPrice(): self
    {
        $this->totalPrice = $this->quantity * $this->item->unitPrice();
        return $this;
    }

    public function quantityAvailableForSpecialOffers(): int
    {
        return $this->quantity - $this->quantityUsedForSpecialOffers;
    }
}
