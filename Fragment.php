<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\Specification\Fragment as FragmentSpecification;
use Innmind\UrlResolver\Exception\InvalidArgumentException;
use Innmind\Immutable\StringPrimitive;

class Fragment extends StringPrimitive
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
