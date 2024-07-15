<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Feature\Cart\ItemDetails;
use App\Http\Managers\CartInstanceManager;

class CartController extends Controller
{
    public function __invoke(Item $item, Request $request, CartInstanceManager $cartInstanceManager)
    {
        $cart = $cartInstanceManager->getCart();
        $anotherItem = new Item();
        $anotherItem = $anotherItem->find(4);
        $cart->add(new ItemDetails($item, $request->query('quantity')));
        $cart->add(new ItemDetails($anotherItem, 1));

        $offers = $cart->loadActiveSpecialOffers();

        $cartInstanceManager->update($cart);

        return response(200);
    }
}
