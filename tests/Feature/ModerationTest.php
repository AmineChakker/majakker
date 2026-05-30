<?php

namespace Tests\Feature;

use App\Models\{User, School, Post, ModerationReport};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModerationTest extends TestCase
{
    use DatabaseTransactions;

    private User $student;
    private User $director;
    private Post $post;
    private ModerationReport $report;

    protected function setUp(): void
    {
        parent::setUp();
        $school          = School::create(['name' => 'Test School', 'city' => 'Casablanca', 'initial' => 'T', 'plan' => 'pro', 'is_active' => true]);
        $this->student   = User::factory()->create(['school_id' => $school->id, 'role' => 'student']);
        $this->director  = User::factory()->create(['school_id' => $school->id, 'role' => 'director']);
        $this->post      = Post::create(['user_id' => $this->student->id, 'school_id' => $school->id, 'body' => 'Flagged', 'visibility' => 'school']);
        $this->report    = ModerationReport::create(['post_id' => $this->post->id, 'reason' => 'Bad content', 'severity' => 'medium', 'status' => 'pending']);
    }

    public function test_student_cannot_access_moderation_queue(): void
    {
        $this->actingAs($this->student)->get('/dashboard/moderation')->assertStatus(403);
    }

    public function test_director_can_view_moderation_queue(): void
    {
        $this->actingAs($this->director)->get('/dashboard/moderation')->assertStatus(200);
    }

    public function test_director_can_approve_report(): void
    {
        $this->actingAs($this->director)
            ->post("/dashboard/moderation/{$this->report->id}/approve")
            ->assertRedirect();

        $this->assertDatabaseHas('moderation_reports', ['id' => $this->report->id, 'status' => 'approved']);
    }

    public function test_director_can_reject_report_and_delete_post(): void
    {
        $this->actingAs($this->director)
            ->post("/dashboard/moderation/{$this->report->id}/reject")
            ->assertRedirect();

        $this->assertDatabaseHas('moderation_reports', ['id' => $this->report->id, 'status' => 'rejected']);
        $this->assertSoftDeleted('posts', ['id' => $this->post->id]);
    }

    public function test_director_can_ignore_report(): void
    {
        $this->actingAs($this->director)
            ->post("/dashboard/moderation/{$this->report->id}/ignore")
            ->assertRedirect();

        $this->assertDatabaseHas('moderation_reports', ['id' => $this->report->id, 'status' => 'ignored']);
    }
}
