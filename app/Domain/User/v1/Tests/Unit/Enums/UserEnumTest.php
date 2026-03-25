<?php

declare(strict_types=1);

namespace App\Domain\User\v1\Tests\Unit\Enums;

use App\Domain\User\v1\Enums\UserEnum;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UserEnumTest extends TestCase
{
    #[Test]
    public function it_has_correct_column_values(): void
    {
        $this->assertSame('name', UserEnum::name->value);
        $this->assertSame('email', UserEnum::email->value);
        $this->assertSame('password', UserEnum::password->value);
        $this->assertSame('wallet', UserEnum::wallet->value);
    }
}
