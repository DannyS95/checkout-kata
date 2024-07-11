<?php

namespace App\Feature\Checkout\Collections;

use Illuminate\Support\Collection;

class ItemDetailsCollection extends Collection
{
    /**
     * @var Collection[ItemDetails]
     */
    private Collection $itemDetails;

    public function __construct() {
        $this->itemDetails = collect();
    }
    # implement this
    public function push(...$values)
    {
        $this->itemDetails->filter(function (int $value, int $key) {
            dd($value);
        });
        dd($values);
    }
}
