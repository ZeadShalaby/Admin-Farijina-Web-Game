<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    protected $casts = [
        'is_active' => 'boolean',
        'is_almost' => 'boolean',
        'end_at' => 'datetime',
    ];
    public function getImageUrlAttribute($value)
    {
        return $value ? url($value) : null;
    }
    public function getImageAttribute($value)
    {
        return $value ? url($value) : null;
    }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    public function gameCategories()
    {
        return $this->hasMany(MyGameCategory::class);
    }
    public function questionsByPoints($points)
    {
        return $this->questions()->where('points', $points)->count();
    } // In the Category Model
    // In the Category Model

    public function usersWhoViewedQuestions()
    {
        return $this->belongsToMany(User::class, 'temp_user_question_views', 'category_id', 'user_id')
            ->join('questions', 'questions.id', '=', 'temp_user_question_views.question_id')
            ->where('temp_user_question_views.category_id', $this->id);
    }
}
