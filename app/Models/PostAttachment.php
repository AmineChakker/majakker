<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasOne};

class PostAttachment extends Model {
    protected $fillable=['post_id','kind','path','original_name','file_size','mime_type','sort_order'];
    public function post():BelongsTo{return $this->belongsTo(Post::class);}
    public function poll():HasOne{return $this->hasOne(Poll::class,'attachment_id');}
    public function getUrlAttribute():string{
        return $this->path ? asset('storage/'.$this->path) : '';
    }
    public function getFileSizeHumanAttribute():string{
        $b=$this->file_size??0;
        if($b<1024) return $b.' o';
        if($b<1048576) return round($b/1024,1).' Ko';
        return round($b/1048576,1).' Mo';
    }
    public function getExtAttribute():string{
        return strtoupper(pathinfo($this->original_name??'',PATHINFO_EXTENSION))?:'FILE';
    }
}
