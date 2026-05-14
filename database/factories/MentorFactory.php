<?php

namespace Database\Factories;

use App\Models\Mentor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mentor>
 */
class MentorFactory extends Factory
{
    protected $model = Mentor::class;

    public function definition(): array
    {
        $expertisePool = [
            'Laravel', 'PHP', 'React', 'Vue', 'Node.js', 'Python',
            'Data Science', 'UI/UX', 'Mobile Development', 'DevOps',
            'TypeScript', 'Tailwind CSS', 'Database Design', 'API Development',
        ];

        return [
            'user_id' => User::factory()->state(['role' => User::ROLE_INSTRUCTOR]),
            'bio' => fake('id_ID')->paragraphs(2, true),
            'expertise' => fake()->randomElements($expertisePool, fake()->numberBetween(2, 5)),
            'photo' => null,
            'linkedin_url' => 'https://linkedin.com/in/'.fake()->userName(),
            'years_experience' => fake()->numberBetween(2, 15),
            'is_active' => true,
        ];
    }
}
