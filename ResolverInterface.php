<?php

namespace Innmind\UrlResolver;

interface ResolverInterface
{
    /**
     * Resolve a destination based to an origin point
     *
     * Examples:
     *     * http://example.com/foo and /bar => http://example.com/bar
     *     * http://foo/bar and http://bar/ => http://bar/
     *     * http://foo/bar and ?query=string => http://foo/bar?query=string
     *
     * @param string $origin
     * @param string $destination
     *
     * @return string
     */
    public function resolve($origin, $destination);

    /**
     * Return the folder's path of the given url
     *
     * @param string $url
     *
     * @return string
     */
    public function folder($url);


    /**
     * Return the file's path for the given url
     *
     * Examples:
     *     * http://foo/bar/baz => http://foo/bar/baz
     *     * http://foo/bar/baz#fragment => http://foo/bar/baz
     *     * http://foo/bar/baz?query=string => http://foo/bar/baz?query=string
     *     * http://foo/bar/ => http://foo/bar/
     *
     * @param string $url
     *
     * @return string
     */
    public function file($url);

    /**
     * Check if the url point to a folder
     *
     * @param string $url
     *
     * @return bool
     */
    public function isFolder($url);
}
