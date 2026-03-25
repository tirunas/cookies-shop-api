<?php

declare(strict_types=1);

namespace App\Shared\Base;

use App\Domain\User\v1\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseService
{
    public function getAuthUser(): User
    {
        return Auth::user() ?? abort(Response::HTTP_UNAUTHORIZED);
    }
}
