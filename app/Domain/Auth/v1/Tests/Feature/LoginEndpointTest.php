<?php

declare(strict_types=1);

namespace App\Domain\Auth\v1\Tests\Feature;

use App\Domain\User\v1\Enums\UserEnum;
use App\Domain\User\v1\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LoginEndpointTest extends TestCase
{
    use RefreshDatabase;

    private const string LOGIN_URL = '/api/v1/auth/login';

    public function test_login_returns_token_with_valid_credentials(): void
    {
        User::factory()->create([
            UserEnum::email->value => 'test@example.com',
            UserEnum::password->value => 'password',
        ]);

        $response = $this->postJson(self::LOGIN_URL, [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(['token']);
        $this->assertNotEmpty($response->json('token'));
    }

    public function test_login_returns_unauthorized_with_wrong_password(): void
    {
        User::factory()->create([
            UserEnum::email->value => 'test@example.com',
            UserEnum::password->value => 'password',
        ]);

        $response = $this->postJson(self::LOGIN_URL, [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_login_returns_unauthorized_with_nonexistent_email(): void
    {
        $response = $this->postJson(self::LOGIN_URL, [
            'email' => 'nobody@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_login_validates_email_is_required(): void
    {
        $response = $this->postJson(self::LOGIN_URL, [
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_login_validates_password_is_required(): void
    {
        $response = $this->postJson(self::LOGIN_URL, [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_login_validates_email_format(): void
    {
        $response = $this->postJson(self::LOGIN_URL, [
            'email' => 'not-an-email',
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_login_returns_validation_errors_when_body_empty(): void
    {
        $response = $this->postJson(self::LOGIN_URL, []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['email', 'password']);
    }
}
