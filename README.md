# UrlResolver

[![Build Status](https://github.com/innmind/url-resolver/workflows/CI/badge.svg?branch=master)](https://github.com/innmind/url-resolver/actions?query=workflow%3ACI)
[![codecov](https://codecov.io/gh/innmind/url-resolver/branch/develop/graph/badge.svg)](https://codecov.io/gh/innmind/url-resolver)
[![Type Coverage](https://shepherd.dev/github/innmind/url-resolver/coverage.svg)](https://shepherd.dev/github/innmind/url-resolver)

Allow to build an absolute url from a source url and a destination.

Example:
```php
use Innmind\UrlResolver\UrlResolver;
use Innmind\Url\Url;

$resolve = new UrlResolver;

$url = $resolve(
    Url::of('http://example.com/foo/'),
    Url::of('./bar/baz?query=string#fragment'),
);
// $url resolves to http://example.com/foo/bar/baz?query=string#fragment
```
