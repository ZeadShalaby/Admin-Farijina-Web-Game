<?php

namespace App\Models;

use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function couponUsage()
    {
        return $this->hasOne(CouponUsage::class, 'user_id', 'user_id')
            ->latestOfMany(); // لو عايز آخر كوبون استخدمه المستخدم
    }

    public function coupon()
    {
        return $this->hasOneThrough(
            Coupon::class,       // الجدول النهائي اللي فيه بيانات الكوبون
            CouponUsage::class,   // الجدول الوسيط
            'user_id',           // FK في جدول used_coupons يشير لـ transactions.user_id
            'id',                // PK في جدول coupons
            'user_id',           // local key في transactions
            'coupon_id'          // local key في used_coupons
        )->latestOfMany(); // آخر كوبون مستخدم
    }

}
