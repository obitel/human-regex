<?php

namespace Mpociot\HumanRegex\Test;

use Mpociot\HumanRegex\CharacterSet;
use Mpociot\HumanRegex\HumanRegex;

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    /** @var HumanRegex */
    protected $regex;

    public function setUp()
    {
        $this->regex = new HumanRegex();
    }

    /** @test */
    public function it_can_find_strings_using_find()
    {
        $this->regex->find('foo');

        $this->assertTrue($this->regex->matches('foo bar'));
        $this->assertTrue($this->regex->matches('bar foo'));
        $this->assertFalse($this->regex->matches('bar baz'));
        $this->assertFalse($this->regex->matches('bar Foo'));
    }

    /** @test */
    public function it_can_find_strings_using_find_with_closure()
    {
        $this->regex->find(function($regex){
            return $regex->find('foo');
        });

        $this->assertTrue($this->regex->matches('foo bar'));
        $this->assertTrue($this->regex->matches('bar foo'));
        $this->assertFalse($this->regex->matches('bar baz'));
        $this->assertFalse($this->regex->matches('bar Foo'));
    }

    /** @test */
    public function it_can_find_strings_using_then()
    {
        $this->regex->then('foo');

        $this->assertTrue($this->regex->matches('foo bar'));
        $this->assertTrue($this->regex->matches('bar foo'));
        $this->assertFalse($this->regex->matches('bar baz'));
        $this->assertFalse($this->regex->matches('bar Foo'));
    }

    /** @test */
    public function it_aliases_then()
    {
        $this->regex->find('foo')->then(' bar');

        $this->assertTrue($this->regex->matches('foo bar'));
        $this->assertFalse($this->regex->matches('foobar'));
    }

    /** @test */
    public function it_matches_at_start_of_string()
    {
        $this->regex
            ->startOfString()
            ->find('foo');

        $this->assertTrue($this->regex->matches('foo bar'));
        $this->assertFalse($this->regex->matches('bar foo'));
    }

    /** @test */
    public function it_matches_an_optional_string()
    {
        $this->regex
            ->startOfString()
            ->find('foo')
            ->find('bar')->optional();

        $this->assertTrue($this->regex->matches('foobaz'));
        $this->assertTrue($this->regex->matches('foobar'));
    }

    /** @test */
    public function it_matches_single_whitespace()
    {
        $this->regex
            ->startOfString()
            ->find('foo')
            ->whitespace()
            ->find('bar');

        $this->assertTrue($this->regex->matches('foo bar'));
        $this->assertFalse($this->regex->matches('foo   bar'));
    }

    /** @test */
    public function it_matches_multiple_whitespaces()
    {
        $this->regex
            ->startOfString()
            ->find('foo')
            ->whitespaces()
            ->find('bar');

        $this->assertTrue($this->regex->matches('foo bar'));
        $this->assertTrue($this->regex->matches('foo   bar'));
    }

    /** @test */
    public function it_can_maybe_find_a_string()
    {
        $this->regex
            ->startOfString()
            ->find('foo')
            ->whitespace()
            ->maybe('bar')
            ->endOfString();

        $this->assertTrue($this->regex->matches('foo '));
        $this->assertTrue($this->regex->matches('foo bar'));
        $this->assertFalse($this->regex->matches('foo baz'));
        $this->assertFalse($this->regex->matches('bar foo'));
    }

    /** @test */
    public function it_can_find_one_or_another_string_multiple_times()
    {
        $this->regex
            ->findEither('foo')->multipleTimes()
            ->or('bar')->multipleTimes();

        $this->assertTrue($this->regex->matches('foofoo'));
        $this->assertTrue($this->regex->matches('barbar'));
        $this->assertFalse($this->regex->matches('No'));
    }

    /** @test */
    public function it_can_find_one_or_another_string()
    {
        $this->regex
            ->findEither('foo')
            ->or('baz')
            ->or('bar');

        $this->assertTrue($this->regex->matches('foo'));
        $this->assertTrue($this->regex->matches('bar'));
        $this->assertTrue($this->regex->matches('baz'));
        $this->assertFalse($this->regex->matches('No'));
    }

    /** @test */
    public function it_can_use_or_without_either()
    {
        $this->regex
            ->find('foo')
            ->or('baz')
            ->or('bar');

        $this->assertTrue($this->regex->matches('foo'));
        $this->assertTrue($this->regex->matches('bar'));
        $this->assertTrue($this->regex->matches('baz'));
        $this->assertFalse($this->regex->matches('No'));
    }

    /** @test */
    public function it_can_maybe_find_a_string_with_closure()
    {
        $this->regex
            ->startOfString()
            ->find('foo')
            ->maybe(function($regex){
                return $regex->whitespace()
                    ->find('bar');
            })
            ->endOfString();

        $this->assertTrue($this->regex->matches('foo'));
        $this->assertTrue($this->regex->matches('foo bar'));
        $this->assertFalse($this->regex->matches('foofoo bar'));
        $this->assertFalse($this->regex->matches('foo baz'));
        $this->assertFalse($this->regex->matches('bar foo'));
    }

    /** @test */
    public function it_can_find_a_string_multiple_times()
    {
        $this->regex
            ->startOfString()
            ->find('foo')->multipleTimes();

        $this->assertTrue($this->regex->matches('foo'));
        $this->assertTrue($this->regex->matches('foofoo bar'));
        $this->assertFalse($this->regex->matches('bar foo'));
    }

    /** @test */
    public function it_matches_at_end_of_string()
    {
        $this->regex
            ->endOfString()
            ->find('foo');

        $this->assertFalse($this->regex->matches('foo bar'));
        $this->assertTrue($this->regex->matches('bar foo'));
    }

    /** @test */
    public function it_matches_a_line_break()
    {
        $this->regex
            ->find('foo')
            ->lineBreak();

        $this->assertFalse($this->regex->matches('foo'));
        $this->assertTrue($this->regex->matches('foo'.PHP_EOL));
    }

    /** @test */
    public function it_matches_a_string_once()
    {
        $this->regex
            ->find('f')
            ->digit()
            ->then('_');

        $this->assertTrue($this->regex->matches('f1_'));
        $this->assertFalse($this->regex->matches('f12_'));
    }

    /** @test */
    public function it_matches_a_string_on_a_given_limit()
    {
        $this->regex
            ->find('f')
            ->digits()->limit(1,2)
            ->then('_');

        $this->assertTrue($this->regex->matches('f1_'));
        $this->assertTrue($this->regex->matches('f12_'));
        $this->assertFalse($this->regex->matches('f132_'));
    }

    /** @test */
    public function it_matches_a_string_at_least()
    {
        $this->regex
            ->find('f')
            ->digits()->atLeast(2)
            ->then('_');

        $this->assertFalse($this->regex->matches('f1_'));
        $this->assertTrue($this->regex->matches('f12_'));
        $this->assertTrue($this->regex->matches('f132_'));
    }

    /** @test */
    public function it_matches_a_line_break_using_shorthand()
    {
        $this->regex
            ->find('foo')
            ->br();

        $this->assertFalse($this->regex->matches('foo'));
        $this->assertTrue($this->regex->matches('foo'.PHP_EOL));
    }

    /** @test */
    public function it_matches_a_single_digit()
    {
        $this->regex
            ->find('f')
            ->digit();

        $this->assertTrue($this->regex->matches('f1'));
        $matches = $this->regex->findMatches('f12');
        $this->assertSame('f1', $matches[0]);
    }

    /** @test */
    public function it_matches_multiple_digits()
    {
        $this->regex
            ->find('f')
            ->digits();

        $this->assertTrue($this->regex->matches('f1234'));

        $matches = $this->regex->findMatches('f1234');
        $this->assertSame('f1234', $matches[0]);
    }

    /** @test */
    public function it_matches_a_single_letter()
    {
        $this->regex
            ->find('f')
            ->letter();

        $this->assertTrue($this->regex->matches('fo'));

        $matches = $this->regex->findMatches('foo');
        $this->assertSame('fo', $matches[0]);
    }

    /** @test */
    public function it_matches_multiple_letters()
    {
        $this->regex
            ->find('f')
            ->letters();

        $this->assertTrue($this->regex->matches('fo'));

        $matches = $this->regex->findMatches('foo');
        $this->assertSame('foo', $matches[0]);
    }

    /** @test */
    public function it_matches_a_single_alphanumeric()
    {
        $this->regex
            ->find('f')
            ->alphanumeric();

        $this->assertTrue($this->regex->matches('fo'));
        $this->assertTrue($this->regex->matches('f0'));

        $matches = $this->regex->findMatches('fo0');
        $this->assertSame('fo', $matches[0]);
    }

    /** @test */
    public function it_matches_multiple_alphanumeric()
    {
        $this->regex
            ->find('f')
            ->alphanumerics();

        $this->assertTrue($this->regex->matches('fo'));

        $matches = $this->regex->findMatches('fo0');
        $this->assertSame('fo0', $matches[0]);
    }

    /** @test */
    public function it_matches_once()
    {
        $this->regex
            ->find('f')->once();

        $this->assertTrue($this->regex->matches('f'));

        $matches = $this->regex->findMatches('fffff');
        $this->assertSame('f', $matches[0]);
    }

    /** @test */
    public function it_can_negate_expressions()
    {
        $this->regex
            ->startOfString()
            ->not(function(HumanRegex $r){
                return $r->find('foo');
            })
            ->anything()
            ->then('-')
            ->digits()->exactly(4)
            ->then('-')
            ->digits()->exactly(2)
            ->then('-')
            ->digits()->exactly(2)
            ->then('.mov')
            ->endOfString();

        $this->assertFalse($this->regex->matches('foo-2016-04-23.mov'));
        $this->assertTrue($this->regex->matches('bar-2016-04-23.mov'));
    }

    /** @test */
    public function it_can_use_regex_objects()
    {
        $fooBarRegex = HumanRegex::create()->findEither('foo')->or('bar');

        $this->regex
            ->startOfString()
            ->then($fooBarRegex)
            ->endOfString();

        $this->assertTrue($this->regex->matches('foo'));
        $this->assertTrue($this->regex->matches('bar'));
        $this->assertFalse($this->regex->matches('baz'));
    }

    /** @test */
    public function it_can_add_capture_groups()
    {
        $this->regex
            ->capture(function($regex){
                return $regex->find('foo');
            });

        $this->assertTrue($this->regex->matches('foo bar'));
        $this->assertTrue($this->regex->matches('bar foo'));
        $this->assertFalse($this->regex->matches('bar baz'));

        $matches = $this->regex->findMatches('bar foo bar');

        $this->assertCount(2, $matches);
        $this->assertSame('foo', $matches[0]);
        $this->assertSame('foo', $matches[1]);
    }

    /** @test */
    public function it_can_add_multiple_capture_groups()
    {
        $this->regex
            ->startOfString()
            ->capture(function($regex){
                return $regex->find('foo');
            })
            ->anything()
            ->capture(function($regex){
                return $regex->find('bar');
            });

        $this->assertTrue($this->regex->matches('foo bar'));
        $this->assertTrue($this->regex->matches('foo something bar'));
        $this->assertFalse($this->regex->matches('bar foo'));
        $this->assertFalse($this->regex->matches('bar baz'));

        $matches = $this->regex->findMatches('foo something bar');

        $this->assertCount(3, $matches);
        $this->assertSame('foo something bar', $matches[0]);
        $this->assertSame('foo', $matches[1]);
        $this->assertSame('bar', $matches[2]);
    }

}