<?php

declare(strict_types=1);

use Quanta\Http\UrlPatternMatchingResult;

describe('UrlPatternMatchingResult::success()', function () {

    it('should be an instance of UrlPatternMatchingResult', function () {
        $test = UrlPatternMatchingResult::success('/path');

        expect($test)->toBeAnInstanceOf(UrlPatternMatchingResult::class);
    });

});

describe('UrlPatternMatchingResult::failure()', function () {

    it('should be an instance of UrlPatternMatchingResult', function () {
        $this->result = UrlPatternMatchingResult::failure();

        expect($this->result)->toBeAnInstanceOf(UrlPatternMatchingResult::class);
    });

});

describe('UrlPatternMatchingResult::placeholderCastingError()', function () {

    it('should be an instance of UrlPatternMatchingResult', function () {
        $this->result = UrlPatternMatchingResult::placeholderCastingError('key');

        expect($this->result)->toBeAnInstanceOf(UrlPatternMatchingResult::class);
    });

});

describe('UrlPatternMatchingResult::placeholderFormatError()', function () {

    it('should be an instance of UrlPatternMatchingResult', function () {
        $this->result = UrlPatternMatchingResult::placeholderFormatError('key', 'regex', 'placeholder');

        expect($this->result)->toBeAnInstanceOf(UrlPatternMatchingResult::class);
    });

});

describe('UrlPatternMatchingResult', function () {

    context('when the result is a success', function () {

        beforeEach(function () {
            $this->result = UrlPatternMatchingResult::success('/path');
        });

        describe('->isSuccess()', function () {

            it('should return true', function () {
                $test = $this->result->isSuccess();

                expect($test)->toBeTruthy();
            });

        });

        describe('->path()', function () {

            it('should return the path', function () {
                $test = $this->result->path();

                expect($test)->toEqual('/path');
            });

        });

        describe('->error()', function () {

            it('should throw a LogicException', function () {
                $test = fn () => $this->result->error('name');

                expect($test)->toThrow(new LogicException);
            });

        });

    });

    context('when the result is a failure', function () {

        beforeEach(function () {
            $this->result = UrlPatternMatchingResult::failure();
        });

        describe('->isSuccess()', function () {

            it('should return false', function () {
                $test = $this->result->isSuccess();

                expect($test)->toBeFalsy();
            });

        });

        describe('->path()', function () {

            it('should throw a LogicException', function () {
                $test = fn () => $this->result->path();

                expect($test)->toThrow(new LogicException);
            });

        });

        describe('->error()', function () {

            it('should return a string', function () {
                $test = $this->result->error('name');

                expect($test)->toBeA('string');
            });

            it('should not throw', function () {
                $test = fn () => $this->result->error('name');

                expect($test)->not->toThrow();
            });

        });

    });

    context('when the result is a placeholderCastingError', function () {

        beforeEach(function () {
            $this->result = UrlPatternMatchingResult::placeholderCastingError('key');
        });

        describe('->isSuccess()', function () {

            it('should return false', function () {
                $test = $this->result->isSuccess();

                expect($test)->toBeFalsy();
            });

        });

        describe('->path()', function () {

            it('should throw a LogicException', function () {
                $test = fn () => $this->result->path();

                expect($test)->toThrow(new LogicException);
            });

        });

        describe('->error()', function () {

            it('should return a string', function () {
                $test = $this->result->error('name');

                expect($test)->toBeA('string');
            });

            it('should not throw', function () {
                $test = fn () => $this->result->error('name');

                expect($test)->not->toThrow();
            });

        });

    });

    context('when the result is a placeholderFormatError', function () {

        beforeEach(function () {
            $this->result = UrlPatternMatchingResult::placeholderFormatError('key', 'regex', 'placeholder');
        });

        describe('->isSuccess()', function () {

            it('should return false', function () {
                $test = $this->result->isSuccess();

                expect($test)->toBeFalsy();
            });

        });

        describe('->path()', function () {

            it('should throw a LogicException', function () {
                $test = fn () => $this->result->path();

                expect($test)->toThrow(new LogicException);
            });

        });

        describe('->error()', function () {

            it('should return a string', function () {
                $test = $this->result->error('name');

                expect($test)->toBeA('string');
            });

            it('should not throw', function () {
                $test = fn () => $this->result->error('name');

                expect($test)->not->toThrow();
            });

        });

    });

});
