<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\{
    UrlResolver,
    Resolver,
    Exception\OriginIsNotAValidUrl,
};
use PHPUnit\Framework\TestCase;

class UrlResolverTest extends TestCase
{
    protected $resolve;

    public function setUp(): void
    {
        $this->resolve = new UrlResolver('http', 'https');
    }

    public function testInterface()
    {
        $this->assertInstanceOf(Resolver::class, $this->resolve);
    }

    public function testResolve()
    {
        $this->assertSame(
            'http://example.com/foo',
            ($this->resolve)(
                'http://example.com',
                'foo'
            )
        );
        $this->assertSame(
            'http://example.com/foo',
            ($this->resolve)(
                'http://example.com/bar',
                'http://example.com/foo'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/bar',
            ($this->resolve)(
                'http://xn--example.com/foo/baz',
                './bar'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/bar',
            ($this->resolve)(
                'http://xn--example.com/foo/baz',
                'bar'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/bar/baz?query=string#fragment',
            ($this->resolve)(
                'http://xn--example.com/foo/baz',
                'bar/baz?query=string#fragment'
            )
        );
        $this->assertSame(
            'http://xn--example.com/bar/foo',
            ($this->resolve)(
                'http://xn--example.com/foo/baz',
                '../bar/foo'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/baz?query=string',
            ($this->resolve)(
                'http://xn--example.com/foo/baz',
                '?query=string'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/baz/?query=string',
            ($this->resolve)(
                'http://xn--example.com/foo/baz/',
                '?query=string'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/baz/#fragment',
            ($this->resolve)(
                'http://xn--example.com/foo/baz/',
                '#fragment'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/baz#fragment',
            ($this->resolve)(
                'http://xn--example.com/foo/baz',
                '#fragment'
            )
        );
        $this->assertSame(
            'http://xn--example.com/absolute',
            ($this->resolve)(
                'http://xn--example.com/foo/baz',
                '/absolute'
            )
        );
        $this->assertSame(
            'http://xn--example.com/absolute',
            ($this->resolve)(
                'http://xn--example.com/foo/',
                '/absolute'
            )
        );
        $this->assertSame(
            'http://xn--example.com:80/',
            ($this->resolve)(
                'http://xn--example.com:80/foo/',
                '../'
            )
        );
        $this->assertSame(
            'https://xn--example.com:443/',
            ($this->resolve)(
                'https://xn--example.com:443/foo/',
                '../'
            )
        );
        $this->assertSame(
            'http://xn--example.com/',
            ($this->resolve)(
                'http://xn-elsewhere.com/',
                '//xn--example.com/'
            )
        );
    }

    public function testThrowIfOriginIsNotAUrl()
    {
        $this->expectException(OriginIsNotAValidUrl::class);
        $this->expectExceptionMessage('http://');

        ($this->resolve)(
            '//',
            'bar'
        );
    }
}
