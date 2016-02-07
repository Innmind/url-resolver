<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Specification;

use Innmind\UrlResolver\Url;

class RelativePath
{
    /**
     * Check if the given Url is a relative path
     *
     * @param Url $url
     *
     * @return bool
     */
    public function isSatisfiedBy(Url $url): bool
    {
        if ((string) $url->substring(0, 2) === './') {
            return true;
        }

        if ((string) $url->substring(0, 3) === '../') {
            return true;
        }

        return !$url->match('/^(\/|\?|#)/');
    }
}
