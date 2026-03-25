<?php

declare(strict_types=1);

namespace App\Domain\Auth\v1\Services;

use App\Domain\Auth\v1\DTO\LoginDTO;
use App\Domain\Auth\v1\Exceptions\InvalidCredentialsException;
use App\Domain\User\v1\Enums\UserEnum;
use App\Domain\User\v1\Models\User;
use App\Shared\Base\BaseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService extends BaseService
{
    public function login(LoginDTO $dto): string
    {
        $user = User::query()->byEmail($dto->email)->first();

        if (!$user || !Hash::check($dto->password, $user->password)) {
            Log::warning(sprintf('Failed login attempt for email: %s', $dto->email));

            throw new InvalidCredentialsException();
        }

        Log::info(sprintf('User %s logged in successfully', $user->email));

        return $user->createToken('api')->plainTextToken;
    }
}
