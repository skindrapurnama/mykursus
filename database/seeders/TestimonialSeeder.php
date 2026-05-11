<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Testimonial;
use App\Models\Registration;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $registrations = Registration::all();

        foreach ($registrations as $reg) {
            Testimonial::create([
                'user_id' => $reg->user_id,
                'course_id' => $reg->course_id,
                'rating' => rand(3, 5),
                'comment' => fake()->sentence(rand(10, 20)),
                'admin_reply' => 'Terima kasih atas feedbacknya 🙏',
                'is_visible' => true,
            ]);
        }
    }
}
