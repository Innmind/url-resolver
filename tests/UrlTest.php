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
use Pdp\{
    Parser,
    PublicSuffixListManager
};

class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function testAppendScheme()
    {
        $url = new Url('//localhost');

        $url2 = $url->appendScheme(new Scheme('http'));
        $this->assertNotSame($url, $url2);
        $this->assertSame('//localhost', (string) $url);
        $this->assertSame('http://localhost', (string) $url2);

        $url = new Url('https://localhost');

        $url2 = $url->appendScheme(new Scheme('http'));
        $this->assertNotSame($url, $url2);
        $this->assertSame('https://localhost', (string) $url);
        $this->assertSame('http://localhost', (string) $url2);
    }

    public function testWithQueryString()
    {
        $u = new Url('http://localhost:8080/foo/?foo=bar');

        $u2 = $u->withQueryString(
            new QueryString('?bar=baz'),
            new Parser((new PublicSuffixListManager)->getList())
        );
        $this->assertNotSame($u, $u2);
        $this->assertSame('http://localhost:8080/foo/?foo=bar', (string) $u);
        $this->assertSame('http://localhost:8080/foo/?bar=baz', (string) $u2);
    }

    public function testWithFragment()
    {
        $u = new Url('http://localhost:8080/foo/?foo=bar#baz');

        $u2 = $u->withFragment(
            new Fragment('#bar'),
            new Parser((new PublicSuffixListManager)->getList())
        );
        $this->assertNotSame($u, $u2);
        $this->assertSame('http://localhost:8080/foo/?foo=bar#baz', (string) $u);
        $this->assertSame('http://localhost:8080/foo/?foo=bar#bar', (string) $u2);
    }

    public function testWithPath()
    {
        $u = new Url('http://localhost:8080/foo/?foo=bar#baz');

        $u2 = $u->withPath(
            new Path('/path/to/content'),
            new Parser((new PublicSuffixListManager)->getList())
        );
        $this->assertNotSame($u, $u2);
        $this->assertSame('http://localhost:8080/foo/?foo=bar#baz', (string) $u);
        $this->assertSame('http://localhost:8080/path/to/content', (string) $u2);
    }
}
