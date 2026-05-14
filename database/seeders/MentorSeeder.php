<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Mentor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MentorSeeder extends Seeder
{
    public function run(): void
    {
        $mentorProfiles = [
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi.mentor@mykursus.test',
                'bio' => 'Senior backend engineer dengan pengalaman membangun aplikasi skala besar berbasis Laravel. Berfokus pada arsitektur bersih, performa database, dan testing yang andal.',
                'expertise' => ['Laravel', 'PHP', 'API Development', 'Database Design'],
                'linkedin_url' => 'https://linkedin.com/in/andi-wijaya',
                'years_experience' => 10,
            ],
            [
                'name' => 'Sari Pratiwi',
                'email' => 'sari.mentor@mykursus.test',
                'bio' => 'Frontend specialist yang gemar mengeksplorasi React dan TypeScript. Praktisi UI/UX yang percaya bahwa antarmuka terbaik adalah yang tak terasa.',
                'expertise' => ['React', 'TypeScript', 'UI/UX', 'Tailwind CSS'],
                'linkedin_url' => 'https://linkedin.com/in/sari-pratiwi',
                'years_experience' => 7,
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.mentor@mykursus.test',
                'bio' => 'Data scientist yang aktif di industri fintech. Mengajar dengan pendekatan praktis lewat studi kasus dataset nyata.',
                'expertise' => ['Python', 'Data Science', 'Database Design'],
                'linkedin_url' => 'https://linkedin.com/in/budi-santoso',
                'years_experience' => 8,
            ],
            [
                'name' => 'Nadia Rahman',
                'email' => 'nadia.mentor@mykursus.test',
                'bio' => 'Mobile developer yang berfokus pada cross-platform development. Menguasai Flutter dan React Native untuk produk consumer.',
                'expertise' => ['Mobile Development', 'React', 'TypeScript'],
                'linkedin_url' => 'https://linkedin.com/in/nadia-rahman',
                'years_experience' => 6,
            ],
            [
                'name' => 'Rizky Permana',
                'email' => 'rizky.mentor@mykursus.test',
                'bio' => 'DevOps engineer yang berpengalaman menyiapkan pipeline CI/CD dan infrastruktur cloud untuk startup hingga enterprise.',
                'expertise' => ['DevOps', 'Node.js', 'API Development'],
                'linkedin_url' => 'https://linkedin.com/in/rizky-permana',
                'years_experience' => 9,
            ],
        ];

        $courses = Course::all();

        if ($courses->isEmpty()) {
            $this->command->warn('Tidak ada kursus. Lewati attach mentor ke kursus.');
        }

        foreach ($mentorProfiles as $profile) {
            $user = User::firstOrCreate(
                ['email' => $profile['email']],
                [
                    'name' => $profile['name'],
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_INSTRUCTOR,
                ],
            );

            $user->update(['role' => User::ROLE_INSTRUCTOR]);

            $mentor = Mentor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'bio' => $profile['bio'],
                    'expertise' => $profile['expertise'],
                    'linkedin_url' => $profile['linkedin_url'],
                    'years_experience' => $profile['years_experience'],
                    'is_active' => true,
                ],
            );

            if ($courses->isNotEmpty()) {
                $attachCount = min(rand(1, 3), $courses->count());
                $pickedCourses = $courses->random($attachCount);
                $pickedCourses = $pickedCourses instanceof Course ? collect([$pickedCourses]) : $pickedCourses;

                $sync = [];
                $isFirst = true;
                foreach ($pickedCourses as $course) {
                    $sync[$course->id] = [
                        'role' => $isFirst ? 'primary' : 'co-mentor',
                    ];
                    $isFirst = false;
                }

                $mentor->courses()->syncWithoutDetaching($sync);
            }
        }

        $this->command->info('5 mentor berhasil di-seed. Login: andi.mentor@mykursus.test / password');
    }
}
