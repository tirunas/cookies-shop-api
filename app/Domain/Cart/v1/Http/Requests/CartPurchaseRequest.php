<?php

declare(strict_types=1);

namespace App\Domain\Cart\v1\Http\Requests;

use App\Domain\Cart\v1\DTO\CartPurchaseDTO;
use App\Domain\Cart\v1\Enums\CartProductEnum;
use App\Shared\Base\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CartPurchaseRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'product' => [
                'required',
                'string',
                Rule::enum(CartProductEnum::class),
            ],
            'quantity' => [
                'required',
                'int',
                'min:1',
            ]
        ];
    }

    public function after(): array
    {
        return [
            $this->validateWalletBalance(...),
        ];
    }


    public function getDTO(): CartPurchaseDTO
    {
        return new CartPurchaseDTO(
            product: $this->getProduct(),
            quantity: $this->input('quantity'),
        );
    }

    private function getProduct(): CartProductEnum
    {
        return CartProductEnum::from($this->input('product'));
    }

    private function validateWalletBalance(Validator $validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $dto = $this->getDTO();
        $wallet = $this->getAuthUser()->wallet;

        if ($dto->getRequestedAmount() > $wallet) {
            $maxQuantity = floor($wallet / $dto->product->price());

            $validator->errors()->add(
                'quantity',
                "Insufficient balance. You have {$wallet} in your wallet and can purchase up to {$maxQuantity} of this product."
            );
        }
    }
}
