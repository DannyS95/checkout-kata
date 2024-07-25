<?php

namespace App\Feature\Cart;

use App\Models\Item;
use App\Models\SpecialOffer;

class ItemDetails
{
    public float $totalPrice;

    public int $quantityUsedForSpecialOffers;

    public function __construct(public Item $item, public ?int $quantity)
    {
        $this->calculateTotalPrice();
        $this->quantityUsedForSpecialOffers = 0;
    }

    public function calculateTotalPrice()
    {
        $this->totalPrice = $this->quantity * $this->item->unitPrice();
    }

    public function quantityAvailableForSpecialOffers(): int
    {
        return $this->quantity - $this->quantityUsedForSpecialOffers;
    }

    public function specialOffersDescriptions(): array
    {
        $collect = collect([$this->item->itemBundlesSpecialOffers, $this->item->itemSpecialOffers])->filter(function($value, $key) {
            return $value->count() > 0;
        })->flatten();

        $descriptions = [];
        foreach ($collect as $specialOffer) {
            /** @var SpecialOffer $specialOffer */
           $descriptions[] = $specialOffer->specialOfferDescription();
        }

        return $descriptions;
    }

    public function finalPrice(): float
    {
        return $this->item->unitPrice() * $this->quantityAvailableForSpecialOffers();
    }
}
