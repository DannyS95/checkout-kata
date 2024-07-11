<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'A',
                'unit_price' => 50,
                'slug' => Str::upper(Str::slug('A')),
            ],
            [
                'name' => 'B',
                'unit_price' => 75,
                'slug' => Str::upper(Str::slug('B')),
            ],
            [
                'name' => 'C',
                'unit_price' => 25,
                'slug' => Str::upper(Str::slug('C')),
            ],
            [
                'name' => 'D',
                'unit_price' => 150,
                'slug' => Str::upper(Str::slug('D')),
            ],
            [
                'name' => 'E',
                'unit_price' => 200,
                'slug' => Str::upper(Str::slug('E')),
            ],
        ];

        DB::table('items')->insert($items);

        $specialOffers = [
            [
                'offer_discount_price' => 1.25,
                'required_units' => 2,
                'active' => true,
            ],
            [
                'offer_discount_price' => 0,
                'required_units' => 3,
                'active' => true,
            ],
            [
                'offer_discount_price' => 3,
                'required_units' => null,
                'active' => true,
            ],
        ];

        DB::table('special_offers')->insert($specialOffers);


        $itemSpecialOffers = [
            [
                'item_id' => 2,
                'special_offer_id' => 1,
                'discount_single_item_applications' => null,
            ],
            [
                'item_id' => 3,
                'special_offer_id' => 2,
                'discount_single_item_applications' => 1,
            ],
        ];

        DB::table('item_special_offers')->insert($itemSpecialOffers);

        $itemsBundledSpecialOffers = [
            [
                'item_id' => 4,
                'bundled_item_id' => 5,
                'special_offer_id' => 3,
            ]
        ];

        DB::table('items_bundled_special_offers')->insert($itemsBundledSpecialOffers);
    }
}
