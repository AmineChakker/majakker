<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};

class PollOption extends Model {
    public $timestamps=false;
    protected $fillable=['poll_id','label','votes_count','sort_order'];
    public function poll():BelongsTo{return $this->belongsTo(Poll::class);}
    public function votes():HasMany{return $this->hasMany(PollVote::class,'option_id');}
    public function isVotedByUser(int $userId):bool{return $this->votes()->where('user_id',$userId)->exists();}
    public function getPercentageAttribute():int{
        $total=$this->poll->total_votes;
        return $total>0?round(($this->votes_count/$total)*100):0;
    }
}
