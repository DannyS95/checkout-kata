<?php

namespace App\Feature\Cart\Strategy;

use App\Feature\Cart\ItemDetails;
use App\Feature\Cart\SpecialOfferDetails;

class ItemSpecialOfferDetailsStrategy implements SpecialOfferDetailsStrategy
{
    public function __construct(public SpecialOfferDetails $specialOfferDetails, public ItemDetails $itemDetails) {
    }

    public function checkThroughItemDetailsPolicy(): void
    {
        $this->specialOfferDetails->isPromoEligeable = $this->itemDetails->quantityAvailableForSpecialOffers() >= $this->specialOfferDetails->specialOffer->requiredUnits() &&
            $this->specialOfferDetails->specialOffer->active === 1;
    }

    public function totalPriceWithoutDiscount(): float
    {
        return $this->specialOfferDetails->specialOffer->requiredUnits() * $this->specialOfferDetails->count * $this->itemDetails->item->unitPrice();
    }

    public function increment(): void
    {
        $this->specialOfferDetails->count += $this->itemDetails->quantity / $this->specialOfferDetails->specialOffer->requiredUnits();
    }

    public function totalPriceWithDiscount(): float
    {
        if ($this->specialOfferDetails->specialOffer->discountPrice() == 0) {
            return $this->totalPriceWithoutDiscount() - $this->itemDetails->item->unitPrice() * $this->specialOfferDetails->count;
        }

        return $this->specialOfferDetails->specialOffer->discountPrice() * $this->specialOfferDetails->count;
    }

    public function useItemQuantityInSpecialOffer(): void
    {
        $this->itemDetails->quantityUsedForSpecialOffers += $this->specialOfferDetails->count * $this->specialOfferDetails->specialOffer->requiredUnits();
    }
}
