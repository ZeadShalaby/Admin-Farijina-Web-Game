<?php

namespace App\Enums;

enum CategoryFilterMode: int
{
    case NORMAL = 0;
    case DEFAULT = 1;
    case MEDIUM = 2;
    case SPECIAL = 3;



    public function label(): string
    {
        return match ($this) {
            self::NORMAL => 'طبيعي',
            self::DEFAULT => 'ولاكلمة المود العادي',
            self::MEDIUM => 'ولاكلمة المود المتسوط',
            self::SPECIAL => 'ولاكلمة المود المميز',
        };
    }
}
