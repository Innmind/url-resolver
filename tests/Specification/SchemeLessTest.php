<?php
declare(strict_types = 1);

namespace Tests\Innmind\UrlResolver\Specification;

use Innmind\UrlResolver\{
    Specification\SchemeLess,
    Url
};

class SchemeLessTest extends \PHPUnit_Framework_TestCase
{
    public function testIsStatisfiedBy()
    {
        $s = new SchemeLess;

        $this->assertTrue($s->isSatisfiedBy(new Url('//localhost')));
        $this->assertTrue($s->isSatisfiedBy(new Url('localhost')));
        $this->assertFalse($s->isSatisfiedBy(new Url('http://localhost')));
        $this->assertFalse($s->isSatisfiedBy(new Url('ftp://localhost')));
        $this->assertFalse($s->isSatisfiedBy(new Url('unknown://localhost')));
    }
}
