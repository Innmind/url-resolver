<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\Scheme;
use PHPUnit\Framework\TestCase;

class SchemeTest extends TestCase
{
    public function testValidScheme()
    {
        $this->assertSame('http', (string) new Scheme('http'));
        $this->assertSame('https', (string) new Scheme('https'));
        $this->assertSame('ftp', (string) new Scheme('ftp'));
        $this->assertSame('unknown', (string) new Scheme('unknown'));
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
