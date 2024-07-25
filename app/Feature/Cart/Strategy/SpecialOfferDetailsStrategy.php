<?php

namespace App\Feature\Cart\Strategy;

interface SpecialOfferDetailsStrategy
{

    public function checkThroughItemDetailsPolicy(): void;

    public function totalPriceWithoutDiscount(): float;

    public function increment(): void;

    public function totalPriceWithDiscount(): float;

    public function useItemQuantityInSpecialOffer(): void;

    public function getFinalPrice(): float;
}
