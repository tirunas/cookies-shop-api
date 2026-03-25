<?php

declare(strict_types=1);

namespace App\Shared\Base;

use App\Shared\Enums\ModelEnum;
use App\Shared\Enums\SqlOperator;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 *
 * @extends Builder<TModel>
 */
abstract class BaseBuilder extends Builder
{
    public function byId(int|array|null $value = null, SqlOperator $operator = SqlOperator::equals): self
    {
        return $this->byField(ModelEnum::id, $value, $operator);
    }

    protected function byField(
        string|BackedEnum $field,
        int|float|string|array|null $value = null,
        SqlOperator $operator = SqlOperator::equals,
    ): static {
        $column = $field;

        if ($field instanceof BackedEnum) {
            $column = $field->value;
        }

        return match ($operator) {
            SqlOperator::equals => $this->where($column, $value),
            SqlOperator::notEquals => $this->where($column, '!=', $value),
            SqlOperator::gt => $this->where($column, '>', $value),
            SqlOperator::gte => $this->where($column, '>=', $value),
            SqlOperator::lt => $this->where($column, '<', $value),
            SqlOperator::lte => $this->where($column, '<=', $value),
            SqlOperator::in => $this->whereIn($column, (array)$value),
            SqlOperator::notIn => $this->whereNotIn($column, (array)$value),
            SqlOperator::isNull => $this->whereNull($column),
            SqlOperator::isNotNull => $this->whereNotNull($column),
            SqlOperator::like => $this->where($column, 'LIKE', $value),
            SqlOperator::between => $this->whereBetween($column, (array)$value),
        };
    }
}
