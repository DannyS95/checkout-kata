<?php

namespace App\Feature\Checkout;

use App\Models\SpecialOffer;
use Illuminate\Support\Collection;

class SpecialOfferDetails
{
    private SpecialOffer $specialOffer;
    private ItemDetails $itemDetails;
    # check the items that are comprised in a special offer, and make such items exempt from price, not by changing the quantity, but by affecting through a custom field
    # maybe a field for quantity exempt from price due to special offer

    public function __construct() {

    }
}
