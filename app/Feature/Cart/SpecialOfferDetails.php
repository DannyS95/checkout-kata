<?php

namespace App\Feature\Cart;

use App\Models\SpecialOffer;

class SpecialOfferDetails
{
    public bool $isPromoEligeable;
    public int $count = 0;

    public function __construct(public ?ItemDetails $itemDetails, public SpecialOffer $specialOffer, public ?BundleDetails $bundleDetails) {
        if ($itemDetails !== null) {
            $this->isPromoEligeable = $this->checkThroughItemDetailsPolicy($itemDetails);
        } else if ($bundleDetails !== null) {
            $this->isPromoEligeable = $this->checkThroughItemDetailsPolicy($bundleDetails->item) && $this->checkThroughItemDetailsPolicy($bundleDetails->bundleItem);
        }

        $this->increment();
    }

    private function checkThroughItemDetailsPolicy(ItemDetails $itemDetails): bool
    {
        return $itemDetails->quantityAvailableForSpecialOffers() >= $this->specialOffer->requiredUnits() &&
            $this->specialOffer->active === 1;
    }

    public function increment()
    {
        $this->count += $this->itemDetails->quantity / $this->specialOffer->requiredUnits();
    }

    public function totalPriceWithoutDiscount()
    {
        if (isset($this->bundleDetails)) {
            return $this->bundleDetails->item->item->unitPrice() + $this->bundleDetails->bundleItem->item->unitPrice() * $this->count;
        }

        return $this->specialOffer->requiredUnits() * $this->count * $this->itemDetails->item->unitPrice();
    }

    public function totalPriceWithDiscount()
    {
        if ($this->specialOffer->discountPrice() == 0) {
            return $this->totalPriceWithoutDiscount() - $this->itemDetails->item->unitPrice() * $this->count;
        }

        return $this->specialOffer->discountPrice() * $this->count;
    }

    public function specialOfferDescription(): string {
        $discount = $this->specialOffer->discountPrice();
        $units = $this->specialOffer->requiredUnits();

        if ($this->specialOffer->discountPrice() == 0.00) {
            return "Buy {$units}, get one Free ";
        }

        if ($this->specialOffer->requiredUnits()) {
            return "Buy {$units}, for {$discount}£";
        }

        $itemBundle = $this->specialOffer->itemBundle()->first()->bundleItemId();
        $item = $this->specialOffer->itemBundle()->first()->itemId();

        return "Buy {$item} and {$itemBundle} for {$discount}£";
    }
}
