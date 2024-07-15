<?php

namespace App\Feature\Cart;

class BundleDetails
{
    public function __construct(public ItemDetails $item, public ItemDetails $bundleItem)
    {
    }
}
