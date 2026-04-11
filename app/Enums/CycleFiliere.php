<?php

namespace App\Enums;

enum CycleFiliere: string
{
    case BTS = 'BTS';
    case HND = 'HND';
    case LP = 'LP';

    public function label(): string
    {
        return match($this) {
            self::BTS => 'BTS',
            self::HND => 'HND',
            self::LP => 'Licence Professionnelle',
        };
    }
}
