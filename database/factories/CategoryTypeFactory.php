<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\CategoryType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryTypeFactory extends Factory
{
    protected $model = CategoryType::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->word(),
            'name_singular' => fake()->word(),
            'publish' => true,
            'sort_order' => 0,
        ];
    }
}
