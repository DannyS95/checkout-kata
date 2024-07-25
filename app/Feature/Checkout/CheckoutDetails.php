<?php

namespace App\Feature\Checkout;

use App\Feature\Cart\Cart;
use App\Feature\Cart\ItemDetails;
use App\Feature\Cart\SpecialOfferDetails;
use App\Feature\Cart\Strategy\SpecialOfferDetailsContext;

final class CheckoutDetails
{
    public function __construct(
        private Cart $cart
    ) {
    }

    public function getItemCheckoutDetails(): array {
        $details = [];
        foreach ($this->cart->itemDetails as $itemDetails) {
            /** @var ItemDetails $itemDetails */
            \array_push($details, [
                'name' => $itemDetails->item->name,
                'unitPrice' => $itemDetails->item->unitPrice(),
                'quantity' => $itemDetails->quantity,
                'totalPrice' => $itemDetails->totalPrice,
                'specialOffers' => $itemDetails->specialOffersDescriptions(),
            ]);
        }

        return $details;
    }

    public function getFinalCheckoutDetails(): array
    {
        return[
            'totalPrice' => $this->cart->getFinalPrice(),
            'itemsTotal' => $this->cart->getItemsTotal(),
            'specialOffersApplied' => $this->cart->collectSpecialOfferDescriptions(),
            'discountsTotal' => $this->cart->discountsTotal(),
        ];
    }

    public function getSpecialOffersCheckoutDetails(): array
    {
        $details = [];

        foreach ($this->cart->specialOfferDetailsContexts as $specialOfferDetailsContext) {
            /** @var SpecialOfferDetailsContext $specialOfferDetailsContext */
            /** @var SpecialOfferDetails $specialOfferDetails */
            $specialOfferDetails = $specialOfferDetailsContext->specialOfferDetails;
            \array_push($details, [
                'name' => $specialOfferDetails->specialOffer->specialOfferDescription($specialOfferDetails->itemDetails->item->name),
                'quantity' => $specialOfferDetails->count,
                'totalPriceWithDiscount' => $specialOfferDetailsContext->specialOfferDetailsStrategy->totalPriceWithDiscount(),
                'totalPriceWithoutDiscount' => $specialOfferDetailsContext->specialOfferDetailsStrategy->totalPriceWithoutDiscount(),
                'items' => array_filter([
                    $specialOfferDetails->itemDetails->item->name => [
                        'quantityInOffer' => $specialOfferDetails->itemDetails->quantityUsedForSpecialOffers,
                        'unitPrice' => $specialOfferDetails->itemDetails->item->unitPrice(),
                    ],
                    $specialOfferDetailsContext->bundleDetails?->bundleItem->item->name => [
                        'quantityInOffer' => $specialOfferDetailsContext->bundleDetails?->bundleItem->quantityUsedForSpecialOffers,
                        'unitPrice' => $specialOfferDetailsContext->bundleDetails?->bundleItem->item->unitPrice(),
                    ],
                ], function($value, $key) {
                    return $key !== "";
                }, ARRAY_FILTER_USE_BOTH)

            ]);
        }

        return $details;
    }
}
