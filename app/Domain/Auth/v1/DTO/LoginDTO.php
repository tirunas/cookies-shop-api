<?php

declare(strict_types=1);

namespace App\Domain\Auth\v1\DTO;

use App\Shared\Base\BaseDTO;

readonly class LoginDTO extends BaseDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}
