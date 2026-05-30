<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,BelongsToMany,HasMany};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasFactory, Notifiable;
    protected $fillable = ['name','email','password','role','school_id','avatar_path','bio','location','joined_at','notifications_count','is_active','google_id'];
    protected $hidden = ['password','remember_token'];
    protected function casts(): array {
        return ['email_verified_at'=>'datetime','password'=>'hashed','is_active'=>'boolean','joined_at'=>'date'];
    }
    public function school(): BelongsTo { return $this->belongsTo(School::class); }
    public function posts(): HasMany { return $this->hasMany(Post::class); }
    public function reactions(): HasMany { return $this->hasMany(Reaction::class); }
    public function comments(): HasMany { return $this->hasMany(Comment::class); }
    public function groups(): BelongsToMany { return $this->belongsToMany(Group::class,'group_members')->withPivot('role')->withTimestamps(); }
    public function sentMessages(): HasMany { return $this->hasMany(Message::class,'sender_id'); }
    public function receivedMessages(): HasMany { return $this->hasMany(Message::class,'recipient_id'); }
    public function badges(): HasMany { return $this->hasMany(Badge::class); }
    public function isStudent():bool{return $this->role==='student';}
    public function isTeacher():bool{return $this->role==='teacher';}
    public function isDirector():bool{return $this->role==='director';}
    public function isAdmin():bool{return $this->role==='admin';}
    public function canModerate():bool{return in_array($this->role,['director','admin']);}
    public function hasReacted(Post $post,string $type='like'):bool{
        return $this->reactions()->where('post_id',$post->id)->where('type',$type)->exists();
    }
    public function getInitialsAttribute():string{
        return collect(explode(' ',$this->name))->map(fn($p)=>mb_strtoupper(mb_substr($p,0,1)))->take(2)->implode('');
    }
    public function getAvatarColorsAttribute():array{
        $tones=[['#F4EFE6','#5C5346'],['#E8ECFF','#2A3FB8'],['#FAF1DD','#8A6520'],['#F8E7DD','#8E4A2E'],['#DEEFEC','#2D6B61'],['#ECE4D6','#3E342A']];
        $hash=0; foreach(str_split($this->name) as $c){$hash=(($hash*31)+ord($c))&0x7FFFFFFF;}
        return $tones[$hash%count($tones)];
    }
    public function getRoleLabelAttribute():string{
        return match($this->role){'student'=>'Étudiant','teacher'=>'Professeur','director'=>'Directeur','admin'=>'Super admin',default=>$this->role};
    }
    public function getHandleAttribute():string{return strtolower(str_replace(' ','.',$this->name));}
    public function getShortNameAttribute():string{
        $parts=explode(' ',$this->name);
        return $parts[0].(isset($parts[1])?' '.mb_strtoupper(mb_substr($parts[1],0,1)).'.' :'');
    }
}
