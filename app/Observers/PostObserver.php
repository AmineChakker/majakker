<?php
namespace App\Observers;

use App\Models\{Post, Hashtag};

class PostObserver
{
    public function created(Post $post): void
    {
        $this->syncHashtags($post);
    }

    public function updated(Post $post): void
    {
        if ($post->wasChanged('body')) {
            // Detach old hashtags and decrement
            foreach ($post->hashtags as $tag) {
                $tag->decrement('posts_count');
            }
            $post->hashtags()->detach();
            $this->syncHashtags($post);
        }
    }

    public function deleted(Post $post): void
    {
        foreach ($post->hashtags as $tag) {
            $tag->decrement('posts_count');
        }
    }

    private function syncHashtags(Post $post): void
    {
        preg_match_all('/#([\w\x{00C0}-\x{024F}]+)/u', $post->body, $matches);
        foreach (array_unique($matches[1]) as $name) {
            $tag = Hashtag::firstOrCreate(['name' => mb_strtolower($name)]);
            $tag->increment('posts_count');
            $post->hashtags()->syncWithoutDetaching([$tag->id]);
        }
    }
}
