<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Tests\Specification;

use Innmind\UrlResolver\{
    Specification\RelativePath,
    Url
};

class RelativePathTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSatisfiedBy()
    {
        $s = new RelativePath;

        $this->assertTrue($s->isSatisfiedBy(new Url('./path/to/content')));
        $this->assertTrue($s->isSatisfiedBy(new Url('../path/to/content')));
        $this->assertTrue($s->isSatisfiedBy(new Url('path/to/content')));
        $this->assertTrue($s->isSatisfiedBy(new Url('_path/to/content')));
        $this->assertTrue($s->isSatisfiedBy(new Url('.path/to/content')));
        $this->assertTrue($s->isSatisfiedBy(new Url('42path/to/content')));
        $this->assertTrue($s->isSatisfiedBy(new Url('!path/to/content')));
        $this->assertFalse($s->isSatisfiedBy(new Url('?foo')));
        $this->assertFalse($s->isSatisfiedBy(new Url('#blabla')));
    }
}
