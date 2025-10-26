<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;
    
    protected $table = 'contact_us'; 
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'question_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the question associated with this complaint.
     */
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    /**
     * Get the user associated with this complaint.
     * This assumes we can match by email or phone with users table
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Try to find associated user by email or phone
     */
    public function findAssociatedUser()
    {
        if ($this->email) {
            return User::where('email', $this->email)->first();
        }
        
        if ($this->phone) {
            return User::where('phone', $this->phone)->first();
        }
        
        return null;
    }

    /**
     * Get user info whether from relationship or by matching email/phone
     */
    public function getUserInfoAttribute()
    {
        if ($this->user) {
            return $this->user;
        }
        
        return $this->findAssociatedUser();
    }
}