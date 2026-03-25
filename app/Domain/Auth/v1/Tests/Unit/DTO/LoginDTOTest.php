<?php

declare(strict_types=1);

namespace App\Domain\Auth\v1\Tests\Unit\DTO;

use App\Domain\Auth\v1\DTO\LoginDTO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LoginDTOTest extends TestCase
{
    #[Test]
    public function it_creates_dto_with_correct_properties(): void
    {
        $dto = new LoginDTO(
            email: 'test@example.com',
            password: 'secret',
        );

        $this->assertSame('test@example.com', $dto->email);
        $this->assertSame('secret', $dto->password);
    }

    #[Test]
    public function it_is_readonly(): void
    {
        $dto = new LoginDTO(email: 'test@example.com', password: 'secret');

        $reflection = new \ReflectionClass($dto);
        $this->assertTrue($reflection->isReadonly());
    }
}
