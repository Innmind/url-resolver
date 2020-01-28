<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Scheme,
    Exception\InvalidArgumentException,
};
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

    public function testThrowWhenInvalidScheme()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "42" is not a valid scheme');

        new Scheme('42');
    }
}
