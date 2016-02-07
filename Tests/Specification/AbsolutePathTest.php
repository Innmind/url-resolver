<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Tests\Specification;

use Innmind\UrlResolver\Specification\AbsolutePath;
use Innmind\UrlResolver\Url;

class AbsolutePathTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSatisfiedBy()
    {
        $s = new AbsolutePath;

        $this->assertTrue($s->isSatisfiedBy(new Url('/path/to/content')));
        $this->assertFalse($s->isSatisfiedBy(new Url('?foo')));
        $this->assertFalse($s->isSatisfiedBy(new Url('#blabla')));
    }
}
