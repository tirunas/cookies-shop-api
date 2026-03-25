<?php

declare(strict_types=1);

namespace App\Shared\Tests\Unit\Enums;

use App\Shared\Enums\SqlOperator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SqlOperatorTest extends TestCase
{
    #[Test]
    public function it_has_correct_operator_values(): void
    {
        $this->assertSame('=', SqlOperator::equals->value);
        $this->assertSame('!=', SqlOperator::notEquals->value);
        $this->assertSame('>', SqlOperator::gt->value);
        $this->assertSame('>=', SqlOperator::gte->value);
        $this->assertSame('<', SqlOperator::lt->value);
        $this->assertSame('<=', SqlOperator::lte->value);
        $this->assertSame('IN', SqlOperator::in->value);
        $this->assertSame('NOT IN', SqlOperator::notIn->value);
        $this->assertSame('IS NULL', SqlOperator::isNull->value);
        $this->assertSame('IS NOT NULL', SqlOperator::isNotNull->value);
        $this->assertSame('LIKE', SqlOperator::like->value);
        $this->assertSame('BETWEEN', SqlOperator::between->value);
    }

    #[Test]
    public function it_has_all_expected_cases(): void
    {
        $this->assertCount(12, SqlOperator::cases());
    }
}
