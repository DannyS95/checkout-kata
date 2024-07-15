<?php

namespace App\Http\Managers;

use App\Feature\Cart\Cart;
use Illuminate\Support\Facades\Session;

class CartInstanceManager
{
    protected $sessionKey = 'cart';
    private ?Cart $cart;

    public function __construct()
    {
        $this->instantiateCart();
    }

    private function instantiateCart()
    {
        if (!Session::has($this->sessionKey)) {
            Session::put($this->sessionKey, [new Cart()]);
        }

        $this->cart = Session::get($this->sessionKey)[0];
    }

    public function update(Cart $cart)
    {
        $this->cart = $cart;
        Session::put($this->sessionKey, [$this->cart]);
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function cartExists(): bool
    {
        return Session::has($this->sessionKey);
    }

    public function cartDoesNotExist(): bool
    {
        return !$this->cartExists();
    }

    public function empty(): void
    {
        Session::remove($this->sessionKey);
        $this->cart = null;
    }
}
