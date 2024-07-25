<?php

namespace App\Feature\Cart\Strategy;

use App\Feature\Cart\BundleDetails;
use App\Feature\Cart\SpecialOfferDetails;

class ItemBundleSpecialOfferDetailsStrategy implements SpecialOfferDetailsStrategy
{
    public function __construct(public SpecialOfferDetails $specialOfferDetails, public BundleDetails $bundleDetails) {
    }

    public function checkThroughItemDetailsPolicy(): void
    {
        $this->specialOfferDetails->isPromoEligeable = $this->bundleDetails->item->quantityAvailableForSpecialOffers() >= $this->specialOfferDetails->specialOffer->requiredUnits() &&
            $this->bundleDetails->bundleItem->quantityAvailableForSpecialOffers() >= $this->specialOfferDetails->specialOffer->requiredUnits() &&
            $this->specialOfferDetails->specialOffer->active === 1;
    }

    public function totalPriceWithoutDiscount(): float
    {
        return ($this->bundleDetails->item->item->unitPrice() + $this->bundleDetails->bundleItem->item->unitPrice()) * $this->specialOfferDetails->count;
    }

    public function increment(): void
    {
        $this->specialOfferDetails->count += $this->bundleDetails->calculateQuantity();
    }

    public function totalPriceWithDiscount(): float
    {
        return $this->specialOfferDetails->specialOffer->discountPrice() * $this->specialOfferDetails->count;
    }

    public function useItemQuantityInSpecialOffer(): void
    {
        $val = $this->bundleDetails->calculateQuantity();
        $this->bundleDetails->item->quantityUsedForSpecialOffers += $val;
        $this->bundleDetails->bundleItem->quantityUsedForSpecialOffers += $val;
    }

    public function getFinalPrice(): float
    {
        return $this->specialOfferDetails->specialOffer->discountPrice() * $this->specialOfferDetails->count;
    }
}
