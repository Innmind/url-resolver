<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\Exception\DomainException;
use Innmind\Immutable\Str;

final class Scheme
{
    private Str $string;

    public function __construct(string $value)
    {
        $this->string = Str::of($value);

        if (!$this->string->matches('/^[a-z]+$/')) {
            throw new DomainException($value);
        }
    }

    public function toString(): string
    {
        return $this->string->toString();
    }
}
