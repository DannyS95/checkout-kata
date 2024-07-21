<?php

namespace App\Feature\Cart;

class BundleDetails
{
    public function __construct(public ItemDetails $item, public ItemDetails $bundleItem)
    {
    }

    public function getQuantity()
    {
        return ($this->item->quantityAvailableForSpecialOffers() +
            $this->bundleItem->quantityAvailableForSpecialOffers()) /
        2;
    }
}
