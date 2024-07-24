<?php

namespace App\Feature\Cart\Strategy;

use App\Feature\Cart\ItemDetails;
use App\Feature\Cart\BundleDetails;
use App\Feature\Cart\SpecialOfferDetails;

class SpecialOfferDetailsContext
{
    public function __construct(public SpecialOfferDetailsStrategy $specialOfferDetailsStrategy,
        public SpecialOfferDetails $specialOfferDetails,
        public ?ItemDetails $itemDetails,
        public ?BundleDetails $bundleDetails
    ) {
        $this->specialOfferDetailsStrategy->checkThroughItemDetailsPolicy();
        $this->specialOfferDetailsStrategy->increment();
        $this->specialOfferDetailsStrategy->useItemQuantityInSpecialOffer();
    }

    public function getFinalPrice(): float
    {
        if ($this->specialOfferDetails->specialOffer->discountPrice() > 0) {
            return $this->itemDetails->totalPrice - $this->specialOfferDetails->getTotalDiscountValue();
        }

        return $this->itemDetails->totalPrice - $this->specialOfferDetails->getTotalDiscountValue();
    }
}
