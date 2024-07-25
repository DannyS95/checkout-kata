<?php

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
class CheckoutTest extends BaseTestCase
{
}

// tests/Pest.php

uses(CheckoutTest::class);

function get($item, $quantity) {
    return "/items/$item/add-to-cart?quantity=$quantity";
}

it('is possible to add one or many products to the cart, with given quantity', function (string $item, int $quantity) {
        $url = get($item, $quantity);
        $response = $this->get($url)['itemDetails'][0];

        expect($response)
            ->toMatchArray([
                'name' => $item,
                'quantity' => $quantity,
            ]);
    })
    ->with([
        ['A', 1],
        ['B', 1],
        ['C', 2],
        ['D', 1],
        ['E', 1],
    ]);

it('item total price is quantity multiplied by unit price', function (string $item, int $quantity) {
    $url = get($item, $quantity);
    $response = $this->get($url)['itemDetails'][0];
    expect($response['totalPrice'])->toBe($quantity * $response['unitPrice']);

})
    ->with([
        ['A', 1],
        ['B', 1],
        ['C', 1],
        ['D', 1],
        ['E', 1],
    ]);

it('should be possible to earn one or more special offers by adding required items to the cart', function (array $items) {
        $resp = [];
        foreach ($items as $item) {
            $url = get($item['name'], $item['quantity']);
            $resp = $this->get($url)['specialOfferDetails'];
        }

        expect($resp)->toHaveCount(3);
    })
    ->with([
        [
            [
                ['name' => 'E', 'quantity' => 1],
                ['name' => 'D', 'quantity' => 1],
                ['name' => 'C', 'quantity' => 3],
                ['name' => 'B', 'quantity' => 3],

            ],
        ],
    ]);

it('should be possible to have a stacked quantity on special offers', function (string $item, int $quantity) {
    $url = get($item, $quantity);
    $resp = $this->get($url);

    expect($resp['specialOfferDetails'][0])->toMatchArray(['quantity' => 2]);
    expect($resp['itemDetails'][0])->toMatchArray(['quantity' => 6]);
})
    ->with([
        ['name' => 'C', 'quantity' => 6],
    ]);

it('should be possible to have more than one special offer in the cart, with a stacked quantity bigger than one, and offer quantities are correct', function (array $items) {
    $resp = [];
    foreach ($items as $item) {
        $url = get($item['name'], $item['quantity']);
        $resp = $this->get($url)['specialOfferDetails'];
    }
    expect($resp[0])->toMatchArray(['quantity' => 2]);
    expect($resp[1])->toMatchArray(['quantity' => 2]);
})
    ->with([
        [
            [
                ['name' => 'E', 'quantity' => 2],
                ['name' => 'D', 'quantity' => 2],
                ['name' => 'C', 'quantity' => 6],
                ['name' => 'B', 'quantity' => 3],

            ],
        ],
    ]);

it('the sum of the price of all the items, is smaller than the total, when a special offer is applied', function (string $item, int $quantity) {
    $url = get($item, $quantity);
    $resp = $this->get($url);

    expect($resp['finalCheckoutDetails']['itemsTotal'])->toBeGreaterThan($resp['finalCheckoutDetails']['totalPrice']);
})
    ->with([
        ['name' => 'C', 'quantity' => 7],
    ]);

it('special offers should have the right ammount of items included in it, based on the required ammount criteria', function (string $item, int $quantity) {
    $url = get($item, $quantity);
    $resp = $this->get($url);

    expect($resp['specialOfferDetails'][0]['items']['C'])->toMatchArray(['quantityInOffer' => 6]);
})
    ->with([
        ['name' => 'C', 'quantity' => 7],
    ]);

it('special offers available to an item are visible, even if not active', function (string $item, int $quantity) {
    $url = get($item, $quantity);
    $resp = $this->get($url);

    expect($resp['itemDetails'][0])->toHaveKey('specialOffers');
    expect($resp['itemDetails'][0]['specialOffers'])->toBeArray();
})
    ->with([
        ['name' => 'C', 'quantity' => 1],
    ]);

it('special offers for items that need to be bought together, as a bundle, list both items contained in the offer', function (array $items) {
    $resp = [];
    foreach ($items as $item) {
        $url = get($item['name'], $item['quantity']);
        $resp = $this->get($url)['specialOfferDetails'];
    }

    foreach ($resp[0]["items"] as $name => $item) {
        expect($item)->toMatchArray(['quantityInOffer' => 2]);
        expect($resp[0]["items"])->toHaveKey($name);
    }
})
    ->with([
    [
        [
            ['name' => 'E', 'quantity' => 2],
            ['name' => 'D', 'quantity' => 2],
        ],
    ],
    ]);

it('check if special offers for items bought together as bundles, have the correct quantity, if the items have different quantities', function (array $items) {
    $resp = [];
    foreach ($items as $item) {
        $url = get($item['name'], $item['quantity']);
        $resp = $this->get($url)['specialOfferDetails'];
    }

    $min = 0;
    $max = 0;
    foreach ($resp[0]["items"] as $name => $item) {
        $max = max($max, $item['quantityInOffer']);
        $min = min($max, $item['quantityInOffer']);
    }

    expect($min)->toBe($max);
})
    ->with([
        [
            [
                ['name' => 'E', 'quantity' => 2],
                ['name' => 'D', 'quantity' => 3],
            ],
        ],
    ]);

it('in the checkout details, the total price is, the sum of the special offer, and remaining items not included in the special offer discounts', function (string $item, int $quantity) {
    $url = get($item, $quantity);
    $resp = $this->get($url);

    $itemDetails = $resp['itemDetails'][0];
    $specialOfferDetails = $resp['specialOfferDetails'][0];

    $total = $itemDetails['unitPrice'] * ($itemDetails['quantity'] - $specialOfferDetails["items"][$item]['quantityInOffer']);
    $total += $specialOfferDetails['totalPriceWithDiscount'];

    expect($resp['finalCheckoutDetails']['totalPrice'])->toBe($total);
})
    ->with([
        ['name' => 'C', 'quantity' => 7],
    ]);

it('check special offer quantity is correct, at the end of the checkout', function (string $item, int $quantity) {
    $url = get($item, $quantity);
    $resp = $this->get($url);
    dd($resp);
    $end = $resp['finalCheckoutDetails'];
    $special = $resp['specialOfferDetails'][0];

    foreach ($end['specialOffersApplied'] as $offerApplied) {
        expect($offerApplied['quantity'])->toBe($special['quantity']);
    }
})
    ->with([
        ['name' => 'C', 'quantity' => 7],
    ]);
