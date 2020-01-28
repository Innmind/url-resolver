<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Url,
    Scheme,
    QueryString,
    Fragment,
    Path
};
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function testAppendScheme()
    {
        $url = new Url('//localhost');

        $url2 = $url->appendScheme(new Scheme('http'));
        $this->assertNotSame($url, $url2);
        $this->assertSame('//localhost', $url->toString());
        $this->assertSame('http://localhost', $url2->toString());

        $url = new Url('https://localhost');

        $url2 = $url->appendScheme(new Scheme('http'));
        $this->assertNotSame($url, $url2);
        $this->assertSame('https://localhost', $url->toString());
        $this->assertSame('http://localhost', $url2->toString());
    }

    public function testWithQueryString()
    {
        $u = new Url('http://localhost:8080/foo/?foo=bar');

        $u2 = $u->withQueryString(
            new QueryString('?bar=baz')
        );
        $this->assertNotSame($u, $u2);
        $this->assertSame('http://localhost:8080/foo/?foo=bar', $u->toString());
        $this->assertSame('http://localhost:8080/foo/?bar=baz', $u2->toString());
    }

    public function testWithFragment()
    {
        $u = new Url('http://localhost:8080/foo/?foo=bar#baz');

        $u2 = $u->withFragment(
            new Fragment('#bar')
        );
        $this->assertNotSame($u, $u2);
        $this->assertSame('http://localhost:8080/foo/?foo=bar#baz', $u->toString());
        $this->assertSame('http://localhost:8080/foo/?foo=bar#bar', $u2->toString());
    }

    public function testWithPath()
    {
        $u = new Url('http://localhost:8080/foo/?foo=bar#baz');

        $u2 = $u->withPath(
            new Path('/path/to/content')
        );
        $this->assertNotSame($u, $u2);
        $this->assertSame('http://localhost:8080/foo/?foo=bar#baz', $u->toString());
        $this->assertSame('http://localhost:8080/path/to/content', $u2->toString());
    }
}
