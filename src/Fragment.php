<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Specification\Fragment as FragmentSpecification,
    Exception\InvalidArgumentException,
};
use Innmind\Immutable\Str;

class Fragment extends Str
{
    public function __construct(string $value)
    {
        if (!(new FragmentSpecification)->isSatisfiedBy(new Url($value))) {
            throw new InvalidArgumentException(sprintf(
                'The value "%s" is not a valid fragment',
                $value
            ));
        }

        parent::__construct($value);
    }
}
