<?php

declare(strict_types=1);

namespace App\Domain\Cart\v1\DTO;

use App\Domain\Cart\v1\Enums\CartProductEnum;
use App\Shared\Base\BaseDTO;

readonly class CartPurchaseDTO extends BaseDTO
{
    public function __construct(
        public CartProductEnum $product,
        public int $quantity,
    )
    {
    }

    public function getRequestedAmount(): float
    {
        return $this->quantity * $this->product->price();
    }
}
