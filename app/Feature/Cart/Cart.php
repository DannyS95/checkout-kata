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
    private Collection $specialOffers;
    /**
     * @var Collection[ItemDetails]
     */
    private Collection $itemDetails;

    public function __construct()
    {
        $this->specialOffers = collect();
        $this->itemDetails = collect();
    }

    private function find(Item $item): Collection
    {
        return $this->itemDetails->filter(function(ItemDetails $value, $key)  use ($item) {
            return $value->item->slug === $item->slug;
        });
    }

    public function add(ItemDetails $itemDetails): void
    {
        $current = $this->find($itemDetails->item);

        if ($current->isEmpty()) {
            $this->itemDetails->push($itemDetails);
        } else {
            $current->first()->quantity += $itemDetails->quantity;
        }
    }

    public function loadActiveSpecialOffers()
    {
        /** @var ItemDetails $itemDetails */
        foreach ($this->itemDetails as $itemDetails) {
            if ($itemDetails->quantityAvailableForSpecialOffers() > 0) {
                $item = $itemDetails->item;
                $this->loadSpecialOffers($item, $itemDetails);
            }
        }
    }

    private function loadSpecialOffers(Item $item, ItemDetails $itemDetails): void
    {
        $specialOffers = $item->itemSpecialOffers();

        if ($item->specialOffers()->count() > 0 && $itemDetails->quantityAvailableForSpecialOffers() > 0) {
              $this->loadItemSpecialOffers($specialOffers->get(), $itemDetails);
        }

        $this->loadItemBundlesOffers($item, $itemDetails);

        if (isset($specialOfferDetails)) {
            $this->specialOffers->push($specialOfferDetails);
        }
    }

    public function loadItemBundlesOffers(Item $item, ItemDetails $itemDetails): void
    {
        $specialOfferBundleItemIds = $this->itemDetails
            ->map(function(ItemDetails $value, $key) use ($item) {
                return $value->item->id !== $item->id && $value->quantityAvailableForSpecialOffers() > 0 ? $value->item->id : null;
            })
            ->filter()
            ->toArray();

        $idsSeperator = implode(',', $specialOfferBundleItemIds);
        $specialOfferBundles = $item->itemBundlesSpecialOffers()
            ->whereIn('bundle_item_id', $specialOfferBundleItemIds)
            ->orderByRaw("FIND_IN_SET(bundle_item_id,'$idsSeperator')");

        if ($specialOfferBundles->count() > 0) {
            $this->addSpecialOfferBundles($specialOfferBundles->get(), $itemDetails);
        }
    }

    private function loadItemSpecialOffers(Collection $specialOffers, ItemDetails $itemDetails): void
    {
        foreach ($specialOffers as $specialOffer) {
            $specialOfferDetails = new SpecialOfferDetails(itemDetails: $itemDetails, specialOffer: $specialOffer, bundleDetails:null);
            if ($specialOfferDetails->isPromoEligeable) {
                $this->specialOffers->push($specialOfferDetails);
            }
        }
    }

    private function addSpecialOfferBundles(Collection $specialOfferBundles, ItemDetails $itemDetails): void
    {
        foreach ($specialOfferBundles as $specialOffer) {
            $bundleItemDetails = $this->itemDetails
                ->filter(function(ItemDetails $value , $key) use ($specialOffer) {
                    return $specialOffer->itemBundle()->first()->bundleItemId() === $value->item->id;
                })
                ->first();

            $bundleItemDetails->quantityUsedForSpecialOffers++;
            $itemDetails->quantityUsedForSpecialOffers++;

            $specialOfferDetails = new SpecialOfferDetails(itemDetails: null, specialOffer: $specialOffer, bundleDetails: new BundleDetails(
                $itemDetails,
                $this->itemDetails
                ->filter(function(ItemDetails $value , $key) use ($specialOffer) {
                    return $specialOffer->itemBundle()->first()->bundleItemId() === $value->item->id;
                })
                ->first()
            ));

            if ($specialOfferDetails->isPromoEligeable) {
                $this->specialOffers->push($specialOfferDetails);
            }
        }
    }
}
