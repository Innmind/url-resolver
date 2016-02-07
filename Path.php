<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\Specification\AbsolutePath;
use Innmind\UrlResolver\Exception\InvalidArgumentException;
use Innmind\Immutable\StringPrimitive;

class Path extends StringPrimitive
{
    public function __construct(string $value)
    {
        if (!(new AbsolutePath)->isSatisfiedBy(new Url($value))) {
            throw new InvalidArgumentException(sprintf(
                'The value "%s" is not a valid path',
                $value
            ));
        }

        parent::__construct($value);
    }
}
