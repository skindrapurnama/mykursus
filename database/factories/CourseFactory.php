<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(3),
            'description' => fake()->paragraph(3),
            'price' => fake()->numberBetween(100_000, 1_500_000),
            'quota' => fake()->numberBetween(10, 100),
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(60),
            'certificate_template' => null,
            'is_comment_enabled' => true,
        ];
    }
}
