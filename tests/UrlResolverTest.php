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
    protected $resolver;

    public function setUp(): void
    {
        $this->resolver = new UrlResolver('http', 'https');
    }

    public function testInterface()
    {
        $this->assertInstanceOf(Resolver::class, $this->resolver);
    }

    public function testResolve()
    {
        $this->assertSame(
            'http://example.com/foo',
            $this->resolver->resolve(
                'http://example.com',
                'foo'
            )
        );
        $this->assertSame(
            'http://example.com/foo',
            $this->resolver->resolve(
                'http://example.com/bar',
                'http://example.com/foo'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/bar',
            $this->resolver->resolve(
                'http://xn--example.com/foo/baz',
                './bar'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/bar',
            $this->resolver->resolve(
                'http://xn--example.com/foo/baz',
                'bar'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/bar/baz?query=string#fragment',
            $this->resolver->resolve(
                'http://xn--example.com/foo/baz',
                'bar/baz?query=string#fragment'
            )
        );
        $this->assertSame(
            'http://xn--example.com/bar/foo',
            $this->resolver->resolve(
                'http://xn--example.com/foo/baz',
                '../bar/foo'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/baz?query=string',
            $this->resolver->resolve(
                'http://xn--example.com/foo/baz',
                '?query=string'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/baz/?query=string',
            $this->resolver->resolve(
                'http://xn--example.com/foo/baz/',
                '?query=string'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/baz/#fragment',
            $this->resolver->resolve(
                'http://xn--example.com/foo/baz/',
                '#fragment'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/baz#fragment',
            $this->resolver->resolve(
                'http://xn--example.com/foo/baz',
                '#fragment'
            )
        );
        $this->assertSame(
            'http://xn--example.com/absolute',
            $this->resolver->resolve(
                'http://xn--example.com/foo/baz',
                '/absolute'
            )
        );
        $this->assertSame(
            'http://xn--example.com/absolute',
            $this->resolver->resolve(
                'http://xn--example.com/foo/',
                '/absolute'
            )
        );
        $this->assertSame(
            'http://xn--example.com:80/',
            $this->resolver->resolve(
                'http://xn--example.com:80/foo/',
                '../'
            )
        );
        $this->assertSame(
            'https://xn--example.com:443/',
            $this->resolver->resolve(
                'https://xn--example.com:443/foo/',
                '../'
            )
        );
        $this->assertSame(
            'http://xn--example.com/',
            $this->resolver->resolve(
                'http://xn-elsewhere.com/',
                '//xn--example.com/'
            )
        );
    }

    public function testThrowIfOriginIsNotAUrl()
    {
        $this->expectException(OriginIsNotAValidUrl::class);
        $this->expectExceptionMessage('http://');

        $this->resolver->resolve(
            '//',
            'bar'
        );
    }

    public function testFolder()
    {
        $this->assertSame(
            'http://xn--example.com/foo/',
            $this->resolver->folder('http://xn--example.com/foo/bar')
        );
        $this->assertSame(
            'http://xn--example.com/',
            $this->resolver->folder('http://xn--example.com/foo/')
        );
        $this->assertSame(
            'http://xn--example.com/',
            $this->resolver->folder('http://xn--example.com/')
        );
        $this->assertSame(
            'http://xn--example.com/',
            $this->resolver->folder('http://xn--example.com/foo/?foo=bar')
        );
    }

    public function testFile()
    {
        $this->assertSame(
            'http://xn--example.com/foo',
            $this->resolver->file('http://xn--example.com/foo')
        );
        $this->assertSame(
            'http://xn--example.com/foo?query=string',
            $this->resolver->file('http://xn--example.com/foo?query=string')
        );
        $this->assertSame(
            'http://xn--example.com/foo',
            $this->resolver->file('http://xn--example.com/foo#fragment')
        );
        $this->assertSame(
            'http://xn--example.com/foo/',
            $this->resolver->file('http://xn--example.com/foo/')
        );
        $this->assertSame(
            'http://xn--example.com/foo/?foo',
            $this->resolver->file('http://xn--example.com/foo/?foo')
        );
        $this->assertSame(
            'http://xn--example.com/foo/',
            $this->resolver->file('http://xn--example.com/foo/#fragment')
        );
    }
}
