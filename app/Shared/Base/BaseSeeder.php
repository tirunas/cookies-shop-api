<?php

declare(strict_types=1);

namespace App\Shared\Base;

use Illuminate\Database\Seeder;

abstract class BaseSeeder extends Seeder
{
    abstract public function run(): void;
}
