<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Specification;

use Innmind\UrlResolver\Url;

class QueryString
{
    /**
     * Check if the given Url is simply a query string
     *
     * @param Url $url
     *
     * @return bool
     */
    public function isSatisfiedBy(Url $url): bool
    {
        return (string) $url->substring(0, 1) === '?';
    }
}
