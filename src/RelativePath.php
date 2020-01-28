<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\Exception\DomainException;
use Innmind\Immutable\Str;

final class RelativePath
{
    private Str $string;

    public function __construct(string $value)
    {
        if (!(new Url($value))->relativePath()) {
            throw new DomainException($value);
        }

        $this->string = Str::of($value);
    }

    public function startsWithSelfReference(): bool
    {
        return $this->string->startsWith('./');
    }

    public function removeSelfReference(): self
    {
        return new self((string) $this->string->substring(2));
    }

    public function startsWithParentFolderReference(): bool
    {
        return $this->string->startsWith('../');
    }

    public function removeParentFolderReference(): self
    {
        return new self((string) $this->string->substring(3));
    }

    public function toString(): string
    {
        return (string) $this->string;
    }
}
