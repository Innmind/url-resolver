<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver\Specification;

use Innmind\UrlResolver\{
    Specification\QueryString,
    Url
};

class QueryStringTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSatisfiedBy()
    {
        $s = new QueryString;

        $this->assertTrue($s->isSatisfiedBy(new Url('?foo')));
        $this->assertTrue($s->isSatisfiedBy(new Url('?foo=bar#blabla')));
        $this->assertFalse($s->isSatisfiedBy(new Url('#fragment')));
    }
}
