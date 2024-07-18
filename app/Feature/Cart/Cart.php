<?php

namespace App\Feature\Cart;

use App\Models\Item;
use App\Models\SpecialOffer;
use App\Feature\Cart\ItemDetails;
use Illuminate\Support\Collection;
use App\Feature\Cart\BundleDetails;
use App\Feature\Cart\SpecialOfferDetails;

class Cart
{
    /**
     * @var Collection[SpecialOfferDetails]
     */
    public Collection $specialOfferDetails;
    /**
     * @var Collection[ItemDetails]
     */
    public Collection $itemDetails;

    public function __construct()
    {
        $this->specialOfferDetails = collect();
        $this->itemDetails = collect();
    }
    /**
     * Summary of find
     * @param \App\Models\Item $item
     * @return \Illuminate\Support\Collection[ItemDetails]
     */
    private function find(Item $item): Collection
    {
        return $this->itemDetails->filter(function(ItemDetails $value, $key)  use ($item) {
            return $value->item->slug === $item->slug;
        });
    }
    /**
     * Summary of findOffer
     * @param \App\Models\SpecialOffer $specialOffer
     * @return \Illuminate\Support\Collection[SpecialOfferDetails]
     */
    private function findOffer(SpecialOffer $specialOffer): Collection
    {
        return $this->specialOfferDetails->filter(function(SpecialOfferDetails $value, $key)  use ($specialOffer) {
            return $value->specialOffer->id === $specialOffer->id;
        });
    }


    public function add(ItemDetails $itemDetails): self
    {
        $current = $this->find($itemDetails->item);

        if ($current->isEmpty()) {
            $this->itemDetails->push($itemDetails);
        } else {
            $current->first()->quantity += $itemDetails->quantity;
        }

        return $this;
    }

    public function loadActiveSpecialOffers()
    {
        /** @var ItemDetails $itemDetails */
        foreach ($this->itemDetails as $itemDetails) {
            if ($itemDetails->quantityAvailableForSpecialOffers() > 0) {
                $item = $itemDetails->item;
                $this->loadAllSpecialOfferTypes($item, $itemDetails);
            }
        }
    }

    private function loadAllSpecialOfferTypes(Item $item, ItemDetails $itemDetails): void
    {
        $this->loadItemSpecialOffers($item, $itemDetails)
            ->loadItemBundlesOffers($item, $itemDetails);
    }

    private function loadItemBundlesOffers(Item $item, ItemDetails $itemDetails): self
    {
        $specialOfferBundleItemIds = $this->itemDetails
            ->map(function(ItemDetails $value, $key) use ($item) {
                return $value->item->id !== $item->id && $value->quantityAvailableForSpecialOffers() > 0 ? $value->item->id : null;
            })
            ->filter()
            ->toArray();

        $specialOfferBundles = $item->itemBundlesSpecialOffers()
            ->whereIn('bundle_item_id', $specialOfferBundleItemIds);

        if ($specialOfferBundles->count() > 0) {
            $this->addSpecialOfferBundlesToCart($specialOfferBundles->get(), $itemDetails);
        }

        return $this;
    }

    private function loadItemSpecialOffers(Item $item, ItemDetails $itemDetails): self
    {
        $specialOffers = $item->itemSpecialOffers()->where('required_units', '<=', $itemDetails->quantity)->where('required_units', function($query) {
            $query->selectRaw('MAX(required_units)');
        });

        if ($specialOffers->count() > 0) {
            foreach ($specialOffers->get() as $specialOffer) {
                $this->addSpecialOfferDetailsToCart(itemDetails: $itemDetails, specialOffer: $specialOffer, bundleDetails: null);
            }
        }

        return $this;
    }

    private function addSpecialOfferBundlesToCart(Collection $specialOfferBundles, ItemDetails $itemDetails): void
    {
        foreach ($specialOfferBundles as $specialOffer) {
            $bundleItemDetails = $this->itemDetails
                ->filter(function(ItemDetails $value , $key) use ($specialOffer) {
                    return $specialOffer->itemBundle()->first()->bundleItemId() === $value->item->id;
                })
                ->first();

            $bundleItemDetails->quantityUsedForSpecialOffers++;
            $itemDetails->quantityUsedForSpecialOffers++;

            $bundleDetails = new BundleDetails(
                $itemDetails,
                $this->itemDetails
                    ->filter(function(ItemDetails $value , $key) use ($specialOffer) {
                        return $specialOffer->itemBundle()->first()->bundleItemId() === $value->item->id;
                })
                ->first()
            );

            $this->addSpecialOfferDetailsToCart(specialOffer: $specialOffer, itemDetails: null, bundleDetails: $bundleDetails);
        }
    }

    private function addSpecialOfferDetailsToCart(SpecialOffer $specialOffer, ?ItemDetails $itemDetails, ?BundleDetails $bundleDetails): void
    {
        $current = $this->findOffer($specialOffer);
        if ($current->count() > 0) {
            $current->first()->count += $itemDetails->quantity % $specialOffer->requiredUnits();
            $itemDetails->quantityUsedForSpecialOffers = $specialOffer->requiredUnits();
        } else {
            $specialOfferDetails = new SpecialOfferDetails(itemDetails: $itemDetails,
                specialOffer: $specialOffer,
                bundleDetails: $bundleDetails
            );
            $this->specialOfferDetails->push($specialOfferDetails);
            $specialOfferDetails->count += $itemDetails->quantity % $specialOffer->requiredUnits();
            $itemDetails->quantityUsedForSpecialOffers = $specialOffer->requiredUnits();
        }
    }
}
