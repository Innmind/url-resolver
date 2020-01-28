<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Scheme,
    Exception\DomainException,
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
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('42');

        new Scheme('42');
    }
}
