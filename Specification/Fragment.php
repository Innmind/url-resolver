<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Specification;

use Innmind\UrlResolver\Url as UrlModel;

class Fragment
{
    /**
     * Check if the given Url is simply a fragment
     *
     * @param UrlModel $url
     *
     * @return bool
     */
    public function isSatisfiedBy(UrlModel $url): bool
    {
        return (string) $url->substring(0, 1) === '#';
    }
}
