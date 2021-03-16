<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Http\UrlGenerator;
use Quanta\Http\UrlPatternParserInterface;

describe('UrlGenerator', function () {

    beforeEach(function () {
        $this->parser = mock(UrlPatternParserInterface::class);

        $this->generator = new UrlGenerator($this->parser->get());
    });

    describe('->register()', function () {

        it('should register the given route name and pattern', function () {
            $this->generator->register('name', '/pattern');

            $test = $this->generator->has('name');

            expect($test)->toBeTruthy();
        });

    });

    describe('->has()', function () {

        context('when the given route name is not registered', function () {

            it('should return false', function () {
                $test = $this->generator->has('name');

                expect($test)->toBeFalsy();
            });

        });

        context('when the given route name is registered', function () {

            it('should return true', function () {
                $this->generator->register('name', '/pattern');

                $test = $this->generator->has('name');

                expect($test)->toBeTruthy();
            });

        });

    });

    describe('->generate()', function () {

        context('when the given route name is not registered', function () {

            it('should throw a LogicException', function () {
                $test = fn () => $this->generator->generate('name');

                expect($test)->toThrow(new LogicException);
            });

        });

        context('when the parser throws an exception', function () {

            it('should throw a LogicException wrapped around the exception thrown by the parser', function () {
                $exception = new Exception;

                $this->parser->parsed->with('/pattern')->throws($exception);

                $this->generator->register('name', '/pattern');

                $test = fn () => $this->generator->generate('name');

                expect($test)->toThrow(new Exception);

                try {
                    $test();
                }

                catch (\Throwable $e) {
                    expect($e->getPrevious())->toBe($exception);
                }
            });

        });

    });

});
