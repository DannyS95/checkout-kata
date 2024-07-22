<?php

namespace App\Feature\Cart;

class BundleDetails
{
    public function __construct(public ItemDetails $item, public ItemDetails $bundleItem)
    {
    }

    public function calculateBundleQuantity()
    {
        $min = min($this->item->quantityAvailableForSpecialOffers(), $this->bundleItem->quantityAvailableForSpecialOffers());

        return intval($min > 1 ? $min : 2 / 2);
    }
}
