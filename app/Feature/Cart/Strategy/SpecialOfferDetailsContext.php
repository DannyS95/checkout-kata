<?php

namespace App\Feature\Cart\Strategy;

use App\Feature\Cart\ItemDetails;
use App\Feature\Cart\BundleDetails;
use App\Feature\Cart\SpecialOfferDetails;

final class SpecialOfferDetailsContext
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
}
