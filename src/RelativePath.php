<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Specification\RelativePath as RelativePathSpecification,
    Exception\DomainException,
};
use Innmind\Immutable\Str;

class RelativePath extends Str
{
    public function __construct(string $value)
    {
        if (!(new RelativePathSpecification)->isSatisfiedBy(new Url($value))) {
            throw new DomainException($value);
        }

        parent::__construct($value);
    }
}
