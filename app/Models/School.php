<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{HasMany, HasOne};

class School extends Model
{
    protected $fillable = [
        'name', 'city', 'initial', 'student_count', 'plan', 'is_active', 'joined_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'joined_at' => 'date',
    ];

    public function users(): HasMany  { return $this->hasMany(User::class); }
    public function posts(): HasMany  { return $this->hasMany(Post::class); }
    public function groups(): HasMany { return $this->hasMany(Group::class); }
    public function events(): HasMany { return $this->hasMany(Event::class); }

    public function director(): HasOne
    {
        return $this->hasOne(User::class)->where('role', 'director')->latestOfMany();
    }
}
