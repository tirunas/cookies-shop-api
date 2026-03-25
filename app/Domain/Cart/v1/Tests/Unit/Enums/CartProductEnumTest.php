<?php

declare(strict_types=1);

namespace App\Domain\Cart\v1\Tests\Unit\Enums;

use App\Domain\Cart\v1\Enums\CartProductEnum;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CartProductEnumTest extends TestCase
{
    #[Test]
    public function cookies_has_correct_value(): void
    {
        $this->assertSame('cookies', CartProductEnum::cookies->value);
    }

    #[Test]
    public function cookies_price_is_one(): void
    {
        $this->assertSame(1.0, CartProductEnum::cookies->price());
    }

    #[Test]
    public function it_can_be_created_from_string(): void
    {
        $product = CartProductEnum::from('cookies');

        $this->assertSame(CartProductEnum::cookies, $product);
    }

    #[Test]
    public function it_returns_null_for_invalid_string(): void
    {
        $product = CartProductEnum::tryFrom('invalid');

        $this->assertNull($product);
    }
}
