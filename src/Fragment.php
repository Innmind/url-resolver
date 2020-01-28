<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Specification\Fragment as FragmentSpecification,
    Exception\DomainException,
};
use Innmind\Immutable\Str;

final class Fragment
{
    private Str $string;

    public function __construct(string $value)
    {
        if (!(new FragmentSpecification)->isSatisfiedBy(new Url($value))) {
            throw new DomainException($value);
        }

        $this->string = Str::of($value);
    }

    public function withoutHash(): string
    {
        return (string) $this->string->substring(1);
    }

    public function toString(): string
    {
        return (string) $this->string;
    }
}
