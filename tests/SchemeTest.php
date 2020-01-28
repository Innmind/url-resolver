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
        $this->assertSame('http', (new Scheme('http'))->toString());
        $this->assertSame('https', (new Scheme('https'))->toString());
        $this->assertSame('ftp', (new Scheme('ftp'))->toString());
        $this->assertSame('unknown', (new Scheme('unknown'))->toString());
    }

    public function testThrowWhenInvalidScheme()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('42');

        new Scheme('42');
    }
}
