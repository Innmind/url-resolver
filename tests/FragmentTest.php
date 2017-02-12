<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\Fragment;
use PHPUnit\Framework\TestCase;

class FragmentTest extends TestCase
{
    public function testNotThrowWhenBuilding()
    {
        $this->assertSame('#fragment', (string) new Fragment('#fragment'));
    }

    /**
     * @expectedException Innmind\UrlResolver\Exception\InvalidArgumentException
     * @expectedExceptionMessage The value "?foo=bar" is not a valid fragment
     */
    public function testThrowWhenInvalidValue()
    {
        new Fragment('?foo=bar');
    }
}
