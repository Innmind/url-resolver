<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\QueryString;

class QueryStringTest extends \PHPUnit_Framework_TestCase
{
    public function testNotThrowWhenBuilding()
    {
        new QueryString('?foo');
        new QueryString('?foo=bar');
        new QueryString('?foo=bar#fragment');
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
