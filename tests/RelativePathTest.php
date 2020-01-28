<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver;

use Innmind\UrlResolver\{
    RelativePath,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class RelativePathTest extends TestCase
{
    public function testValidRelativePath()
    {
        $this->assertSame(
            '../path/to/content',
            (new RelativePath('../path/to/content'))->toString(),
        );
        $this->assertSame(
            './path/to/content',
            (new RelativePath('./path/to/content'))->toString(),
        );
        $this->assertSame(
            'path/to/content',
            (new RelativePath('path/to/content'))->toString(),
        );
        $this->assertSame(
            '.path/to/content',
            (new RelativePath('.path/to/content'))->toString(),
        );
        $this->assertSame(
            '!path/to/content',
            (new RelativePath('!path/to/content'))->toString(),
        );
        $this->assertSame(
            '42path/to/content',
            (new RelativePath('42path/to/content'))->toString(),
        );
    }

    public function testThrowWhenInvalidRelativePath()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('/relative/path');

        new RelativePath('/relative/path');
    }
}
