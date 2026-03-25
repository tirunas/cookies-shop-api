<?php

declare(strict_types=1);

namespace App\Domain\Cart\v1\Services;

use App\Domain\Cart\v1\DTO\CartPurchaseDTO;
use App\Domain\Cart\v1\Exceptions\InsufficientBalanceException;
use App\Domain\User\v1\Enums\UserEnum;
use App\Domain\User\v1\Models\User;
use App\Shared\Base\BaseService;
use App\Shared\Enums\SqlOperator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartService extends BaseService
{
    public function purchase(CartPurchaseDTO $dto): void
    {
        $affected = User::query()
            ->byId($this->getAuthUser()->id)
            ->byWallet($dto->getRequestedAmount(), SqlOperator::gte)
            ->update([
                UserEnum::wallet->value => DB::raw(
                    sprintf('%s - %d', UserEnum::wallet->value, $dto->getRequestedAmount()),
                ),
            ]);

        if ($affected === 0) {
            Log::warning(sprintf(
                'User %s failed to buy %d cookies, insufficient balance',
                $this->getAuthUser()->email,
                $dto->quantity,
            ));

            throw new InsufficientBalanceException();
        }

        Log::info(sprintf('User %s have bought %d cookies', $this->getAuthUser()->email, $dto->quantity));
    }
}
