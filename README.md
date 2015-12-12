# UrlResolver

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/url-resolver/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Innmind/url-resolver/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Innmind/url-resolver/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Innmind/url-resolver/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/Innmind/url-resolver/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Innmind/url-resolver/build-status/master)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/926af5f8-5942-452d-8f22-b080480673b0/big.png)](https://insight.sensiolabs.com/projects/926af5f8-5942-452d-8f22-b080480673b0)

Allow to build an absolute url from a source url and a destination.

Example:
```php
$url = $resolver->resolve(
    'http://example.com/foo/',
    'bar/baz?query=string#fragment'
);
// $url resolves to http://example.com/foo/bar/baz?query=string#fragment
```
