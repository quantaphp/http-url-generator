<?php

declare(strict_types=1);

use Quanta\Http\UrlVariant;
use Quanta\Http\ParsedUrlPattern;
use Quanta\Http\UrlPatternMatchingResult;

describe('ParsedUrlPattern', function () {

    beforeEach(function () {
        $variant1 = UrlVariant::start()
            ->withConstant('/part1/')
            ->withPlaceholder('id1')
            ->withConstant('/part2');

        $variant2 = UrlVariant::start()
            ->withConstant('/part1/')
            ->withPlaceholder('id1')
            ->withConstant('/part2/')
            ->withPlaceholder('id2', '[0-9]+');

        $this->parsed = new ParsedUrlPattern($variant1, $variant2);
    });

    describe('->result()', function () {

        context('when no variant is matching the given placeholder array', function () {

            it('should return a failed result', function () {
                $test = $this->parsed->result(['id2' => 1, 'key' => 'test']);

                expect($test)->toEqual(UrlPatternMatchingResult::failure());
            });

        });

        context('when only one variant is matching the given placeholder array', function () {

            it('should return a successful result', function () {
                $test = $this->parsed->result(['id1' => 'anything', 'key' => 'test']);

                expect($test)->toEqual(UrlPatternMatchingResult::success('/part1/anything/part2'));
            });

        });

        context('when many variants are matching the given placeholder array', function () {

            it('should return a successful result with the variant using the most placeholders', function () {
                $test = $this->parsed->result(['id1' => 'anything', 'id2' => 2, 'key' => 'test']);

                expect($test)->toEqual(UrlPatternMatchingResult::success('/part1/anything/part2/2'));
            });

        });

        context('when a placeholder value can\'t be casted as string', function () {

            it('should return a placeholderCastingError', function () {
                $test = $this->parsed->result(['id1' => new class {}, 'id2' => 2, 'key' => 'test']);

                expect($test)->toEqual(UrlPatternMatchingResult::placeholderCastingError('id1'));
            });

        });

        context('when a placeholder value does not match the associated regex', function () {

            it('should return a placeholderFormatError', function () {
                $test = $this->parsed->result(['id1' => 'anything', 'id2' => 'test', 'key' => 'test']);

                expect($test)->toEqual(UrlPatternMatchingResult::placeholderFormatError('id2', '[0-9]+', 'test'));
            });

        });

    });

});
