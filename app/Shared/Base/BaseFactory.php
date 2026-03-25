<?php

declare (strict_types = 1);

namespace App\Shared\Base;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 *
 * @extends Factory<TModel>
 */
abstract class BaseFactory extends Factory
{
    abstract public function definition(): array;
}
