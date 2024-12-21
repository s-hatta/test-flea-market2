<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;
use DateTime;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $now = new DateTime();
        return [
            'owner_id' => User::factory(),
            'name' => $this->faker->words(2, true),
            'brand_name' => $this->faker->company(),
            'condition_id' => 1,
            'price' => $this->faker->numberBetween(100, 50000),
            'detail' => $this->faker->sentence(),
            'img_url' => 'test-image.jpg',
            'stock' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}