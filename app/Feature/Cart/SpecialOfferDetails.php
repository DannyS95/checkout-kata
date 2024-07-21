<?php

namespace App\Feature\Cart;

use App\Models\SpecialOffer;

class SpecialOfferDetails
{
    public bool $isPromoEligeable;
    public int $count = 0;

    public function __construct(public SpecialOffer $specialOffer) {
    }
}
