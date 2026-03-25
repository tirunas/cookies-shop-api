<?php

declare(strict_types = 1);

namespace App\Domain\User\v1\Database\Seeders;

use App\Domain\User\v1\Enums\UserEnum;
use App\Domain\User\v1\Models\User;
use App\Shared\Base\BaseSeeder;

class UserSeeder extends BaseSeeder
{
    public const int DEFAULT_WALLET_AMOUNT = 10;

    public function run(): void
    {
        User::factory()->create([
            UserEnum::email->value => 'test@example.com',
            UserEnum::password->value => 'password',
            UserEnum::wallet->value => self::DEFAULT_WALLET_AMOUNT,
        ]);
    }
}
