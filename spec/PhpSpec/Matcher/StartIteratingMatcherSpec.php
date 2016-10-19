<?php

namespace spec\PhpSpec\Matcher;

use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Formatter\Presenter\Presenter;
use PhpSpec\Matcher\Matcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class StartIteratingMatcherSpec extends ObjectBehavior
{
    function let(Presenter $presenter)
    {
        $presenter->presentValue(Argument::any())->will(function ($subject) {
            return '"' . $subject[0] . '"';
        });

        $this->beConstructedWith($presenter);
    }

    function it_is_a_matcher()
    {
        $this->shouldBeAnInstanceOf(Matcher::class);
    }

    function it_responds_to_startIterating()
    {
        $this->supports('startIterating', [], [[]])->shouldReturn(true);

        $this->supports('startIterating', new \ArrayObject([]), [[]])->shouldReturn(true);
        $this->supports('startIterating', new \ArrayIterator([]), [[]])->shouldReturn(true);
        $this->supports('startIterating', $this->createGeneratorReturningArray([]), [[]])->shouldReturn(true);

        $this->supports('startIterating', [], [new \ArrayIterator([])])->shouldReturn(true);
        $this->supports('startIterating', [], [new \ArrayObject([])])->shouldReturn(true);
        $this->supports('startIterating', [], [$this->createGeneratorReturningArray([])])->shouldReturn(true);
    }

    function it_positive_matches_generator_while_starting_iterating_the_same()
    {
        $this
            ->shouldNotThrow()
            ->during('positiveMatch', [
                'startIterating',
                $this->createGeneratorReturningArray(['a' => 'b', 'c' => 'd']),
                [['a' => 'b']],
            ])
        ;

        $this
            ->shouldNotThrow()
            ->during('positiveMatch', [
                'startIterating',
                $this->createGeneratorReturningArray(['a' => 'b', 'c' => 'd']),
                [$this->createGeneratorReturningArray(['a' => 'b'])],
            ])
        ;
    }

    function it_positive_matches_infitite_generator_while_starting_iterating_the_same()
    {
        $this
            ->shouldNotThrow()
            ->during('positiveMatch', [
                'startIterating',
                $this->createInfiniteGenerator(),
                [[0 => 0, 1 => 1]]
            ])
        ;
    }

    function it_does_not_positive_match_generator_while_not_starting_iterating_the_same()
    {
        $this
            ->shouldThrow(new FailureException('Expected subject to have element #1 with key "c" and value "e", but got key "c" and value "d".'))
            ->during('positiveMatch', [
                'startIterating',
                $this->createGeneratorReturningArray(['a' => 'b', 'c' => 'd']),
                [['a' => 'b', 'c' => 'e']],
            ])
        ;
    }

    function it_negative_matches_generator_while_not_starting_iterating_the_same()
    {
        $this
            ->shouldNotThrow()
            ->during('negativeMatch', [
                'startIterating',
                $this->createGeneratorReturningArray(['a' => 'b', 'c' => 'd']),
                [['a' => 'b', 'c' => 'e']],
            ])
        ;

        $this
            ->shouldNotThrow()
            ->during('negativeMatch', [
                'startIterating',
                $this->createGeneratorReturningArray(['a' => 'b', 'c' => 'd']),
                [$this->createGeneratorReturningArray(['a' => 'b', 'c' => 'e'])],
            ])
        ;
    }

    function it_negative_matches_infinite_generator_while_not_starting_iterating_the_same()
    {
        $this
            ->shouldNotThrow()
            ->during('negativeMatch', [
                'startIterating',
                $this->createInfiniteGenerator(),
                [[0 => 0, 1 => 1, 3 => 3]],
            ])
        ;
    }

    function it_does_not_negative_matches_generator_while_starting_iterating_the_same()
    {
        $this
            ->shouldThrow(FailureException::class)
            ->during('negativeMatch', [
                'startIterating',
                $this->createGeneratorReturningArray(['a' => 'b', 'c' => 'd']),
                [['a' => 'b']],
            ])
        ;

        $this
            ->shouldThrow(FailureException::class)
            ->during('negativeMatch', [
                'startIterating',
                $this->createInfiniteGenerator(),
                [[0 => 0, 1 => 1]],
            ])
        ;
    }

    /**
     * @param array $array
     *
     * @return \Generator
     */
    private function createGeneratorReturningArray(array $array) {
        foreach ($array as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * @return \Generator
     */
    private function createInfiniteGenerator() {
        for ($i = 0; true; ++$i) {
            yield $i => $i;
        }
    }
}
