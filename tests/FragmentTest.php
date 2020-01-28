<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Fragment,
    Exception\InvalidArgumentException,
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
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "?foo=bar" is not a valid fragment');

        new Fragment('?foo=bar');
    }
}
