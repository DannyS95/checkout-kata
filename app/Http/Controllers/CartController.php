<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Feature\Checkout\ItemDetails;
use App\Http\Managers\CartInstanceManager;

class CartController extends Controller
{
    public function __invoke(Item $item, Request $request, CartInstanceManager $cartInstanceManager)
    {
        $cart = $cartInstanceManager->getCart();

        $cart->add(new ItemDetails($item, $request->query('quantity')));

        $cartInstanceManager->update($cart);

        return response(200);
    }
}
