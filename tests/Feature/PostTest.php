<?php

namespace Tests\Feature;

use App\Models\{User, School, Post};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PostTest extends TestCase
{
    use DatabaseTransactions;

    private School $school;
    private User $student;
    private User $teacher;
    private User $director;

    protected function setUp(): void
    {
        parent::setUp();
        $this->school   = School::create(['name' => 'Test School', 'city' => 'Casablanca', 'initial' => 'T', 'plan' => 'pro', 'is_active' => true]);
        $this->student  = User::factory()->create(['school_id' => $this->school->id, 'role' => 'student']);
        $this->teacher  = User::factory()->create(['school_id' => $this->school->id, 'role' => 'teacher']);
        $this->director = User::factory()->create(['school_id' => $this->school->id, 'role' => 'director']);
    }

    public function test_authenticated_user_can_view_feed(): void
    {
        $this->actingAs($this->student)->get('/feed')->assertStatus(200);
    }

    public function test_guest_is_redirected_from_feed(): void
    {
        $this->get('/feed')->assertRedirect('/login');
    }

    public function test_student_can_create_post(): void
    {
        $this->actingAs($this->student)
            ->post('/posts', ['body' => 'Test post body'])
            ->assertRedirect();

        $this->assertDatabaseHas('posts', ['body' => 'Test post body', 'user_id' => $this->student->id]);
    }

    public function test_post_requires_body(): void
    {
        $this->actingAs($this->student)
            ->post('/posts', ['body' => ''])
            ->assertSessionHasErrors('body');
    }

    public function test_post_body_cannot_exceed_5000_chars(): void
    {
        $this->actingAs($this->student)
            ->post('/posts', ['body' => str_repeat('a', 5001)])
            ->assertSessionHasErrors('body');
    }

    public function test_author_can_delete_own_post(): void
    {
        $post = Post::create(['user_id' => $this->student->id, 'school_id' => $this->school->id, 'body' => 'Delete me', 'visibility' => 'school']);
        $this->actingAs($this->student)->delete("/posts/{$post->id}")->assertRedirect();
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_student_cannot_delete_others_post(): void
    {
        $other = User::factory()->create(['school_id' => $this->school->id, 'role' => 'student']);
        $post  = Post::create(['user_id' => $other->id, 'school_id' => $this->school->id, 'body' => 'Not mine', 'visibility' => 'school']);
        $this->actingAs($this->student)->delete("/posts/{$post->id}")->assertStatus(403);
    }

    public function test_director_can_delete_any_post(): void
    {
        $post = Post::create(['user_id' => $this->student->id, 'school_id' => $this->school->id, 'body' => 'Director deletes', 'visibility' => 'school']);
        $this->actingAs($this->director)->delete("/posts/{$post->id}")->assertRedirect();
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_student_cannot_pin_post(): void
    {
        $post = Post::create(['user_id' => $this->student->id, 'school_id' => $this->school->id, 'body' => 'Pin me', 'visibility' => 'school']);
        $this->actingAs($this->student)->post("/posts/{$post->id}/pin")->assertStatus(403);
    }

    public function test_director_can_pin_post(): void
    {
        $post = Post::create(['user_id' => $this->student->id, 'school_id' => $this->school->id, 'body' => 'Pin me', 'visibility' => 'school']);
        $this->actingAs($this->director)->post("/posts/{$post->id}/pin")->assertRedirect();
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'is_pinned' => true]);
    }

    public function test_hashtags_are_extracted_on_create(): void
    {
        $this->actingAs($this->student)->post('/posts', ['body' => 'Hello #majakker and #robotique!']);
        $this->assertDatabaseHas('hashtags', ['name' => 'majakker']);
        $this->assertDatabaseHas('hashtags', ['name' => 'robotique']);
    }
}
