<?php

declare(strict_types=1);

namespace App\Domain\Cart\v1\Tests\Unit\Exceptions;

use App\Domain\Cart\v1\Exceptions\InsufficientBalanceException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InsufficientBalanceExceptionTest extends TestCase
{
    #[Test]
    public function it_has_bad_request_status_code(): void
    {
        $exception = new InsufficientBalanceException();

        $this->assertSame(Response::HTTP_BAD_REQUEST, $exception->getStatusCode());
    }

    #[Test]
    public function it_has_correct_message(): void
    {
        $exception = new InsufficientBalanceException();

        $this->assertSame('Insufficient balance.', $exception->getMessage());
    }

    #[Test]
    public function it_extends_http_exception(): void
    {
        $exception = new InsufficientBalanceException();

        $this->assertInstanceOf(HttpException::class, $exception);
    }
}
