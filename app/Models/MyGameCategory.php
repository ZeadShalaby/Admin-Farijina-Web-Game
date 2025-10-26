<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyGameCategory extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function gameCategories()
    {
        return $this->hasMany(MyGameCategory::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function game()
    {
        return $this->belongsTo(MyGame::class, 'my_game_id');
    }
}
