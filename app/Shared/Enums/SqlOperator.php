<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum SqlOperator: string
{
    case equals = '=';
    case notEquals = '!=';
    case gt = '>';
    case gte = '>=';
    case lt = '<';
    case lte = '<=';
    case in = 'IN';
    case notIn = 'NOT IN';
    case isNull = 'IS NULL';
    case isNotNull = 'IS NOT NULL';
    case like = 'LIKE';
    case between = 'BETWEEN';
}
