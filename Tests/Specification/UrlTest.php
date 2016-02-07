<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver\Tests\Specification;

use Innmind\UrlResolver\Specification\Url as UrlSpecification;
use Innmind\UrlResolver\Url;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSatisfiedBy()
    {
        $s = new UrlSpecification(['http', 'https']);

        $this->assertTrue($s->isSatisfiedBy(new Url('http://localhost')));
        $this->assertTrue($s->isSatisfiedBy(new Url('https://localhost')));
        $this->assertFalse($s->isSatisfiedBy(new Url('ftp://localhost')));
        $this->assertFalse($s->isSatisfiedBy(new Url('unknown://localhost')));

        $s = new UrlSpecification;

        $this->assertTrue($s->isSatisfiedBy(new Url('http://localhost')));
        $this->assertTrue($s->isSatisfiedBy(new Url('https://localhost')));
        $this->assertTrue($s->isSatisfiedBy(new Url('ftp://localhost')));
        $this->assertTrue($s->isSatisfiedBy(new Url('unknown://localhost')));
        $this->assertFalse($s->isSatisfiedBy(new Url('localhost')));
        $this->assertFalse($s->isSatisfiedBy(new Url('//localhost')));
    }
}
