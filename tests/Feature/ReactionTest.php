<?php

namespace Tests\Feature;

use App\Models\{User, School, Post};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReactionTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;
    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();
        $school     = School::create(['name' => 'Test School', 'city' => 'Casablanca', 'initial' => 'T', 'plan' => 'pro', 'is_active' => true]);
        $this->user = User::factory()->create(['school_id' => $school->id, 'role' => 'student']);
        $this->post = Post::create(['user_id' => $this->user->id, 'school_id' => $school->id, 'body' => 'React to me', 'visibility' => 'school']);
    }

    public function test_user_can_like_a_post(): void
    {
        $this->actingAs($this->user)
            ->postJson("/posts/{$this->post->id}/react", ['type' => 'like'])
            ->assertJson(['reacted' => true]);

        $this->assertDatabaseHas('reactions', ['user_id' => $this->user->id, 'post_id' => $this->post->id, 'type' => 'like']);
    }

    public function test_liking_twice_toggles_reaction(): void
    {
        $this->actingAs($this->user)->postJson("/posts/{$this->post->id}/react", ['type' => 'like']);
        $this->actingAs($this->user)->postJson("/posts/{$this->post->id}/react", ['type' => 'like'])
            ->assertJson(['reacted' => false]);

        $this->assertDatabaseMissing('reactions', ['user_id' => $this->user->id, 'post_id' => $this->post->id]);
    }

    public function test_invalid_reaction_type_rejected(): void
    {
        $this->actingAs($this->user)
            ->postJson("/posts/{$this->post->id}/react", ['type' => 'invalid'])
            ->assertStatus(422);
    }
}
