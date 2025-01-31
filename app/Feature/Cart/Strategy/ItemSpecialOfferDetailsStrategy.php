<?php

namespace App\Feature\Cart\Strategy;

use App\Feature\Cart\ItemDetails;
use App\Feature\Cart\SpecialOfferDetails;

final class ItemSpecialOfferDetailsStrategy implements SpecialOfferDetailsStrategy
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
        $this->itemDetails->quantityUsedForSpecialOffers += $this->unitsUsedInSpecialOffer() * $this->specialOfferDetails->specialOffer->requiredUnits();
    }

    public function unitsUsedInSpecialOffer(): int
    {
        return $this->itemDetails->quantity / $this->specialOfferDetails->specialOffer->requiredUnits();
    }

    public function getFinalPrice(): float
    {
        if ($this->specialOfferDetails->specialOffer->discountPrice() > 0) {
            return $this->itemDetails->totalPrice - $this->specialOfferDetails->getTotalDiscountValue();
        }

        return $this->itemDetails->totalPrice - $this->specialOfferDetails->getTotalDiscountValue();
    }
}
