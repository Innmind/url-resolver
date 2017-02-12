<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\Exception\InvalidArgumentException;
use Innmind\Immutable\Str;

class Scheme extends Str
{
    public function __construct(string $value)
    {
        parent::__construct($value);

        if (!$this->matches('/^[a-z]+$/')) {
            throw new InvalidArgumentException(sprintf(
                'The value "%s" is not a valid scheme',
                $value
            ));
        }
    }
}
