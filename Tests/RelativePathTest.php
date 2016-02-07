<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Tests;

use Innmind\UrlResolver\RelativePath;

class RelativePathTest extends \PHPUnit_Framework_TestCase
{
    public function testValidRelativePath()
    {
        new RelativePath('../path/to/content');
        new RelativePath('./path/to/content');
        new RelativePath('path/to/content');
        new RelativePath('.path/to/content');
        new RelativePath('!path/to/content');
        new RelativePath('42path/to/content');
    }

    /**
     * @expectedException Innmind\UrlResolver\Exception\InvalidArgumentException
     * @expectedExceptionMessage The value "/relative/path" is not a valid relative path
     */
    public function testThrowWhenInvalidRelativePath()
    {
        new RelativePath('/relative/path');
    }
}
