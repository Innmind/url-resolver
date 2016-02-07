<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Tests;

use Innmind\UrlResolver\UrlResolver;

class UrlResolverTest extends \PHPUnit_Framework_TestCase
{
    protected $r;

    public function setUp()
    {
        $this->r = new UrlResolver(['http', 'https']);
    }

    public function testResolve()
    {
        $this->assertSame(
            'http://example.com/foo',
            $this->r->resolve(
                'http://example.com/bar',
                'http://example.com/foo'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/bar',
            $this->r->resolve(
                'http://xn--example.com/foo/baz',
                './bar'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/bar',
            $this->r->resolve(
                'http://xn--example.com/foo/baz',
                'bar'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/bar/baz?query=string#fragment',
            $this->r->resolve(
                'http://xn--example.com/foo/baz',
                'bar/baz?query=string#fragment'
            )
        );
        $this->assertSame(
            'http://xn--example.com/bar/foo',
            $this->r->resolve(
                'http://xn--example.com/foo/baz',
                '../bar/foo'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/baz?query=string',
            $this->r->resolve(
                'http://xn--example.com/foo/baz',
                '?query=string'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/baz/?query=string',
            $this->r->resolve(
                'http://xn--example.com/foo/baz/',
                '?query=string'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/baz/#fragment',
            $this->r->resolve(
                'http://xn--example.com/foo/baz/',
                '#fragment'
            )
        );
        $this->assertSame(
            'http://xn--example.com/foo/baz#fragment',
            $this->r->resolve(
                'http://xn--example.com/foo/baz',
                '#fragment'
            )
        );
        $this->assertSame(
            'http://xn--example.com/absolute',
            $this->r->resolve(
                'http://xn--example.com/foo/baz',
                '/absolute'
            )
        );
        $this->assertSame(
            'http://xn--example.com/absolute',
            $this->r->resolve(
                'http://xn--example.com/foo/',
                '/absolute'
            )
        );
        $this->assertSame(
            'http://xn--example.com:80/',
            $this->r->resolve(
                'http://xn--example.com:80/foo/',
                '../'
            )
        );
        $this->assertSame(
            'https://xn--example.com:443/',
            $this->r->resolve(
                'https://xn--example.com:443/foo/',
                '../'
            )
        );
        $this->assertSame(
            'http://xn--example.com/',
            $this->r->resolve(
                'http://xn-elsewhere.com/',
                '//xn--example.com/'
            )
        );
    }

    /**
     * @expectedException Innmind\UrlResolver\Exception\UrlException
     * @expectedExceptionMessage The origin variable is not a url (given: http://)
     */
    public function testThrowIfOriginIsNOtAUrl()
    {
        $this->r->resolve(
            '//',
            'bar'
        );
    }

    public function testFolder()
    {
        $this->assertSame(
            'http://xn--example.com/foo/',
            $this->r->folder('http://xn--example.com/foo/bar')
        );
        $this->assertSame(
            'http://xn--example.com/',
            $this->r->folder('http://xn--example.com/foo/')
        );
        $this->assertSame(
            'http://xn--example.com/',
            $this->r->folder('http://xn--example.com/')
        );
        $this->assertSame(
            'http://xn--example.com/',
            $this->r->folder('http://xn--example.com/foo/?foo=bar')
        );
    }

    public function testIsFolder()
    {
        $this->assertTrue($this->r->isFolder('http://xn--example.com/'));
        $this->assertTrue($this->r->isFolder('http://xn--example.com/foo/'));
        $this->assertTrue($this->r->isFolder('http://xn--example.com/foo/'));
        $this->assertTrue($this->r->isFolder('http://xn--example.com/foo/?foo=bar'));
        $this->assertFalse($this->r->isFolder('http://xn--example.com/foo'));
        $this->assertFalse($this->r->isFolder('http://xn--example.com/foo#/'));
    }

    public function testFile()
    {
        $this->assertSame(
            'http://xn--example.com/foo',
            $this->r->file('http://xn--example.com/foo')
        );
        $this->assertSame(
            'http://xn--example.com/foo?query=string',
            $this->r->file('http://xn--example.com/foo?query=string')
        );
        $this->assertSame(
            'http://xn--example.com/foo',
            $this->r->file('http://xn--example.com/foo#fragment')
        );
        $this->assertSame(
            'http://xn--example.com/foo/',
            $this->r->file('http://xn--example.com/foo/')
        );
        $this->assertSame(
            'http://xn--example.com/foo/?foo',
            $this->r->file('http://xn--example.com/foo/?foo')
        );
        $this->assertSame(
            'http://xn--example.com/foo/',
            $this->r->file('http://xn--example.com/foo/#fragment')
        );
    }
}
