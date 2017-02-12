<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver\Specification;

use Innmind\UrlResolver\{
    Specification\Fragment,
    Url
};
use PHPUnit\Framework\TestCase;

class FragmentTest extends TestCase
{
    public function testIsSatisfiedBy()
    {
        $s = new Fragment;

        $this->assertTrue($s->isSatisfiedBy(new Url('#fragment')));
        $this->assertFalse($s->isSatisfiedBy(new Url('?foo')));
        $this->assertFalse($s->isSatisfiedBy(new Url('?foo=bar#blabla')));
    }
}
