<?php
namespace App\Notifications;

use App\Models\ModerationReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ModerationAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ModerationReport $report) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'actor'     => 'Système IA',
            'message'   => 'Nouveau signalement en attente de modération (score IA : ' . number_format($this->report->ai_score ?? 0, 2) . ').',
            'report_id' => $this->report->id,
            'type'      => 'moderation',
        ];
    }
}
