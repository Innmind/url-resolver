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

    public function testAbsolutePath()
    {
        $this->assertTrue((new Url('/path/to/content'))->absolutePath());
        $this->assertFalse((new Url('?foo'))->absolutePath());
        $this->assertFalse((new Url('#blabla'))->absolutePath());
    }

    public function testFragment()
    {
        $this->assertTrue((new Url('#fragment'))->fragment());
        $this->assertFalse((new Url('?foo'))->fragment());
        $this->assertFalse((new Url('?foo=bar#blabla'))->fragment());
    }

    public function testQueryString()
    {
        $this->assertTrue((new Url('?foo'))->queryString());
        $this->assertTrue((new Url('?foo=bar#blabla'))->queryString());
        $this->assertFalse((new Url('#fragment'))->queryString());
    }

    public function testRelativePath()
    {
        $this->assertTrue((new Url('./path/to/content'))->relativePath());
        $this->assertTrue((new Url('../path/to/content'))->relativePath());
        $this->assertTrue((new Url('path/to/content'))->relativePath());
        $this->assertTrue((new Url('_path/to/content'))->relativePath());
        $this->assertTrue((new Url('.path/to/content'))->relativePath());
        $this->assertTrue((new Url('42path/to/content'))->relativePath());
        $this->assertTrue((new Url('!path/to/content'))->relativePath());
        $this->assertFalse((new Url('?foo'))->relativePath());
        $this->assertFalse((new Url('#blabla'))->relativePath());
    }

    public function testSchemeLess()
    {
        $this->assertTrue((new Url('//localhost'))->schemeLess());
        $this->assertTrue((new Url('localhost'))->schemeLess());
        $this->assertFalse((new Url('http://localhost'))->schemeLess());
        $this->assertFalse((new Url('ftp://localhost'))->schemeLess());
        $this->assertFalse((new Url('unknown://localhost'))->schemeLess());
    }

    public function testValid()
    {
        $this->assertTrue((new Url('http://localhost'))->valid('http', 'https'));
        $this->assertTrue((new Url('https://localhost'))->valid('http', 'https'));
        $this->assertFalse((new Url('ftp://localhost'))->valid('http', 'https'));
        $this->assertFalse((new Url('unknown://localhost'))->valid('http', 'https'));

        $this->assertTrue((new Url('http://localhost'))->valid());
        $this->assertTrue((new Url('https://localhost'))->valid());
        $this->assertTrue((new Url('ftp://localhost'))->valid());
        $this->assertTrue((new Url('unknown://localhost'))->valid());
        $this->assertFalse((new Url('localhost'))->valid());
        $this->assertFalse((new Url('//localhost'))->valid());
    }
}
