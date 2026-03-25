<?php

declare(strict_types=1);

namespace App\Domain\Auth\v1\Http\Controllers;

use App\Domain\Auth\v1\Http\Requests\AuthLoginRequest;
use App\Domain\Auth\v1\Services\AuthService;
use App\Shared\Base\BaseController;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseController
{
    public function __construct(private readonly AuthService $service) {}

    public function login(AuthLoginRequest $request): JsonResponse
    {
        $token = $this->service->login($request->getDTO());

        return new JsonResponse([
            'token' => $token,
        ]);
    }
}
