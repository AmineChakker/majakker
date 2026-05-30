<?php

namespace Tests\Feature;

use App\Models\{User, School};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RoleGateTest extends TestCase
{
    use DatabaseTransactions;

    private School $school;
    private User $student;
    private User $teacher;
    private User $director;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->school   = School::create(['name' => 'Test School', 'city' => 'Casablanca', 'initial' => 'T', 'plan' => 'pro', 'is_active' => true]);
        $this->student  = User::factory()->create(['school_id' => $this->school->id, 'role' => 'student']);
        $this->teacher  = User::factory()->create(['school_id' => $this->school->id, 'role' => 'teacher']);
        $this->director = User::factory()->create(['school_id' => $this->school->id, 'role' => 'director']);
        $this->admin    = User::factory()->create(['school_id' => null, 'role' => 'admin']);
    }

    public function test_student_cannot_access_dashboard(): void
    {
        $this->actingAs($this->student)->get('/dashboard')->assertStatus(403);
    }

    public function test_teacher_cannot_access_dashboard(): void
    {
        $this->actingAs($this->teacher)->get('/dashboard')->assertStatus(403);
    }

    public function test_director_can_access_dashboard(): void
    {
        $this->actingAs($this->director)->get('/dashboard')->assertStatus(200);
    }

    public function test_student_cannot_access_admin(): void
    {
        $this->actingAs($this->student)->get('/admin')->assertStatus(403);
    }

    public function test_director_cannot_access_admin(): void
    {
        $this->actingAs($this->director)->get('/admin')->assertStatus(403);
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $this->actingAs($this->admin)->get('/admin')->assertStatus(200);
    }

    public function test_guest_cannot_access_feed(): void
    {
        $this->get('/feed')->assertRedirect('/login');
    }

    public function test_student_can_access_feed(): void
    {
        $this->actingAs($this->student)->get('/feed')->assertStatus(200);
    }
}
