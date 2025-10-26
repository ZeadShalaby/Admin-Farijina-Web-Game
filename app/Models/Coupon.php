<?php

namespace App\Models;

use App\Models\Company;
use App\Models\CouponUsage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Coupon.php Model
    public function usages()
    {
        return $this->hasMany(CouponUsage::class, 'coupon_id');
    }



    // Relationship with Section
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}
