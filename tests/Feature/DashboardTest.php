<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_sees_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_dashboard_shows_correct_counts_for_user(): void
    {
        $user = User::factory()->create();

        Registration::factory()->count(3)->create(['user_id' => $user->id]);
        Certificate::factory()->count(2)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('coursesCount', 3);
        $response->assertViewHas('certificatesCount', 2);
    }

    public function test_dashboard_does_not_leak_other_users_data(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $otherCourse = Course::factory()->create(['title' => 'KURSUS USER LAIN XYZ']);
        Registration::factory()->create([
            'user_id' => $userB->id,
            'course_id' => $otherCourse->id,
            'status' => 'approved',
        ]);

        $this->actingAs($userA)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('KURSUS USER LAIN XYZ');
    }

    public function test_continue_learning_only_shows_approved_registrations_max_3(): void
    {
        $user = User::factory()->create();

        Registration::factory()->count(5)->create([
            'user_id' => $user->id,
            'status' => 'approved',
        ]);
        Registration::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $continueLearning = $response->viewData('continueLearning');
        $this->assertCount(3, $continueLearning);
        $this->assertTrue($continueLearning->every(fn ($r) => $r->status === 'approved'));
    }

    public function test_activities_section_caps_at_5_items_newest_first(): void
    {
        $user = User::factory()->create();

        Registration::factory()->count(8)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $activities = $response->viewData('activities');
        $this->assertLessThanOrEqual(5, $activities->count());

        $timestamps = $activities->pluck('timestamp')->all();
        $sorted = collect($timestamps)->sortByDesc(fn ($t) => $t->timestamp)->values()->all();
        $this->assertEquals($timestamps, $sorted);
    }
}
