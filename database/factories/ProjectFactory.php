<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $title = fake()->sentence(3, false);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'name' => fake()->company(),
            'location' => fake()->city(),
            'year' => fake()->numberBetween(1990, 2025),
            'description' => fake()->paragraph(),
            'info' => fake()->paragraph(),
            'status' => fake()->randomElement(['Ausgeführt', 'In Planung', 'Studie']),
            'has_detail' => true,
            'publish' => false,
            'sort_order' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['publish' => true]);
    }
}
