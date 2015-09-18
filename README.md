# UrlResolver

Allow to build an absolute url from a source url and a destination.

Example:
```php
$url = $resolver->resolve(
    'http://example.com/foo/',
    'bar/baz?query=string#fragment'
);
// $url resolves to http://example.com/foo/bar/baz?query=string#fragment
```
