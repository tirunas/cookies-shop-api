<?php

declare(strict_types=1);

namespace App\Domain\Auth\v1\Http\Requests;

use App\Domain\Auth\v1\DTO\LoginDTO;
use App\Shared\Base\BaseRequest;

class AuthLoginRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function getDTO(): LoginDTO
    {
        return new LoginDTO(
            email: $this->input('email'),
            password: $this->input('password'),
        );
    }
}
