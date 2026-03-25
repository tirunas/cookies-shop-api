<?php

declare(strict_types=1);

namespace App\Domain\User\v1\Tests\Integration\Builders;

use App\Domain\User\v1\Enums\UserEnum;
use App\Domain\User\v1\Models\User;
use App\Shared\Enums\SqlOperator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_by_email_finds_user(): void
    {
        User::factory()->create([UserEnum::email->value => 'find@example.com']);
        User::factory()->create([UserEnum::email->value => 'other@example.com']);

        $user = User::query()->byEmail('find@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('find@example.com', $user->email);
    }

    public function test_by_email_returns_null_when_not_found(): void
    {
        User::factory()->create([UserEnum::email->value => 'exists@example.com']);

        $user = User::query()->byEmail('missing@example.com')->first();

        $this->assertNull($user);
    }

    public function test_by_wallet_with_gte_operator(): void
    {
        User::factory()->create([UserEnum::wallet->value => 100, UserEnum::email->value => 'rich@example.com']);
        User::factory()->create([UserEnum::wallet->value => 5, UserEnum::email->value => 'poor@example.com']);

        $users = User::query()->byWallet(50, SqlOperator::gte)->get();

        $this->assertCount(1, $users);
        $this->assertSame('rich@example.com', $users->first()->email);
    }

    public function test_by_wallet_with_equals_operator(): void
    {
        User::factory()->create([UserEnum::wallet->value => 10]);
        User::factory()->create([UserEnum::wallet->value => 20]);

        $users = User::query()->byWallet(10)->get();

        $this->assertCount(1, $users);
    }

    public function test_by_id_finds_user(): void
    {
        $user = User::factory()->create();

        $found = User::query()->byId($user->id)->first();

        $this->assertNotNull($found);
        $this->assertSame($user->id, $found->id);
    }
}
