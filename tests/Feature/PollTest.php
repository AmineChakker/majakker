<?php

namespace Tests\Feature;

use App\Models\{User, School, Post, PostAttachment, Poll, PollOption};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PollTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;
    private Poll $poll;
    private PollOption $optionA;
    private PollOption $optionB;

    protected function setUp(): void
    {
        parent::setUp();
        $school      = School::create(['name' => 'Test School', 'city' => 'Casablanca', 'initial' => 'T', 'plan' => 'pro', 'is_active' => true]);
        $this->user  = User::factory()->create(['school_id' => $school->id, 'role' => 'student']);
        $post        = Post::create(['user_id' => $this->user->id, 'school_id' => $school->id, 'body' => 'Poll!', 'visibility' => 'school']);
        $att         = PostAttachment::create(['post_id' => $post->id, 'kind' => 'poll']);
        $this->poll  = Poll::create(['attachment_id' => $att->id, 'question' => 'Test?']);
        $this->optionA = PollOption::create(['poll_id' => $this->poll->id, 'label' => 'A', 'sort_order' => 0]);
        $this->optionB = PollOption::create(['poll_id' => $this->poll->id, 'label' => 'B', 'sort_order' => 1]);
    }

    public function test_user_can_vote(): void
    {
        $this->actingAs($this->user)
            ->postJson("/polls/{$this->poll->id}/vote", ['option_id' => $this->optionA->id])
            ->assertJson(['voted' => true]);

        $this->assertDatabaseHas('poll_votes', ['poll_id' => $this->poll->id, 'option_id' => $this->optionA->id, 'user_id' => $this->user->id]);
    }

    public function test_user_cannot_vote_twice(): void
    {
        $this->actingAs($this->user)->postJson("/polls/{$this->poll->id}/vote", ['option_id' => $this->optionA->id]);
        $this->actingAs($this->user)
            ->postJson("/polls/{$this->poll->id}/vote", ['option_id' => $this->optionB->id])
            ->assertJson(['already_voted' => true]);

        $this->assertDatabaseCount('poll_votes', 1);
    }
}
