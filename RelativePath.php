<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Specification\RelativePath as RelativePathSpecification,
    Exception\InvalidArgumentException
};
use Innmind\Immutable\StringPrimitive;

class RelativePath extends StringPrimitive
{
    public function __construct(string $value)
    {
        if (!(new RelativePathSpecification)->isSatisfiedBy(new Url($value))) {
            throw new InvalidArgumentException(sprintf(
                'The value "%s" is not a valid relative path',
                $value
            ));
        }

        parent::__construct($value);
    }
}
