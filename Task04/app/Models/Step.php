<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Step extends Model
{
    protected $fillable = [
        'game_id',
        'user_answer',
        'correct_answer',
        'is_correct',
        'created_at',
    ];

    public $timestamps = false;

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
