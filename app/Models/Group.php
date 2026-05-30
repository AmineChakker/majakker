<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,BelongsToMany,HasMany};
class Group extends Model {
    protected $fillable=['school_id','name','kind','color','teacher_id','description','member_count'];
    public function school():BelongsTo{return $this->belongsTo(School::class);}
    public function teacher():BelongsTo{return $this->belongsTo(User::class,'teacher_id');}
    public function members():BelongsToMany{return $this->belongsToMany(User::class,'group_members')->withPivot('role')->withTimestamps();}
    public function posts():HasMany{return $this->hasMany(Post::class);}
    public function getColorVarAttribute():string{return "var(--c-{$this->color})";}
}
