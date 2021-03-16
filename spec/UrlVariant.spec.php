<?php

declare(strict_types=1);

use Quanta\Http\UrlVariant;
use Quanta\Http\UrlPatternMatchingResult;

describe('UrlVariant::start()', function () {

    it('should be an instance of UrlVariant', function () {
        $test = UrlVariant::start();

        expect($test)->toBeAnInstanceOf(UrlVariant::class);
    });

});

describe('UrlVariant', function () {

    context('when the variant is empty', function () {

        beforeEach(function () {
            $this->variant = UrlVariant::start();
        });

        describe('->expected()', function () {

            it('should return an empty array', function () {
                $test = $this->variant->expected();

                expect($test)->toEqual([]);
            });

        });

        describe('->withConstant()', function () {

            it('should return a new variant', function () {
                $test = $this->variant->withConstant('/path');

                expect($test)->toBeAnInstanceOf(UrlVariant::class);
            });

        });

        describe('->withPlaceholder()', function () {

            context('when no regex is given', function () {

                it('should return a new variant', function () {
                    $test = $this->variant->withPlaceholder('key');

                    expect($test)->toBeAnInstanceOf(UrlVariant::class);
                });

            });

            context('when a regex is given', function () {

                it('should return a new variant', function () {
                    $test = $this->variant->withPlaceholder('key', '[0-9]+');

                    expect($test)->toBeAnInstanceOf(UrlVariant::class);
                });

            });

        });

        describe('->result()', function () {

            it('should return an empty success', function () {
                $test = $this->variant->result([]);

                expect($test)->toEqual(UrlPatternMatchingResult::success(''));
            });

        });

    });

    context('when the variant has one constant', function () {

        beforeEach(function () {
            $this->variant = UrlVariant::start()->withConstant('/path');
        });

        describe('->expected()', function () {

            it('should return an empty array', function () {
                $test = $this->variant->expected();

                expect($test)->toEqual([]);
            });

        });

        describe('->withConstant()', function () {

            it('should return a new variant', function () {
                $test = $this->variant->withConstant('/test');

                expect($test)->toBeAnInstanceOf(UrlVariant::class);
            });

        });

        describe('->withPlaceholder()', function () {

            it('should return a new variant', function () {
                $test = $this->variant->withPlaceholder('key', '[0-9]+');

                expect($test)->toBeAnInstanceOf(UrlVariant::class);
            });

        });

        describe('->result()', function () {

            it('should return a successful result containing the path', function () {
                $test = $this->variant->result([]);

                expect($test)->toEqual(UrlPatternMatchingResult::success('/path'));
            });

        });

    });

    context('when the variant has one placeholder', function () {

        beforeEach(function () {
            $this->variant = UrlVariant::start()->withPlaceholder('id', '[0-9]+');
        });

        describe('->expected()', function () {

            it('should return an array containing the placeholder key', function () {
                $test = $this->variant->expected();

                expect($test)->toEqual(['id']);
            });

        });

        describe('->withConstant()', function () {

            it('should return a new variant', function () {
                $test = $this->variant->withConstant('/test');

                expect($test)->toBeAnInstanceOf(UrlVariant::class);
            });

        });

        describe('->withPlaceholder()', function () {

            context('when no regex is given', function () {

                it('should return a new variant', function () {
                    $test = $this->variant->withPlaceholder('key');

                    expect($test)->toBeAnInstanceOf(UrlVariant::class);
                });

            });

            context('when a regex is given', function () {

                it('should return a new variant', function () {
                    $test = $this->variant->withPlaceholder('key', '[0-9]+');

                    expect($test)->toBeAnInstanceOf(UrlVariant::class);
                });

            });

        });

        describe('->result()', function () {

            context('when the given placeholder array does not contain the placeholder key', function () {

                it('should return null', function () {
                    $test = $this->variant->result(['key' => 'test']);

                    expect($test)->toBeNull();
                });

            });

            context('when the placeholder value can\'t be casted as string', function () {

                it('should return a placeholderCastingError result', function () {
                    $test = $this->variant->result(['id' => new class {}, 'key' => 'test']);

                    expect($test)->toEqual(UrlPatternMatchingResult::placeholderCastingError('id'));
                });

            });

            context('when the placeholder value does not match the associated regex', function () {

                it('should return a placeholderFormatError result', function () {
                    $test = $this->variant->result(['id' => 'test', 'key' => 'test']);

                    expect($test)->toEqual(UrlPatternMatchingResult::placeholderFormatError('id', '[0-9]+', 'test'));
                });

            });

            context('when the placeholder value is valid', function () {

                it('should return a successful result containing the value associated to the placeholder key', function () {
                    $test = $this->variant->result(['id' => 1, 'key' => 'test']);

                    expect($test)->toEqual(UrlPatternMatchingResult::success('1'));
                });

            });

        });

    });

    context('when the variant has many parts', function () {

        beforeEach(function () {
            $this->variant = UrlVariant::start()
                ->withConstant('/path1/')
                ->withPlaceholder('id1')
                ->withConstant('/path2/')
                ->withPlaceholder('id2', '[0-9]+');
        });

        describe('->expected()', function () {

            it('should return an array containing the placeholder keys', function () {
                $test = $this->variant->expected();

                expect($test)->toEqual(['id1', 'id2']);
            });

        });

        describe('->withConstant()', function () {

            it('should return a new variant', function () {
                $test = $this->variant->withConstant('/test');

                expect($test)->toBeAnInstanceOf(UrlVariant::class);
            });

        });

        describe('->withPlaceholder()', function () {

            context('when no regex is given', function () {

                it('should return a new variant', function () {
                    $test = $this->variant->withPlaceholder('key');

                    expect($test)->toBeAnInstanceOf(UrlVariant::class);
                });

            });

            context('when a regex is given', function () {

                it('should return a new variant', function () {
                    $test = $this->variant->withPlaceholder('key', '[0-9]+');

                    expect($test)->toBeAnInstanceOf(UrlVariant::class);
                });

            });

        });

        describe('->result()', function () {

            context('when the given placeholder array does not contain the placeholder key', function () {

                it('should return null', function () {
                    $test = $this->variant->result(['id1' => 'anything', 'key' => 'test']);

                    expect($test)->toBeNull();
                });

            });

            context('when the placeholder value can\'t be casted as string', function () {

                it('should return a placeholderCastingError result', function () {
                    $test = $this->variant->result(['id1' => new class {}, 'id2' => 'test', 'key' => 'test']);

                    expect($test)->toEqual(UrlPatternMatchingResult::placeholderCastingError('id1'));
                });

            });

            context('when the placeholder value does not match the associated regex', function () {

                it('should return a placeholderFormatError result', function () {
                    $test = $this->variant->result(['id1' => 'anything', 'id2' => 'test', 'key' => 'test']);

                    expect($test)->toEqual(UrlPatternMatchingResult::placeholderFormatError('id2', '[0-9]+', 'test'));
                });

            });

            context('when the placeholder value is valid', function () {

                it('should return a successful result containing the value associated to the placeholder key', function () {
                    $test = $this->variant->result(['id1' => 'anything', 'id2' => 2, 'key' => 'test']);

                    expect($test)->toEqual(UrlPatternMatchingResult::success('/path1/anything/path2/2'));
                });

            });

        });

    });

    context('when the variant has many placeholders with the same key', function () {

        beforeEach(function () {
            $this->variant = UrlVariant::start()
                ->withConstant('/path1/')
                ->withPlaceholder('id', '[0-9]+')
                ->withConstant('/path2/')
                ->withPlaceholder('id', '[0-9]+');
        });

        describe('->expected()', function () {

            it('should return an array containing the placeholder key', function () {
                $test = $this->variant->expected();

                expect($test)->toEqual(['id']);
            });

        });

        describe('->withConstant()', function () {

            it('should return a new variant', function () {
                $test = $this->variant->withConstant('/test');

                expect($test)->toBeAnInstanceOf(UrlVariant::class);
            });

        });

        describe('->withPlaceholder()', function () {

            context('when no regex is given', function () {

                it('should return a new variant', function () {
                    $test = $this->variant->withPlaceholder('key');

                    expect($test)->toBeAnInstanceOf(UrlVariant::class);
                });

            });

            context('when a regex is given', function () {

                it('should return a new variant', function () {
                    $test = $this->variant->withPlaceholder('key', '[0-9]+');

                    expect($test)->toBeAnInstanceOf(UrlVariant::class);
                });

            });

        });

        describe('->result()', function () {

            context('when the given placeholder array does not contain the placeholder key', function () {

                it('should return null', function () {
                    $test = $this->variant->result(['key' => 'test']);

                    expect($test)->toBeNull();
                });

            });

            context('when the placeholder value can\'t be casted as string', function () {

                it('should return a placeholderCastingError result', function () {
                    $test = $this->variant->result(['id' => new class {}, 'key' => 'test']);

                    expect($test)->toEqual(UrlPatternMatchingResult::placeholderCastingError('id'));
                });

            });

            context('when the placeholder value does not match the associated regex', function () {

                it('should return a placeholderFormatError result', function () {
                    $test = $this->variant->result(['id' => 'test', 'key' => 'test']);

                    expect($test)->toEqual(UrlPatternMatchingResult::placeholderFormatError('id', '[0-9]+', 'test'));
                });

            });

            context('when the placeholder value is valid', function () {

                it('should return a successful result containing the value associated to the placeholder key', function () {
                    $test = $this->variant->result(['id' => 1, 'key' => 'test']);

                    expect($test)->toEqual(UrlPatternMatchingResult::success('/path1/1/path2/1'));
                });

            });

        });

    });

});
