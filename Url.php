<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\Immutable\StringPrimitive;

final class Url extends StringPrimitive
{
    /**
     * Append the given scheme to the url
     *
     * @param string $scheme
     *
     * @return self
     */
    public function appendScheme(string $scheme): self
    {
        return new self(
            (string) $this->pregReplace('/^[a-zA-Z]*:?\/\//', $scheme . '://')
        );
    }
}
