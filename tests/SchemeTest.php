<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\Scheme;

class SchemeTest extends \PHPUnit_Framework_TestCase
{
    public function testValidScheme()
    {
        new Scheme('http');
        new Scheme('https');
        new Scheme('ftp');
        new Scheme('unknown');
    }

    /**
     * @expectedException Innmind\UrlResolver\Exception\InvalidArgumentException
     * @expectedExceptionMessage The value "42" is not a valid scheme
     */
    public function testThrowWhenInvalidScheme()
    {
        new Scheme('42');
    }
}
