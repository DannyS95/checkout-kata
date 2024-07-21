<?php

namespace App\Feature\Checkout;

use App\Feature\Cart\Cart;
use App\Feature\Cart\ItemDetails;
use App\Feature\Cart\SpecialOfferDetails;

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
        foreach ($this->cart->specialOfferDetails as $specialOfferDetails) {
            /** @var SpecialOfferDetails $specialOfferDetails */
            \array_push($details, [
                'name' => $specialOfferDetails->specialOffer->specialOfferDescription(),
                'quantity' => $specialOfferDetails->count,
                'items' => [
                    $specialOfferDetails->itemDetails->item->name =>
                        [
                            'quantityInOffer' => $specialOfferDetails->itemDetails->quantityUsedForSpecialOffers,
                            'totalPriceWithDiscount' => $specialOfferDetails->totalPriceWithDiscount(), //
                            'totalPriceWithoutDiscount' => $specialOfferDetails->totalPriceWithoutDiscount(),
                        ]
                ]

            ]);
        }

        return $details;
    }
}
