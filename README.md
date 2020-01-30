# UrlResolver

| `develop` |
|-----------|
| [![codecov](https://codecov.io/gh/Innmind/url-resolver/branch/develop/graph/badge.svg)](https://codecov.io/gh/Innmind/url-resolver) |
| [![Build Status](https://github.com/Innmind/url-resolver/workflows/CI/badge.svg)](https://github.com/Innmind/url-resolver/actions?query=workflow%3ACI) |

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
