<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Path,
    RelativePath
};
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    public function testValidPath()
    {
        $this->assertSame('/path/to/content', (string) new Path('/path/to/content'));
    }

    /**
     * @expectedException Innmind\UrlResolver\Exception\InvalidArgumentException
     * @expectedExceptionMessage The value "../relative/path" is not a valid path
     */
    public function testThrowWhenInvalidPath()
    {
        new Path('../relative/path');
    }

    public function testIsFolder()
    {
        $this->assertTrue((new Path('/path/'))->isFolder());
        $this->assertTrue((new Path('/path/?query=/'))->isFolder());
        $this->assertTrue((new Path('/path/#!bank/'))->isFolder());
        $this->assertFalse((new Path('/path/to'))->isFolder());
        $this->assertFalse((new Path('/path/to#!bang/'))->isFolder());
        $this->assertFalse((new Path('/path/to?query=/'))->isFolder());
    }

    public function testFolder()
    {
        $p = new Path('/path/to/content/');

        $p2 = $p->folder();
        $this->assertSame('/path/to/content/', (string) $p);
        $this->assertSame('/path/to/', (string) $p2);

        $p = new Path('/path/to/content');

        $p2 = $p->folder();
        $this->assertSame('/path/to/content', (string) $p);
        $this->assertSame('/path/to/', (string) $p2);
    }

    /**
     * @dataProvider pointingToExamples
     */
    public function testPointingTo(string $origin, string $dest, string $expected)
    {
        $p = new Path($origin);

        $p2 = $p->pointingTo(new RelativePath($dest));
        $this->assertSame($origin, (string) $p);
        $this->assertSame($expected, (string) $p2);
    }

    public function testClean()
    {
        $this->assertSame(
            '/path/to/content/',
            (string) (new Path('/path/to/content/'))->clean()
        );
        $this->assertSame(
            '/path/to/content/',
            (string) (new Path('/path/to/content/?query=foo'))->clean()
        );
        $this->assertSame(
            '/path/to/content/',
            (string) (new Path('/path/to/content/#fragment'))->clean()
        );
    }

    public function pointingToExamples()
    {
        return [
            ['/path/to/content', '../bar', '/path/bar'],
            ['/path/to/content', './bar', '/path/to/bar'],
            ['/path/to/content', 'bar', '/path/to/bar'],
            ['/path/to/content?query=foo#fragment', 'bar', '/path/to/bar'],
            ['/path/to/content/', '../bar', '/path/to/bar'],
            ['/path/to/content/', './bar', '/path/to/content/bar'],
            ['/path/to/content/', 'bar', '/path/to/content/bar'],
            ['/path/to/content/?query=foo#fragment', 'bar', '/path/to/content/bar'],
            ['/foo/baz', '../bar/foo', '/bar/foo'],
        ];
    }
}
