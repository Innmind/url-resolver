<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\{
    UrlResolver,
    Resolver,
    Exception\OriginIsNotAValidUrl,
};
use Innmind\Url\Url;
use PHPUnit\Framework\TestCase;

class UrlResolverTest extends TestCase
{
    protected $resolve;

    public function setUp(): void
    {
        $this->resolve = UrlResolver::of('http', 'https');
    }

    public function testInterface()
    {
        $this->assertInstanceOf(Resolver::class, $this->resolve);
    }

    /**
     * @dataProvider cases
     */
    public function testResolve(string $source, string $destination, string $expected)
    {
        $resolved = ($this->resolve)(
            Url::of($source),
            Url::of($destination),
        );

        $this->assertInstanceOf(Url::class, $resolved);
        $this->assertSame($expected, $resolved->toString());
    }

    public function cases(): array
    {
        return [
            ['//example.com', 'foo', 'http://example.com/foo'],
            ['http://example.com', 'foo', 'http://example.com/foo'],
            ['http://example.com/bar', 'http://example.com/foo', 'http://example.com/foo'],
            ['http://xn--example.com/foo/baz', './bar', 'http://xn--example.com/foo/bar'],
            ['http://xn--example.com/foo/baz', 'bar', 'http://xn--example.com/foo/bar'],
            ['http://xn--example.com/foo/baz', 'bar/baz?query=string#fragment', 'http://xn--example.com/foo/bar/baz?query=string#fragment'],
            ['http://xn--example.com/foo/baz', '../bar/foo', 'http://xn--example.com/bar/foo'],
            ['http://xn--example.com/foo/baz', '?query=string', 'http://xn--example.com/foo/baz?query=string'],
            ['http://xn--example.com/foo/baz/', '?query=string', 'http://xn--example.com/foo/baz/?query=string'],
            ['http://xn--example.com/foo/baz/', '#fragment', 'http://xn--example.com/foo/baz/#fragment'],
            ['http://xn--example.com/foo/baz', '#fragment', 'http://xn--example.com/foo/baz#fragment'],
            ['http://xn--example.com/foo/baz', '/absolute', 'http://xn--example.com/absolute'],
            ['http://xn--example.com/foo/', '/absolute', 'http://xn--example.com/absolute'],
            ['http://xn--example.com:80/foo/', '../', 'http://xn--example.com:80/'],
            ['https://xn--example.com:443/foo/', '../', 'https://xn--example.com:443/'],
            ['http://xn-elsewhere.com/', '//xn--example.com/', 'http://xn--example.com/'],
            ['/path/to/content', '../bar', '/path/bar'],
            ['/path/to/content', './bar', '/path/to/bar'],
            ['/path/to/content', 'bar', '/path/to/bar'],
            ['/path/to/content?query=foo#fragment', 'bar', '/path/to/bar'],
            ['/path/to/content/', '../bar', '/path/to/bar'],
            ['/path/to/content/', './bar', '/path/to/content/bar'],
            ['/path/to/content/', 'bar', '/path/to/content/bar'],
            ['/path/to/content/?query=foo#fragment', 'bar', '/path/to/content/bar'],
            ['/foo/baz', '../bar/foo', '/bar/foo'],
        ];
    }
}
