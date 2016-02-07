<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Tests;

use Innmind\UrlResolver\Path;

class PathTest extends \PHPUnit_Framework_TestCase
{
    public function testValidPath()
    {
        new Path('/path/to/content');
    }

    /**
     * @expectedException Innmind\UrlResolver\Exception\InvalidArgumentException
     * @expectedException The value "42" is not a valid path
     */
    public function testThrowWhenInvalidPath()
    {
        new Path('../relative/path');
    }
}
