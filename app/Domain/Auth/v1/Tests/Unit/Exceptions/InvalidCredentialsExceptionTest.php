<?php

declare(strict_types=1);

namespace App\Domain\Auth\v1\Tests\Unit\Exceptions;

use App\Domain\Auth\v1\Exceptions\InvalidCredentialsException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidCredentialsExceptionTest extends TestCase
{
    #[Test]
    public function it_has_unauthorized_status_code(): void
    {
        $exception = new InvalidCredentialsException();

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $exception->getStatusCode());
    }

    #[Test]
    public function it_has_correct_message(): void
    {
        $exception = new InvalidCredentialsException();

        $this->assertSame('Invalid credentials.', $exception->getMessage());
    }

    #[Test]
    public function it_extends_http_exception(): void
    {
        $exception = new InvalidCredentialsException();

        $this->assertInstanceOf(HttpException::class, $exception);
    }
}
