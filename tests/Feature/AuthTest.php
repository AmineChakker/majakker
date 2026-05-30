<?php

namespace Tests\Feature;

use App\Models\{User, School};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    private School $school;

    protected function setUp(): void
    {
        parent::setUp();
        $this->school = School::create([
            'name' => 'Test School', 'city' => 'Casablanca',
            'initial' => 'T', 'plan' => 'pro', 'is_active' => true,
        ]);
    }

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_authenticated_user_redirected_from_login(): void
    {
        $user = User::factory()->create(['school_id' => $this->school->id, 'role' => 'student']);
        $this->actingAs($user)->get('/login')->assertRedirect();
    }

    public function test_student_can_login(): void
    {
        $user = User::factory()->create([
            'school_id' => $this->school->id, 'role' => 'student',
            'password'  => bcrypt('password'),
        ]);
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect();
    }

    public function test_wrong_password_returns_error(): void
    {
        $user = User::factory()->create(['school_id' => $this->school->id]);
        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create(['school_id' => $this->school->id]);
        $this->actingAs($user)->post('/logout')->assertRedirect('/');
    }
}
