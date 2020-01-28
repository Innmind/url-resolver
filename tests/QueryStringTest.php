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
            (new QueryString('?foo'))->toString(),
        );
        $this->assertSame(
            '?foo=bar',
            (new QueryString('?foo=bar'))->toString(),
        );
        $this->assertSame(
            '?foo=bar#fragment',
            (new QueryString('?foo=bar#fragment'))->toString(),
        );
    }

    public function testThrowWhenInvalidValue()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('#fragment');

        new QueryString('#fragment');
    }
}
