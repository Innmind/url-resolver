<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\{
    UrlRepresentation,
    Scheme,
    QueryString,
    Fragment,
    Path
};
use PHPUnit\Framework\TestCase;

class UrlRepresentationTest extends TestCase
{
    public function testAppendScheme()
    {
        $url = new UrlRepresentation('//localhost');

        $url2 = $url->appendScheme(new Scheme('http'));
        $this->assertNotSame($url, $url2);
        $this->assertSame('//localhost', $url->toString());
        $this->assertSame('http://localhost', $url2->toString());

        $url = new UrlRepresentation('https://localhost');

        $url2 = $url->appendScheme(new Scheme('http'));
        $this->assertNotSame($url, $url2);
        $this->assertSame('https://localhost', $url->toString());
        $this->assertSame('http://localhost', $url2->toString());
    }

    public function testWithQueryString()
    {
        $u = new UrlRepresentation('http://localhost:8080/foo/?foo=bar');

        $u2 = $u->withQueryString(
            new QueryString('?bar=baz')
        );
        $this->assertNotSame($u, $u2);
        $this->assertSame('http://localhost:8080/foo/?foo=bar', $u->toString());
        $this->assertSame('http://localhost:8080/foo/?bar=baz', $u2->toString());
    }

    public function testWithFragment()
    {
        $u = new UrlRepresentation('http://localhost:8080/foo/?foo=bar#baz');

        $u2 = $u->withFragment(
            new Fragment('#bar')
        );
        $this->assertNotSame($u, $u2);
        $this->assertSame('http://localhost:8080/foo/?foo=bar#baz', $u->toString());
        $this->assertSame('http://localhost:8080/foo/?foo=bar#bar', $u2->toString());
    }

    public function testWithPath()
    {
        $u = new UrlRepresentation('http://localhost:8080/foo/?foo=bar#baz');

        $u2 = $u->withPath(
            new Path('/path/to/content')
        );
        $this->assertNotSame($u, $u2);
        $this->assertSame('http://localhost:8080/foo/?foo=bar#baz', $u->toString());
        $this->assertSame('http://localhost:8080/path/to/content', $u2->toString());
    }

    public function testAbsolutePath()
    {
        $this->assertTrue((new UrlRepresentation('/path/to/content'))->absolutePath());
        $this->assertFalse((new UrlRepresentation('?foo'))->absolutePath());
        $this->assertFalse((new UrlRepresentation('#blabla'))->absolutePath());
    }

    public function testFragment()
    {
        $this->assertTrue((new UrlRepresentation('#fragment'))->fragment());
        $this->assertFalse((new UrlRepresentation('?foo'))->fragment());
        $this->assertFalse((new UrlRepresentation('?foo=bar#blabla'))->fragment());
    }

    public function testQueryString()
    {
        $this->assertTrue((new UrlRepresentation('?foo'))->queryString());
        $this->assertTrue((new UrlRepresentation('?foo=bar#blabla'))->queryString());
        $this->assertFalse((new UrlRepresentation('#fragment'))->queryString());
    }

    public function testRelativePath()
    {
        $this->assertTrue((new UrlRepresentation('./path/to/content'))->relativePath());
        $this->assertTrue((new UrlRepresentation('../path/to/content'))->relativePath());
        $this->assertTrue((new UrlRepresentation('path/to/content'))->relativePath());
        $this->assertTrue((new UrlRepresentation('_path/to/content'))->relativePath());
        $this->assertTrue((new UrlRepresentation('.path/to/content'))->relativePath());
        $this->assertTrue((new UrlRepresentation('42path/to/content'))->relativePath());
        $this->assertTrue((new UrlRepresentation('!path/to/content'))->relativePath());
        $this->assertFalse((new UrlRepresentation('?foo'))->relativePath());
        $this->assertFalse((new UrlRepresentation('#blabla'))->relativePath());
    }

    public function testSchemeLess()
    {
        $this->assertTrue((new UrlRepresentation('//localhost'))->schemeLess());
        $this->assertTrue((new UrlRepresentation('localhost'))->schemeLess());
        $this->assertFalse((new UrlRepresentation('http://localhost'))->schemeLess());
        $this->assertFalse((new UrlRepresentation('ftp://localhost'))->schemeLess());
        $this->assertFalse((new UrlRepresentation('unknown://localhost'))->schemeLess());
    }

    public function testValid()
    {
        $this->assertTrue((new UrlRepresentation('http://localhost'))->valid('http', 'https'));
        $this->assertTrue((new UrlRepresentation('https://localhost'))->valid('http', 'https'));
        $this->assertFalse((new UrlRepresentation('ftp://localhost'))->valid('http', 'https'));
        $this->assertFalse((new UrlRepresentation('unknown://localhost'))->valid('http', 'https'));

        $this->assertTrue((new UrlRepresentation('http://localhost'))->valid());
        $this->assertTrue((new UrlRepresentation('https://localhost'))->valid());
        $this->assertTrue((new UrlRepresentation('ftp://localhost'))->valid());
        $this->assertTrue((new UrlRepresentation('unknown://localhost'))->valid());
        $this->assertFalse((new UrlRepresentation('localhost'))->valid());
        $this->assertFalse((new UrlRepresentation('//localhost'))->valid());
    }
}
