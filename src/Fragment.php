<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\Exception\DomainException;
use Innmind\Immutable\Str;

final class Fragment
{
    private Str $string;

    public function __construct(string $value)
    {
        if (!(new Url($value))->fragment()) {
            throw new DomainException($value);
        }

        $this->string = Str::of($value);
    }

    public function withoutHash(): string
    {
        return $this->string->substring(1)->toString();
    }

    public function toString(): string
    {
        return $this->string->toString();
    }
}
