<?php
namespace App\Jobs;

use App\Models\{ModerationReport, Post, User};
use App\Notifications\ModerationAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ModeratePost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 30;

    public function __construct(public Post $post) {}

    public function handle(): void
    {
        $apiKey = config('services.openai.key');
        if (!$apiKey || app()->environment('testing')) {
            return;
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(20)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => 'gpt-4o-mini',
                    'temperature' => 0,
                    'max_tokens'  => 60,
                    'messages'    => [
                        ['role' => 'system', 'content' =>
                            'You are a content moderation assistant for a Moroccan school platform. ' .
                            'Posts are in French or Moroccan Arabic (Darija). ' .
                            'Respond ONLY with a JSON object: {"score": <float 0-1>, "reason": "<short reason>"}. ' .
                            'Score 0 = totally safe, 1 = severe violation. ' .
                            'Flag hate speech, sexual content, bullying, or severe off-topic content.'],
                        ['role' => 'user', 'content' => mb_substr($this->post->body, 0, 1000)],
                    ],
                ]);

            $data  = json_decode($response->json('choices.0.message.content', '{}'), true);
            $score = (float) ($data['score'] ?? 0);
            $reason = $data['reason'] ?? 'Analyse IA';

            $this->post->update([
                'ai_score'   => $score,
                'ai_flagged' => $score > 0.5,
            ]);

            if ($score > 0.5) {
                $report = ModerationReport::create([
                    'post_id'  => $this->post->id,
                    'reason'   => $reason,
                    'ai_score' => $score,
                    'severity' => $score > 0.85 ? 'high' : ($score > 0.65 ? 'medium' : 'low'),
                    'status'   => 'pending',
                ]);

                // Auto-hide very harmful content
                if ($score > 0.9) {
                    $this->post->delete();
                }

                // Notify school director(s)
                User::where('school_id', $this->post->school_id)
                    ->where('role', 'director')
                    ->get()
                    ->each(fn($dir) => $dir->notify(new ModerationAlert($report)));
            }
        } catch (\Throwable $e) {
            Log::warning('ModeratePost job failed: ' . $e->getMessage());
        }
    }
}
