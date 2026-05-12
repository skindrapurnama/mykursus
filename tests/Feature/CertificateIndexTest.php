<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('certificates.index'))
            ->assertRedirect(route('login'));
    }

    public function test_user_sees_only_own_certificates(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $ownCourse = Course::factory()->create(['title' => 'KURSUS SAYA SENDIRI']);
        $foreignCourse = Course::factory()->create(['title' => 'KURSUS USER LAIN']);

        Certificate::factory()->create([
            'user_id' => $userA->id,
            'course_id' => $ownCourse->id,
        ]);
        Certificate::factory()->create([
            'user_id' => $userB->id,
            'course_id' => $foreignCourse->id,
        ]);

        $this->actingAs($userA)
            ->get(route('certificates.index'))
            ->assertOk()
            ->assertSee('KURSUS SAYA SENDIRI')
            ->assertDontSee('KURSUS USER LAIN');
    }
}
