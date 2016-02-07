<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\Immutable\StringPrimitive;
use Pdp\Parser;

final class Url extends StringPrimitive
{
    /**
     * Append the given scheme to the url
     *
     * @param Scheme $scheme
     *
     * @return self
     */
    public function appendScheme(Scheme $scheme): self
    {
        return new self(
            (string) $this->pregReplace(
                '/^[a-zA-Z]*:?\/\//',
                (string) $scheme . '://'
            )
        );
    }
}
