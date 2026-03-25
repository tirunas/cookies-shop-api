<?php

declare(strict_types=1);

namespace App\Domain\Cart\v1\Tests\Integration\Services;

use App\Domain\Cart\v1\DTO\CartPurchaseDTO;
use App\Domain\Cart\v1\Enums\CartProductEnum;
use App\Domain\Cart\v1\Exceptions\InsufficientBalanceException;
use App\Domain\Cart\v1\Services\CartService;
use App\Domain\User\v1\Enums\UserEnum;
use App\Domain\User\v1\Models\User;
use App\Shared\Enums\ModelEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    private CartService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new CartService();
    }

    public function test_purchase_deducts_wallet_balance(): void
    {
        $user = User::factory()->create([UserEnum::wallet->value => 10]);
        $this->actingAs($user);

        Log::shouldReceive('info')->once();

        $dto = new CartPurchaseDTO(product: CartProductEnum::cookies, quantity: 3);

        $this->service->purchase($dto);

        $this->assertDatabaseHas('users', [
            ModelEnum::id->value => $user->id,
            UserEnum::wallet->value => 7,
        ]);
    }

    public function test_purchase_deducts_exact_amount(): void
    {
        $user = User::factory()->create([UserEnum::wallet->value => 1]);
        $this->actingAs($user);

        Log::shouldReceive('info')->once();

        $dto = new CartPurchaseDTO(product: CartProductEnum::cookies, quantity: 1);

        $this->service->purchase($dto);

        $this->assertDatabaseHas('users', [
            ModelEnum::id->value => $user->id,
            UserEnum::wallet->value => 0,
        ]);
    }

    public function test_purchase_throws_exception_when_insufficient_balance(): void
    {
        $user = User::factory()->create([UserEnum::wallet->value => 0]);
        $this->actingAs($user);

        Log::shouldReceive('warning')->once();

        $dto = new CartPurchaseDTO(product: CartProductEnum::cookies, quantity: 1);

        $this->expectException(InsufficientBalanceException::class);

        $this->service->purchase($dto);
    }

    public function test_purchase_does_not_deduct_when_insufficient_balance(): void
    {
        $user = User::factory()->create([UserEnum::wallet->value => 0]);
        $this->actingAs($user);

        Log::shouldReceive('warning')->once();

        $dto = new CartPurchaseDTO(product: CartProductEnum::cookies, quantity: 1);

        try {
            $this->service->purchase($dto);
        } catch (InsufficientBalanceException) {
            // expected
        }

        $this->assertDatabaseHas('users', [
            ModelEnum::id->value => $user->id,
            UserEnum::wallet->value => 0,
        ]);
    }

    public function test_purchase_logs_successful_purchase(): void
    {
        $user = User::factory()->create([UserEnum::wallet->value => 100, UserEnum::email->value => 'buyer@example.com']);
        $this->actingAs($user);

        Log::shouldReceive('info')
            ->once()
            ->withArgs(fn (string $message) => str_contains($message, 'buyer@example.com')
                && str_contains($message, '2 cookies'));

        $dto = new CartPurchaseDTO(product: CartProductEnum::cookies, quantity: 2);

        $this->service->purchase($dto);
    }

    public function test_purchase_logs_failed_purchase(): void
    {
        $user = User::factory()->create([UserEnum::wallet->value => 0, UserEnum::email->value => 'broke@example.com']);
        $this->actingAs($user);

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(fn (string $message) => str_contains($message, 'broke@example.com'));

        $dto = new CartPurchaseDTO(product: CartProductEnum::cookies, quantity: 1);

        try {
            $this->service->purchase($dto);
        } catch (InsufficientBalanceException) {
            // expected
        }
    }
}
