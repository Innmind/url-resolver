<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\QueryString;
use PHPUnit\Framework\TestCase;

class QueryStringTest extends TestCase
{
    public function testNotThrowWhenBuilding()
    {
        $this->assertSame(
            '?foo',
            (string) new QueryString('?foo')
        );
        $this->assertSame(
            '?foo=bar',
            (string) new QueryString('?foo=bar')
        );
        $this->assertSame(
            '?foo=bar#fragment',
            (string) new QueryString('?foo=bar#fragment')
        );
    }

    /**
     * @expectedException Innmind\UrlResolver\Exception\InvalidArgumentException
     * @expectedExceptionMessage The value "#fragment" is not a valid query string
     */
    public function testThrowWhenInvalidValue()
    {
        new QueryString('#fragment');
    }
}
