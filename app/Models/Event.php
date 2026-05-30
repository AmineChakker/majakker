<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Event extends Model {
    protected $fillable=['school_id','created_by','title','description','starts_at','location','color'];
    protected function casts():array{return ['starts_at'=>'datetime'];}
    public function school():BelongsTo{return $this->belongsTo(School::class);}
    public function creator():BelongsTo{return $this->belongsTo(User::class,'created_by');}
    public function getColorVarAttribute():string{return "var(--c-{$this->color})";}
    public function getColorSoftVarAttribute():string{return "var(--c-{$this->color}-soft)";}
}
