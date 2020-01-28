<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Specification;

use Innmind\UrlResolver\Url as UrlModel;

class SchemeLess
{
    /**
     * Check if the given url has a scheme or not
     *
     * @param UrlModel $url
     *
     * @return bool
     */
    public function isSatisfiedBy(UrlModel $url): bool
    {
        return $url->schemeLess();
    }
}
