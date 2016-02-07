<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Tests;

use Innmind\UrlResolver\Fragment;

class FragmentTest extends \PHPUnit_Framework_TestCase
{
    public function testNotThrowWhenBuilding()
    {
        new Fragment('#fragment');
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
