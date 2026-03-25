<?php

declare(strict_types=1);

namespace App\Domain\Cart\v1\Tests\Unit\DTO;

use App\Domain\Cart\v1\DTO\CartPurchaseDTO;
use App\Domain\Cart\v1\Enums\CartProductEnum;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CartPurchaseDTOTest extends TestCase
{
    #[Test]
    public function it_creates_dto_with_correct_properties(): void
    {
        $dto = new CartPurchaseDTO(
            product: CartProductEnum::cookies,
            quantity: 3,
        );

        $this->assertSame(CartProductEnum::cookies, $dto->product);
        $this->assertSame(3, $dto->quantity);
    }

    #[Test]
    public function it_calculates_requested_amount(): void
    {
        $dto = new CartPurchaseDTO(
            product: CartProductEnum::cookies,
            quantity: 3,
        );

        $this->assertSame(3.0, $dto->getRequestedAmount());
    }

    #[Test]
    public function it_calculates_requested_amount_for_single_item(): void
    {
        $dto = new CartPurchaseDTO(
            product: CartProductEnum::cookies,
            quantity: 1,
        );

        $this->assertSame(1.0, $dto->getRequestedAmount());
    }

    #[Test]
    public function it_is_readonly(): void
    {
        $dto = new CartPurchaseDTO(
            product: CartProductEnum::cookies,
            quantity: 1,
        );

        $reflection = new \ReflectionClass($dto);
        $this->assertTrue($reflection->isReadonly());
    }
}
