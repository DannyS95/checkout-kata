<?php

namespace App\Feature\Cart;

use App\Models\Item;
use App\Models\SpecialOffer;
use App\Feature\Cart\ItemDetails;
use Illuminate\Support\Collection;
use App\Feature\Cart\BundleDetails;
use App\Feature\Cart\SpecialOfferDetails;
use App\Feature\Cart\Strategy\SpecialOfferDetailsContext;
use App\Feature\Cart\Strategy\SpecialOfferDetailsStrategy;
use App\Feature\Cart\Strategy\ItemSpecialOfferDetailsStrategy;
use App\Feature\Cart\Strategy\ItemBundleSpecialOfferDetailsStrategy;

class Cart
{
    /**
     * @var Collection[SpecialOfferDetailsContext]
     */
    public Collection $specialOfferDetailsContexts;
    /**
     * @var Collection[ItemDetails]
     */
    public Collection $itemDetails;

    public function __construct()
    {
        $this->specialOfferDetailsContexts = collect();
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
    public function findOffer(SpecialOffer $specialOffer): Collection
    {
        return $this->specialOfferDetailsContexts->filter(function(SpecialOfferDetailsContext $value, $key)  use ($specialOffer) {
            return $value->specialOfferDetails->specialOffer->id === $specialOffer->id;
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

    public function findSpecialOffer(ItemDetails $itemDetails)
    {
        return $this->specialOfferDetailsContexts->filter(function(SpecialOfferDetailsContext $value, $key) use ($itemDetails) {
            $specialOfferItemDetails = $value->itemDetails ?? $value->bundleDetails->item;
            return $specialOfferItemDetails->item->id == $itemDetails->item->id;
        });
    }

    private function loadAllSpecialOfferTypes(Item $item, ItemDetails $itemDetails): void
    {
        /** @Todo Handle this as one merged collection */
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
        $specialOffers = $item->itemSpecialOffers()->where('required_units', '<=', $itemDetails->quantity)->orderByDesc('required_units');
        if ($specialOffers->count() > 0) {
            foreach ($specialOffers->get() as $specialOffer) {
                $specialOfferDetails = new SpecialOfferDetails($specialOffer, $itemDetails);
                $itemSpecialOfferDetailsStrategy = new ItemSpecialOfferDetailsStrategy($specialOfferDetails, $itemDetails);
                $specialOfferDetailsContext = new SpecialOfferDetailsContext(specialOfferDetailsStrategy: $itemSpecialOfferDetailsStrategy,
                    specialOfferDetails: $specialOfferDetails,
                    itemDetails: $itemDetails,
                    bundleDetails: null);
                $this->addSpecialOfferDetailsToCart( $specialOfferDetailsContext);
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

            $bundleDetails = new BundleDetails(
                $itemDetails,
                $bundleItemDetails
            );

            $specialOfferDetails = new SpecialOfferDetails($specialOffer, $itemDetails);
            $itemBundleSpecialOfferDetailsStrategy = new ItemBundleSpecialOfferDetailsStrategy($specialOfferDetails, $bundleDetails);
            $specialOfferDetailsContext = new SpecialOfferDetailsContext(specialOfferDetailsStrategy: $itemBundleSpecialOfferDetailsStrategy,
                specialOfferDetails: $specialOfferDetails,
                itemDetails: null,
                bundleDetails: $bundleDetails);

            $this->addSpecialOfferDetailsToCart($specialOfferDetailsContext);
        }
    }

    private function addSpecialOfferDetailsToCart(SpecialOfferDetailsContext $specialOfferDetailsContext): void
    {
        $current = $this->findOffer($specialOfferDetailsContext->specialOfferDetails->specialOffer); //
        if ($current->count() > 0) {
            $current = $current->first()->specialOfferDetailsStrategy;
            /**
             * @var SpecialOfferDetailsStrategy $current
             */
            $current->increment();
            $current->useItemQuantityInSpecialOffer();
        } else {
            $this->specialOfferDetailsContexts->push($specialOfferDetailsContext);
        }
    }

    public function getFinalPrice()
    {
        $price = 0;

        foreach ($this->specialOfferDetailsContexts as $specialOfferContext) {
            /** @var SpecialOfferDetailsContext $specialOfferContext */
            $price += $specialOfferContext->specialOfferDetailsStrategy->getFinalPrice();
        }

        return $price;
    }

    public function discountsTotal()
    {
        $total = 0;

        foreach ($this->specialOfferDetailsContexts as $specialOfferContext) {
            /** @var SpecialOfferDetailsContext $specialOfferContext */
            $specialOfferDetails = $specialOfferContext->specialOfferDetails;
            $total += $specialOfferDetails->getTotalDiscountValue();
        }

        return $total;
    }

    public function getItemsTotal()
    {
        $total = 0;

        foreach ($this->itemDetails as $itemDetails) {
            /** @var ItemDetails $itemDetails */
            $total += $itemDetails->totalPrice;
        }

        return $total;
    }

    public function collectSpecialOfferDescriptions()
    {
        $descriptions = [];
        foreach ($this->specialOfferDetailsContexts as $specialOfferDetailsContext) {
            /** @var SpecialOfferDetailsContext $specialOfferDetailsContext */
           $descriptions[
                $specialOfferDetailsContext
                    ->specialOfferDetails
                    ->specialOffer->specialOfferDescription($specialOfferDetailsContext->itemDetails?->item->name)] = [
                        'quantity' => $specialOfferDetailsContext->specialOfferDetails->count,
                    ];
        }

        return $descriptions;
    }
}
