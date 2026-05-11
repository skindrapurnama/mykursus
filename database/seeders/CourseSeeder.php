<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        Course::insert([
            [
                'title' => 'Laravel Dasar',
                'description' => 'Belajar Laravel dari nol',
                'price' => 500000,
                'quota' => 30,
                'start_date' => now(),
                'end_date' => now()->addDays(7),
                'is_comment_enabled' => true,
            ],
            [
                'title' => 'React Native',
                'description' => 'Mobile App Development',
                'price' => 750000,
                'quota' => 25,
                'start_date' => now(),
                'end_date' => now()->addDays(10),
                'is_comment_enabled' => true,
            ],
            [
                'title' => 'Python untuk Data Science',
                'description' => 'Belajar Python untuk analisis data',
                'price' => 600000,
                'quota' => 20,
                'start_date' => now(),
                'end_date' => now()->addDays(14),
                'is_comment_enabled' => true,
            ],
            [
                'title' => 'UI/UX Design',
                'description' => 'Belajar desain antarmuka dan pengalaman pengguna',
                'price' => 550000,
                'quota' => 15,
                'start_date' => now(),
                'end_date' => now()->addDays(12),
                'is_comment_enabled' => true,
            ],
            [
                'title' => 'Machine Learning dengan Python',
                'description' => 'Belajar machine learning menggunakan Python',
                'price' => 800000,
                'quota' => 10,
                'start_date' => now(),
                'end_date' => now()->addDays(20),
                'is_comment_enabled' => true,
            ],
            [
                'title' => 'Web Development dengan Django',
                'description' => 'Belajar web development menggunakan Django',
                'price' => 650000,
                'quota' => 20,
                'start_date' => now(),
                'end_date' => now()->addDays(15),
                'is_comment_enabled' => true,
            ],
            [
                'title' => 'Mobile App Development dengan Flutter',
                'description' => 'Belajar mobile app development menggunakan Flutter',
                'price' => 700000,
                'quota' => 25,
                'start_date' => now(),
                'end_date' => now()->addDays(18),
                'is_comment_enabled' => true,
            ],
            [
                'title' => 'Data Analysis dengan R',
                'description' => 'Belajar analisis data menggunakan R',
                'price' => 550000,
                'quota' => 15,
                'start_date' => now(),
                'end_date' => now()->addDays(10),
                'is_comment_enabled' => true,
            ],
            [
                'title' => 'Cybersecurity untuk Pemula',
                'description' => 'Belajar dasar-dasar cybersecurity',
                'price' => 600000,
                'quota' => 20,
                'start_date' => now(),
                'end_date' => now()->addDays(14),
                'is_comment_enabled' => true,
            ],
            [
                'title' => 'Cloud Computing dengan AWS',
                'description' => 'Belajar cloud computing menggunakan AWS',
                'price' => 750000,
                'quota' => 30,
                'start_date' => now(),
                'end_date' => now()->addDays(21),
                'is_comment_enabled' => true,
            ],

        ]);
    }
}
