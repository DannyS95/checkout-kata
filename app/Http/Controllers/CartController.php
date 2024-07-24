<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Feature\Cart\ItemDetails;
use App\Feature\Checkout\CheckoutDetails;
use App\Http\Managers\CartInstanceManager;

class CartController extends Controller
{
    public function __invoke(Item $item, Request $request, CartInstanceManager $cartInstanceManager)
    {
        $cart = $cartInstanceManager->getCart();

        $cart->add(new ItemDetails($item, $request->query('quantity')));

        $cart->loadActiveSpecialOffers();

        $checkoutDetails = new CheckoutDetails($cart);

        $itemDetails = $checkoutDetails->getItemCheckoutDetails();

        $specialOfferDetails = $checkoutDetails->getSpecialOffersCheckoutDetails();

        $finalCheckoutDetails = $checkoutDetails->getFinalCheckoutDetails();

        $cartInstanceManager->update($cart);

        return response(200);
    }
}
