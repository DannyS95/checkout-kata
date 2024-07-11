<?php

namespace App\Feature\Checkout\Collections;

use Illuminate\Support\Collection;
use App\Feature\Checkout\SpecialOfferDetails;

class SpecialOfferDetailsCollection extends Collection
{
    /**
     *
     * @var Collection[SpecialOfferDetails]
     */
    private Collection $specialOfferDetails;

    public function __construct() {
        $this->specialOfferDetails = collect();
    }
}
