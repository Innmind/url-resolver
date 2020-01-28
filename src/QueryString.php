<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Specification\QueryString as QueryStringSpecification,
    Exception\InvalidArgumentException,
};
use Innmind\Immutable\Str;

class QueryString extends Str
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
