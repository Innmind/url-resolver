<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\Exception\DomainException;
use Innmind\Immutable\Str;

class Scheme extends Str
{
    public function __construct(string $value)
    {
        parent::__construct($value);

        if (!$this->matches('/^[a-z]+$/')) {
            throw new DomainException($value);
        }
    }
}
