<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Tests;

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
     * @expectedException The value "42" is not a valid scheme
     */
    public function testThrowWhenInvalidScheme()
    {
        new Scheme('42');
    }
}
