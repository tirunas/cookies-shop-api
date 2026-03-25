<?php

declare(strict_types=1);

namespace App\Shared\Tests\Unit\Enums;

use App\Shared\Enums\ModelEnum;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ModelEnumTest extends TestCase
{
    #[Test]
    public function it_has_correct_column_values(): void
    {
        $this->assertSame('id', ModelEnum::id->value);
        $this->assertSame('created_at', ModelEnum::createdAt->value);
        $this->assertSame('updated_at', ModelEnum::updatedAt->value);
    }

    #[Test]
    public function it_has_all_expected_cases(): void
    {
        $this->assertCount(3, ModelEnum::cases());
    }
}
