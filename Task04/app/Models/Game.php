<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = [
        'player_name',
        'started_at',
        'expression',
        'correct_answer',
    ];

    public $timestamps = false;

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }
}
