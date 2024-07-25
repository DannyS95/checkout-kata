<?php

namespace App\Feature\Cart;

final class BundleDetails
{
    public function __construct(public ItemDetails $item, public ItemDetails $bundleItem)
    {
    }

    public function calculateQuantity()
    {
        $qtt = min($this->bundleItem->quantityAvailableForSpecialOffers(), $this->item->quantityAvailableForSpecialOffers());

        return intval($qtt > 1 ? $qtt : 2 / 2);
    }
}
