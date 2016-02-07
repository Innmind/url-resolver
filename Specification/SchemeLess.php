<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Specification;

use Innmind\UrlResolver\Url;

class SchemeLess
{
    /**
     * Check if the given url has a scheme or not
     *
     * @param Url $url
     *
     * @return bool
     */
    public function isSatisfiedBy(Url $url): bool
    {
        if ((string) $url->substring(0, 2) === '//') {
            return true;
        }

        return !$url->match('/^[a-zA-Z]*:?\/\//');
    }
}
