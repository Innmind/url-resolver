<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Specification;

use Innmind\UrlResolver\Url as UrlModel;

class AbsolutePath
{
    /**
     * Check if the given Url is an absolute path
     *
     * @param UrlModel $url
     *
     * @return bool
     */
    public function isSatisfiedBy(UrlModel $url): bool
    {
        return $url->absolutePath();
    }
}
