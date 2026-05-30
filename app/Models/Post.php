<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,BelongsToMany,HasMany,HasOne};
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model {
    use SoftDeletes;
    protected $fillable = ['user_id','school_id','group_id','title','body','is_pinned','is_announcement','visibility','ai_score','ai_flagged','likes_count','comments_count','sparks_count'];
    protected function casts():array{return ['is_pinned'=>'boolean','is_announcement'=>'boolean','ai_flagged'=>'boolean'];}
    public function user():BelongsTo{return $this->belongsTo(User::class);}
    public function school():BelongsTo{return $this->belongsTo(School::class);}
    public function group():BelongsTo{return $this->belongsTo(Group::class);}
    public function attachments():HasMany{return $this->hasMany(PostAttachment::class)->orderBy('sort_order');}
    public function reactions():HasMany{return $this->hasMany(Reaction::class);}
    public function comments():HasMany{return $this->hasMany(Comment::class)->whereNull('parent_id')->with('user','replies.user')->latest();}
    public function hashtags():BelongsToMany{return $this->belongsToMany(Hashtag::class,'post_hashtag');}
    public function moderationReport():HasOne{return $this->hasOne(ModerationReport::class);}
    public function getWhereAttribute():string{
        if($this->group) return $this->group->name;
        if($this->is_announcement) return 'Annonce école';
        return 'École';
    }
    public function getTimeAgoAttribute():string{
        $diff=$this->created_at->diffInMinutes(now());
        if($diff<1) return 'à l\'instant';
        if($diff<60) return "il y a {$diff} min";
        $h=round($diff/60);
        if($h<24) return "il y a {$h} h";
        if($h<48) return 'hier';
        return $this->created_at->format('d M');
    }
}
