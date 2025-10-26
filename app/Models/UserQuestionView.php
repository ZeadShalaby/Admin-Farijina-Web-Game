<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuestionView extends Model
{
    protected $table = 'user_question_views';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    public function game()
    {
        return $this->belongsTo(MyGame::class, 'my_game_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
