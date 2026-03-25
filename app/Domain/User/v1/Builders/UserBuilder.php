<?php

declare(strict_types=1);

namespace App\Domain\User\v1\Builders;

use App\Domain\User\v1\Enums\UserEnum;
use App\Domain\User\v1\Models\User;
use App\Shared\Base\BaseBuilder;
use App\Shared\Enums\SqlOperator;

/** @extends BaseBuilder<User> */
class UserBuilder extends BaseBuilder
{
    public function byEmail(string|array|null $value, SqlOperator $operator = SqlOperator::equals): static
    {
        return $this->byField(UserEnum::email, $value, $operator);
    }

    public function byWallet(int|float|array|null $value = null, SqlOperator $operator = SqlOperator::equals): self
    {
        return $this->byField(UserEnum::wallet, $value, $operator);
    }
}
