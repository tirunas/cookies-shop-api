<?php

declare(strict_types=1);

namespace App\Domain\Auth\v1\Tests\Integration\Services;

use App\Domain\Auth\v1\DTO\LoginDTO;
use App\Domain\Auth\v1\Exceptions\InvalidCredentialsException;
use App\Domain\Auth\v1\Services\AuthService;
use App\Domain\User\v1\Enums\UserEnum;
use App\Domain\User\v1\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AuthService();
    }

    public function test_login_returns_token_with_valid_credentials(): void
    {
        User::factory()->create([
            UserEnum::email->value => 'test@example.com',
            UserEnum::password->value => 'password',
        ]);

        Log::shouldReceive('info')
            ->once()
            ->withArgs(fn (string $message) => str_contains($message, 'logged in successfully'));

        $dto = new LoginDTO(email: 'test@example.com', password: 'password');

        $token = $this->service->login($dto);

        $this->assertNotEmpty($token);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_login_throws_exception_with_wrong_password(): void
    {
        User::factory()->create([
            UserEnum::email->value => 'test@example.com',
            UserEnum::password->value => 'password',
        ]);

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(fn (string $message) => str_contains($message, 'Failed login attempt'));

        $dto = new LoginDTO(email: 'test@example.com', password: 'wrong');

        $this->expectException(InvalidCredentialsException::class);

        $this->service->login($dto);
    }

    public function test_login_throws_exception_with_nonexistent_email(): void
    {
        Log::shouldReceive('warning')
            ->once()
            ->withArgs(fn (string $message) => str_contains($message, 'Failed login attempt'));

        $dto = new LoginDTO(email: 'nobody@example.com', password: 'password');

        $this->expectException(InvalidCredentialsException::class);

        $this->service->login($dto);
    }

    public function test_login_creates_token_named_api(): void
    {
        User::factory()->create([
            UserEnum::email->value => 'test@example.com',
            UserEnum::password->value => 'password',
        ]);

        Log::shouldReceive('info')->once();

        $dto = new LoginDTO(email: 'test@example.com', password: 'password');

        $this->service->login($dto);

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'api',
        ]);
    }
}
