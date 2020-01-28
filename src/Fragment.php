<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Specification\Fragment as FragmentSpecification,
    Exception\DomainException,
};
use Innmind\Immutable\Str;

class Fragment extends Str
{
    public function __construct(string $value)
    {
        if (!(new FragmentSpecification)->isSatisfiedBy(new Url($value))) {
            throw new DomainException($value);
        }

        parent::__construct($value);
    }
}
