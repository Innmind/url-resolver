<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Specification;

use Innmind\UrlResolver\Url as UrlModel;

class QueryString
{
    /**
     * Check if the given Url is simply a query string
     *
     * @param UrlModel $url
     *
     * @return bool
     */
    public function isSatisfiedBy(UrlModel $url): bool
    {
        return $url->queryString();
    }
}
