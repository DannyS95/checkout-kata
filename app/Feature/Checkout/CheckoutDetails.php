<?php

namespace App\Feature\Checkout;

use App\Feature\Cart\Cart;
use App\Models\SpecialOffer;
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
            /** @var SpecialOfferDetails $specialOfferDetails */
            $specialOfferDetails = $this->cart->findOffer($itemDetails->item->itemBundlesSpecialOffers()->first() ?? $itemDetails->item->itemSpecialOffers()->first())->first();
            \array_push($details, [
                'name' => $itemDetails->item->name,
                'unitPrice' => $itemDetails->item->unitPrice(),
                'quantity' => $itemDetails->quantity,
                'totalPrice' => $itemDetails->totalPrice,
                # @Todo get an array of all special offers
                'specialOffer' => $specialOfferDetails->specialOfferDescription(),
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
                'name' => $specialOfferDetails->specialOfferDescription(),
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
