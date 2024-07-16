<?php

namespace App\Feature\Cart;

use App\Models\SpecialOffer;

class SpecialOfferDetails
{
    public bool $isPromoEligeable;
    public int $count = 1;

    public function __construct(public ?ItemDetails $itemDetails, public SpecialOffer $specialOffer, public ?BundleDetails $bundleDetails) {
        if ($itemDetails !== null) {
            $this->isPromoEligeable = $this->checkThroughItemDetailsPolicy($itemDetails);
        } else if ($bundleDetails !== null) {
            $this->isPromoEligeable = $this->checkThroughItemDetailsPolicy($bundleDetails->item) && $this->checkThroughItemDetailsPolicy($bundleDetails->bundleItem);
        }
    }

    private function checkThroughItemDetailsPolicy(ItemDetails $itemDetails): bool
    {
        return $itemDetails->quantityAvailableForSpecialOffers() >= $this->specialOffer->requiredUnits() &&
            $this->specialOffer->active === 1;
    }
}
