<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ModerationReport extends Model {
    protected $fillable=['post_id','comment_id','reported_by','reason','ai_score','severity','status','reviewed_by','reviewed_at'];
    protected function casts():array{return ['reviewed_at'=>'datetime','ai_score'=>'float'];}
    public function post():BelongsTo{return $this->belongsTo(Post::class);}
    public function reporter():BelongsTo{return $this->belongsTo(User::class,'reported_by');}
    public function reviewer():BelongsTo{return $this->belongsTo(User::class,'reviewed_by');}
    public function getSeverityColorAttribute():string{
        return match($this->severity){'high'=>'terracotta','medium'=>'saffron',default=>'atlas'};
    }
    public function getSeverityLabelAttribute():string{
        return match($this->severity){'high'=>'HAUT','medium'=>'MOY.',default=>'BAS'};
    }
}
