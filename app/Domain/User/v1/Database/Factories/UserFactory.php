<?php

declare(strict_types = 1);

namespace App\Domain\User\v1\Database\Factories;

use App\Domain\User\v1\Enums\UserEnum;
use App\Domain\User\v1\Models\User;
use App\Shared\Base\BaseFactory;
use Illuminate\Support\Facades\Hash;

/** @extends BaseFactory<User> */
class UserFactory extends BaseFactory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            UserEnum::name->value => $this->faker->name(),
            UserEnum::email->value => $this->faker->unique()->safeEmail(),
            UserEnum::password->value => Hash::make($this->faker->password()),
            UserEnum::wallet->value => $this->faker->randomNumber(),
        ];
    }
}
