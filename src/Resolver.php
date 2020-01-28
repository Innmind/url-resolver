<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

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
    public function resolve(string $origin, string $destination): string;

    /**
     * Return the folder's path of the given url
     */
    public function folder(string $url): string;


    /**
     * Return the file's path for the given url
     *
     * Examples:
     *     * http://foo/bar/baz => http://foo/bar/baz
     *     * http://foo/bar/baz#fragment => http://foo/bar/baz
     *     * http://foo/bar/baz?query=string => http://foo/bar/baz?query=string
     *     * http://foo/bar/ => http://foo/bar/
     */
    public function file(string $url): string;
}
