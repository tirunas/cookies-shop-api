<?php

declare(strict_types=1);

namespace App\Domain\Cart\v1\Tests\Feature;

use App\Domain\User\v1\Enums\UserEnum;
use App\Domain\User\v1\Models\User;
use App\Shared\Enums\ModelEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PurchaseEndpointTest extends TestCase
{
    use RefreshDatabase;

    private const string PURCHASE_URL = '/api/v1/cart/purchase';

    public function test_purchase_succeeds_with_sufficient_balance(): void
    {
        $user = User::factory()->create([UserEnum::wallet->value => 10]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(self::PURCHASE_URL, [
                'product' => 'cookies',
                'quantity' => 3,
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'Success, you have bought 3 cookies!',
        ]);
    }

    public function test_purchase_deducts_wallet_balance(): void
    {
        $user = User::factory()->create([UserEnum::wallet->value => 10]);

        $this->actingAs($user, 'sanctum')
            ->postJson(self::PURCHASE_URL, [
                'product' => 'cookies',
                'quantity' => 3,
            ]);

        $this->assertDatabaseHas('users', [
            ModelEnum::id->value => $user->id,
            UserEnum::wallet->value => 7,
        ]);
    }

    public function test_purchase_fails_with_insufficient_balance(): void
    {
        $user = User::factory()->create([UserEnum::wallet->value => 0]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(self::PURCHASE_URL, [
                'product' => 'cookies',
                'quantity' => 1,
            ]);

        $response->assertJsonValidationErrors(['quantity']);
    }

    public function test_purchase_does_not_deduct_when_insufficient_balance(): void
    {
        $user = User::factory()->create([UserEnum::wallet->value => 0]);

        $this->actingAs($user, 'sanctum')
            ->postJson(self::PURCHASE_URL, [
                'product' => 'cookies',
                'quantity' => 1,
            ]);

        $this->assertDatabaseHas('users', [
            ModelEnum::id->value => $user->id,
            UserEnum::wallet->value => 0,
        ]);
    }

    public function test_purchase_requires_authentication(): void
    {
        $response = $this->postJson(self::PURCHASE_URL, [
            'product' => 'cookies',
            'quantity' => 1,
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_purchase_validates_product_is_required(): void
    {
        $user = User::factory()->create([UserEnum::wallet->value => 100]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(self::PURCHASE_URL, [
                'quantity' => 1,
            ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['product']);
    }

    public function test_purchase_validates_quantity_is_required(): void
    {
        $user = User::factory()->create([UserEnum::wallet->value => 100]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(self::PURCHASE_URL, [
                'product' => 'cookies',
            ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['quantity']);
    }

    public function test_purchase_validates_quantity_minimum_is_one(): void
    {
        $user = User::factory()->create([UserEnum::wallet->value => 100]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(self::PURCHASE_URL, [
                'product' => 'cookies',
                'quantity' => 0,
            ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['quantity']);
    }

    public function test_purchase_validates_product_must_be_valid_enum(): void
    {
        $user = User::factory()->create([UserEnum::wallet->value => 100]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(self::PURCHASE_URL, [
                'product' => 'invalid_product',
                'quantity' => 1,
            ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['product']);
    }

    public function test_purchase_wallet_shows_max_affordable_quantity_in_error(): void
    {
        $user = User::factory()->create([UserEnum::wallet->value => 3]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(self::PURCHASE_URL, [
                'product' => 'cookies',
                'quantity' => 5,
            ]);

        $response->assertJsonValidationErrors(['quantity']);
        $response->assertJsonFragment([
            'quantity' => [
                'Insufficient balance. You have 3 in your wallet and can purchase up to 3 of this product.',
            ],
        ]);
    }

    public function test_full_login_and_purchase_flow(): void
    {
        User::factory()->create([
            UserEnum::email->value => 'buyer@example.com',
            UserEnum::password->value => 'password',
            UserEnum::wallet->value => 10,
        ]);

        // Login
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'buyer@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('token');

        // Purchase with token
        $purchaseResponse = $this->postJson(self::PURCHASE_URL, [
            'product' => 'cookies',
            'quantity' => 3,
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $purchaseResponse->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('users', [
            UserEnum::email->value => 'buyer@example.com',
            UserEnum::wallet->value => 7,
        ]);
    }
}
