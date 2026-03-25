<?php

declare(strict_types=1);

namespace App\Domain\User\v1\Enums;

enum UserEnum: string
{
    case name = 'name';
    case email = 'email';
    case emailVerifiedAt = 'email_verified_at';
    case password = 'password';
    case wallet = 'wallet';
    case rememberToken = 'remember_token';
    case createdAt = 'created_at';
    case updatedAt = 'updated_at';
}
