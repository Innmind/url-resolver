<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Tests;

use Innmind\UrlResolver\Url;
use Innmind\UrlResolver\Scheme;
use Innmind\UrlResolver\QueryString;
use Pdp\Parser;
use Pdp\PublicSuffixListManager;

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
}
