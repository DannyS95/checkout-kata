<?php

namespace App\Feature\Checkout;

use App\Feature\Cart\Cart;
use App\Feature\Cart\ItemDetails;
use App\Feature\Cart\SpecialOfferDetails;
use App\Feature\Cart\Strategy\SpecialOfferDetailsContext;

class CheckoutDetails
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

    public function getFinalCheckoutDetails()
    {

    }

    public function getSpecialOffersCheckoutDetails(): array
    {
        $details = [];
        foreach ($this->cart->specialOfferDetailsContexts as $specialOfferDetailsContext) {
            /** @var SpecialOfferDetailsContext $specialOfferDetailsContext */
            /** @var SpecialOfferDetails $specialOfferDetails */
            $specialOfferDetails = $specialOfferDetailsContext->specialOfferDetails;
            \array_push($details, [
                'name' => $specialOfferDetails->specialOffer->specialOfferDescription(),
                'quantity' => $specialOfferDetails->count,
                'items' => [
                    $specialOfferDetailsContext->itemDetails->item->name =>
                        [
                            'quantityInOffer' => $specialOfferDetailsContext->itemDetails->quantityUsedForSpecialOffers,
                            'totalPriceWithDiscount' => $specialOfferDetailsContext->specialOfferDetailsStrategy->totalPriceWithDiscount(),
                            'totalPriceWithoutDiscount' => $specialOfferDetailsContext->specialOfferDetailsStrategy->totalPriceWithoutDiscount(),
                        ]
                ]

            ]);
        }

        return $details;
    }
}
