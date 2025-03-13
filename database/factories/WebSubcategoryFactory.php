<?php

namespace Database\Factories;

use App\Models\WebCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\WebSubcategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class WebSubcategoryFactory extends Factory
{
    protected $model = WebSubcategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $uid = $this->faker->numberBetween(1, 10);

        return [
            'category_id' => $this->faker->shuffle(WebCategory::all()->pluck('id')->toArray())[0],
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->paragraph(),
            'order' => $this->faker->numberBetween(1, 3),
            'created_by' => $uid,
            'updated_by' => $uid,
        ];
    }
}
