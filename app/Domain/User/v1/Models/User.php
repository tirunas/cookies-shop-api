<?php

namespace App\Domain\User\v1\Models;

use App\Domain\User\v1\Builders\UserBuilder;
use App\Domain\User\v1\Database\Factories\UserFactory;
use App\Domain\User\v1\Enums\UserEnum;
use App\Shared\Traits\HasTableName;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $email
 * @property-read string $password
 * @property float $wallet
 *
 * @method static UserBuilder query()
 * @method static UserBuilder withTrashed()
 */
#[Fillable([UserEnum::name->value, UserEnum::email->value, UserEnum::password->value])]
#[Hidden([UserEnum::password->value, UserEnum::rememberToken->value])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasTableName;
    use HasApiTokens;
    use Notifiable;

    protected function casts(): array
    {
        return [
            UserEnum::emailVerifiedAt->value => 'datetime',
            UserEnum::password->value => 'hashed',
            UserEnum::wallet->value => 'float',
        ];
    }

    public function newEloquentBuilder($query): UserBuilder
    {
        return new UserBuilder($query);
    }
}
