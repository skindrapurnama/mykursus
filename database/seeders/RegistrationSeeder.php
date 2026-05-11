<?php

namespace Database\Seeders;

use App\Models\Registration;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $courses = Course::inRandomOrder()->first(); //mengambil data 1 secara random
            Registration::create([
                'user_id' => $user->id,
                'course_id' => $courses->id,
                'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            ]);
        }
    }
}
