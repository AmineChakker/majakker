<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};

class Poll extends Model {
    protected $fillable=['attachment_id','question','ends_at'];
    protected function casts():array{return ['ends_at'=>'datetime'];}
    public function attachment():BelongsTo{return $this->belongsTo(PostAttachment::class,'attachment_id');}
    public function options():HasMany{return $this->hasMany(PollOption::class)->orderBy('sort_order');}
    public function votes():HasMany{return $this->hasMany(PollVote::class);}
    public function userHasVoted(int $userId):bool{return $this->votes()->where('user_id',$userId)->exists();}
    public function getTotalVotesAttribute():int{return $this->options->sum('votes_count');}
    public function getDaysRemainingAttribute():string{
        if(!$this->ends_at) return '∞';
        $d=$this->ends_at->diffInDays(now(),false);
        if($d<0) return abs($d).' jours restants';
        return 'Terminé';
    }
}
