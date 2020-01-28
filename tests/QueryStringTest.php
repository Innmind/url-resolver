<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\{
    QueryString,
    Exception\DomainException,
};
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

    public function testThrowWhenInvalidValue()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('#fragment');

        new QueryString('#fragment');
    }
}
