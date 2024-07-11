<?php

namespace App\Feature\Checkout;

use App\Models\Item;
use Illuminate\Support\Collection;
use App\Feature\Checkout\ItemDetails;
use App\Feature\Checkout\Collections\ItemDetailsCollection;
use App\Feature\Checkout\Collections\SpecialOfferDetailsCollection;

class Cart
{
    private SpecialOfferDetailsCollection $specialOffers;

    private Collection $itemDetails;

    public function __construct()
    {
        $this->specialOffers = new SpecialOfferDetailsCollection();
        $this->itemDetails = collect();
    }

    private function find(Item $item): Collection
    {
        return $this->itemDetails->filter(function(ItemDetails $value, $key)  use ($item) {
            return $value->item->slug === $item->slug;
        });
    }

    public function add(ItemDetails $itemDetails)
    {
        $current = $this->find($itemDetails->item);

        if ($current->isEmpty()) {
            $this->itemDetails->push($itemDetails);
        }

        $current->first()->quantity += $itemDetails->quantity;
    }
}
