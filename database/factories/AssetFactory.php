<?php

namespace Database\Factories;

use App\Models\AssetLocation;
use App\Models\Brand;
use App\Models\BusinessEntity;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'purchase_date' => $this->faker->date(),
            'business_entity_id' => $this->faker->numberBetween(1, 5),
            'name' => $this->faker->word(),
            'category_id' => $this->faker->numberBetween(1, 5),
            'brand_id' => $this->faker->numberBetween(1, 5),
            'type' => $this->faker->randomElement(['laptop', 'monitor', 'keyboard']),
            'serial_number' => $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'imei1' => $this->faker->optional()->regexify('[0-9]{15}'),
            'imei2' => $this->faker->optional()->regexify('[0-9]{15}'),
            'item_price' => $this->faker->randomNumber(5, true),
            'asset_location_id' => $this->faker->numberBetween(1, 3),
            'is_available' => $this->faker->boolean(),
        ];
    }
}
