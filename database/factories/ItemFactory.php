<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement(['A', 'B', 'C', 'D', 'E']),
            'unit_price' => $this->faker->randomElement([50, 75, 25, 150, 200]),
        ];
    }
}
