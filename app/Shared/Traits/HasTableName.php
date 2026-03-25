<?php

declare(strict_types=1);

namespace App\Shared\Traits;

trait HasTableName
{
    public static function getTableName(): string
    {
        return (new static)->getTable();
    }
}
