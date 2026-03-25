<?php

declare(strict_types=1);

namespace App\Domain\Cart\v1\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InsufficientBalanceException extends HttpException
{
    public function __construct()
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, 'Insufficient balance.');
    }
}
