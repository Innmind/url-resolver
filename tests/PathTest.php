<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Path,
    RelativePath,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    public function testValidPath()
    {
        $this->assertSame('/path/to/content', (new Path('/path/to/content'))->toString());
    }

    public function testThrowWhenInvalidPath()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('../relative/path');

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
        $this->assertSame('/path/to/content/', $p->toString());
        $this->assertSame('/path/to/', $p2->toString());

        $p = new Path('/path/to/content');

        $p2 = $p->folder();
        $this->assertSame('/path/to/content', $p->toString());
        $this->assertSame('/path/to/', $p2->toString());
    }

    /**
     * @dataProvider pointingToExamples
     */
    public function testPointingTo(string $origin, string $dest, string $expected)
    {
        $p = new Path($origin);

        $p2 = $p->pointingTo(new RelativePath($dest));
        $this->assertSame($origin, $p->toString());
        $this->assertSame($expected, $p2->toString());
    }

    public function testClean()
    {
        $this->assertSame(
            '/path/to/content/',
            (new Path('/path/to/content/'))->clean()->toString(),
        );
        $this->assertSame(
            '/path/to/content/',
            (new Path('/path/to/content/?query=foo'))->clean()->toString(),
        );
        $this->assertSame(
            '/path/to/content/',
            (new Path('/path/to/content/#fragment'))->clean()->toString(),
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
