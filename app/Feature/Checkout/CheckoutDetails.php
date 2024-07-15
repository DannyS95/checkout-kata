<?php

namespace App\Feature\Checkout;

use App\Feature\Checkout\Collections\ItemDetailsCollection;

class CheckoutDetails
{
    private int $totalPriceWithoutOffers;

    private $totalPriceAfterOffers;

    private SpecialOfferDetails $specialOfferDetails;

    private ItemDetailsCollection $itemDetailsCollection;
}
