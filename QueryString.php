<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\Specification\QueryString as QueryStringSpecification;
use Innmind\UrlResolver\Exception\InvalidArgumentException;
use Innmind\Immutable\StringPrimitive;

class QueryString extends StringPrimitive
{
    public function __construct(string $value)
    {
        if (!(new QueryStringSpecification)->isSatisfiedBy(new Url($value))) {
            throw new InvalidArgumentException(sprintf(
                'The value "%s" is not a valid query string',
                $value
            ));
        }

        parent::__construct($value);
    }
}
