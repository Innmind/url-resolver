<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Fragment,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class FragmentTest extends TestCase
{
    public function testNotThrowWhenBuilding()
    {
        $this->assertSame('#fragment', (string) new Fragment('#fragment'));
    }

    public function testThrowWhenInvalidValue()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('?foo=bar');

        new Fragment('?foo=bar');
    }
}
