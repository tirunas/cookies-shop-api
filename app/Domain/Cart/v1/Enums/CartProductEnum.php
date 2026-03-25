<?php

declare(strict_types=1);

namespace App\Domain\Cart\v1\Enums;

enum CartProductEnum: string
{
    case cookies = 'cookies';

    public function price(): float
    {
        return match($this) {
            self::cookies => 1,
        };
    }
}
