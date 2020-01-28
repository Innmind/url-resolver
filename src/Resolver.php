<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\Url\Url;

interface Resolver
{
    /**
     * Resolve a destination based to an origin point
     *
     * Examples:
     *     * http://example.com/foo and /bar => http://example.com/bar
     *     * http://foo/bar and http://bar/ => http://bar/
     *     * http://foo/bar and ?query=string => http://foo/bar?query=string
     */
    public function __invoke(string $origin, string $destination): Url;
}
