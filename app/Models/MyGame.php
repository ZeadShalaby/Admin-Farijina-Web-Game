<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyGame extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type_of_game',
        'name_first_player',
        'name_second_player',
        'num_first_player',
        'num_second_player',
        'num_of_play',
        'first_player_no_answer',
        'first_player_al_jleeb',
        'first_player_tow_answer',
        'second_player_no_answer',
        'second_player_al_jleeb',
        'second_player_tow_answer',
        'first_player_vertebrae_one',
        'first_player_vertebrae_two',
        'second_player_vertebrae_one',
        'second_player_vertebrae_two',
        'first_player_points',
        'user_id',
        'is_free',
        'second_player_points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function categories()
    {
        return $this->hasMany(MyGameCategory::class);
    }
    public function userQuestionViews()
    {
        return $this->hasMany(UserQuestionView::class, 'my_game_id');
    }
}
