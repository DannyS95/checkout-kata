<?php

namespace App\Feature\Cart;

use App\Models\SpecialOffer;

final class SpecialOfferDetails
{
    public bool $isPromoEligeable;
    public int $count = 0;

    public function __construct(public SpecialOffer $specialOffer, public ItemDetails $itemDetails) {
    }

    public function getTotalDiscountValue(): float
    {
        if ($this->specialOffer->discountPrice() > 0) {
            return $this->specialOffer->discountPrice() * $this->count;
        }

        return $this->count * $this->itemDetails->item->unitPrice();
    }
}
