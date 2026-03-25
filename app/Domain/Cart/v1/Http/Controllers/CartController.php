<?php

declare(strict_types=1);

namespace App\Domain\Cart\v1\Http\Controllers;

use App\Domain\Cart\v1\Http\Requests\CartPurchaseRequest;
use App\Domain\Cart\v1\Services\CartService;
use App\Shared\Base\BaseController;
use Illuminate\Http\JsonResponse;

class CartController extends BaseController
{
    public function __construct(private readonly CartService $service)
    {
    }

    public function purchase(CartPurchaseRequest $request): JsonResponse
    {
        $this->service->purchase($request->getDTO());

        return new JsonResponse([
            'message' => sprintf('Success, you have bought %d cookies!', $request->getDTO()->quantity),
        ]);
    }
}
