<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Filiere extends Model
{
    protected $fillable = ['school_id','name','code','level','description','color'];

    public function school(): BelongsTo { return $this->belongsTo(School::class); }
    public function classes(): HasMany  { return $this->hasMany(Group::class)->where('kind','class'); }
}
