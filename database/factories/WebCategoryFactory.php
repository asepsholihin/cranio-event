<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\WebCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class WebCategoryFactory extends Factory
{
    protected $model = WebCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $uid = $this->faker->numberBetween(1, 10);

        return [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->paragraph(),
            'order' => $this->faker->numberBetween(1, 3),
            'created_by' => $uid,
            'updated_by' => $uid,
        ];
    }
}
