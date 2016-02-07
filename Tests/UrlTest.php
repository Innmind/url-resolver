<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Tests;

use Innmind\UrlResolver\Url;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function testAppendScheme()
    {
        $url = new Url('//localhost');

        $url2 = $url->appendScheme('http');
        $this->assertNotSame($url, $url2);
        $this->assertSame('//localhost', (string) $url);
        $this->assertSame('http://localhost', (string) $url2);

        $url = new Url('https://localhost');

        $url2 = $url->appendScheme('http');
        $this->assertNotSame($url, $url2);
        $this->assertSame('https://localhost', (string) $url);
        $this->assertSame('http://localhost', (string) $url2);
    }
}
