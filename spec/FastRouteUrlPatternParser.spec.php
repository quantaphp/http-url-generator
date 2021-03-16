<?php

declare(strict_types=1);

use FastRoute\RouteParser\Std;

use Quanta\Http\UrlVariant;
use Quanta\Http\ParsedUrlPattern;
use Quanta\Http\UrlPatternParserInterface;
use Quanta\Http\FastRouteUrlPatternParser;

describe('FastRouteUrlPatternParser', function () {

    beforeEach(function () {
        $this->parser = new FastRouteUrlPatternParser(new Std);
    });

    it('should implement UrlPatternParserInterface', function () {
        expect($this->parser)->toBeAnInstanceOf(UrlPatternParserInterface::class);
    });

    describe('->parsed()', function () {

        context('when the given url pattern is empty', function () {

            it('should return a ParsedUrlPattern with an UrlVariant containing an empty constant part', function () {

                $test = $this->parser->parsed('');

                $variant = UrlVariant::start()->withConstant('');

                expect($test)->toEqual(new ParsedUrlPattern($variant));

            });

        });

        context('when the given url pattern is constant', function () {

            it('should return a ParsedUrlPattern with an UrlVariant containing the constant part', function () {

                $test = $this->parser->parsed('/path');

                $variant = UrlVariant::start()->withConstant('/path');

                expect($test)->toEqual(new ParsedUrlPattern($variant));

            });

        });

        context('when the given url pattern is a placeholder without a regex', function () {

            it('should return a ParsedUrlPattern with an UrlVariant containing the placeholder', function () {

                $test = $this->parser->parsed('{id}');

                $variant = UrlVariant::start()->withPlaceholder('id', '[^/]+');

                expect($test)->toEqual(new ParsedUrlPattern($variant));

            });

        });

        context('when the given url pattern is a placeholder with a regex', function () {

            it('should return a ParsedUrlPattern with an UrlVariant containing the placeholder', function () {

                $test = $this->parser->parsed('{id:[0-9]+}');

                $variant = UrlVariant::start()->withPlaceholder('id', '[0-9]+');

                expect($test)->toEqual(new ParsedUrlPattern($variant));

            });

        });

        context('when the given url pattern has many parts', function () {

            it('should return a ParsedUrlPattern with an UrlVariant containing the parts', function () {

                $test = $this->parser->parsed('/path1/{id1:[0-9]+}/path2/{id2}/path3');

                $variant = UrlVariant::start()
                    ->withConstant('/path1/')
                    ->withPlaceholder('id1', '[0-9]+')
                    ->withConstant('/path2/')
                    ->withPlaceholder('id2', '[^/]+')
                    ->withConstant('/path3');

                expect($test)->toEqual(new ParsedUrlPattern($variant));

            });

        });

        context('when the given url pattern has optional parts', function () {

            it('should return a ParsedUrlPattern with many UrlVariant', function () {

                $test = $this->parser->parsed('/path1/{id1:[0-9]+}/path2[/{id2}[/path3]]');

                $variant1 = UrlVariant::start()
                    ->withConstant('/path1/')
                    ->withPlaceholder('id1', '[0-9]+')
                    ->withConstant('/path2');

                $variant2 = UrlVariant::start()
                    ->withConstant('/path1/')
                    ->withPlaceholder('id1', '[0-9]+')
                    ->withConstant('/path2/')
                    ->withPlaceholder('id2', '[^/]+');

                $variant3 = UrlVariant::start()
                    ->withConstant('/path1/')
                    ->withPlaceholder('id1', '[0-9]+')
                    ->withConstant('/path2/')
                    ->withPlaceholder('id2', '[^/]+')
                    ->withConstant('/path3');

                expect($test)->toEqual(new ParsedUrlPattern($variant1, $variant2, $variant3));

            });

        });

    });

});
