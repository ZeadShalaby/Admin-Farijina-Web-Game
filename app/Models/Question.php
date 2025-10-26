<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function getImageUrlAttribute($value)
    {
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        return $value ? url($value) : null; // Ensure $value is a valid path
    }
    // public function getImageAttribute($value)
    // {
    //     if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
    //         return $value;
    //     }
    //     return $value ? url($value) : null; // Ensure $value is a valid path
    // }
    public function getPointsAttribute($value)
    {
        return (int) $value;
    }
    public function getLinkQuestionAttribute($value)
    {
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        return $value ? url("storage/" . $value) : null; // Ensure $value is a valid path
    }

    public function getLinkAnswerAttribute($value)
    {
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        return $value ? url("storage/" . $value) : null; // Ensure $value is a valid path
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function question()
    {
        return $this->hasOne(Question::class);
    } 
    public function contactus()
    {
        return $this->hasMany(ContactUs::class);
    }
    public function viewers()
    {
        return $this->belongsToMany(User::class, 'user_question_views')
            ->withTimestamps();
    }
}
