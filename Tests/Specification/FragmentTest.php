<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Tests\Specification;

use Innmind\UrlResolver\{
    Specification\Fragment,
    Url
};

class FragmentTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSatisfiedBy()
    {
        $s = new Fragment;

        $this->assertTrue($s->isSatisfiedBy(new Url('#fragment')));
        $this->assertFalse($s->isSatisfiedBy(new Url('?foo')));
        $this->assertFalse($s->isSatisfiedBy(new Url('?foo=bar#blabla')));
    }
}
