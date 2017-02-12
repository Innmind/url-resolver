<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver\Specification;

use Innmind\UrlResolver\{
    Specification\AbsolutePath,
    Url
};
use PHPUnit\Framework\TestCase;

class AbsolutePathTest extends TestCase
{
    public function testIsSatisfiedBy()
    {
        $s = new AbsolutePath;

        $this->assertTrue($s->isSatisfiedBy(new Url('/path/to/content')));
        $this->assertFalse($s->isSatisfiedBy(new Url('?foo')));
        $this->assertFalse($s->isSatisfiedBy(new Url('#blabla')));
    }
}
