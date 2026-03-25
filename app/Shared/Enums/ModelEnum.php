<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum ModelEnum: string
{
    case id = 'id';
    case createdAt = 'created_at';
    case updatedAt = 'updated_at';
}
