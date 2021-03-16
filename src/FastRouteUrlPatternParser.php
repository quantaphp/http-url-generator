<?php

declare(strict_types=1);

namespace Quanta\Http;

use FastRoute\RouteParser\Std;

final class FastRouteUrlPatternParser implements UrlPatternParserInterface
{
    /**
     * @var \FastRoute\RouteParser\Std
     */
    private Std $parser;

    /**
     * @param \FastRoute\RouteParser\Std $parser
     */
    public function __construct(Std $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @inheritdoc
     */
    public function parsed(string $pattern): ParsedUrlPattern
    {
        $variants = [];

        $parsed = $this->parser->parse($pattern);

        foreach ($parsed as $parts) {
            $variants[] = array_reduce($parts, [$this, 'reduced'], UrlVariant::start());
        }

        return new ParsedUrlPattern(...$variants);
    }

    /**
     * @param \Quanta\Http\UrlVariant   $variant
     * @param mixed                     $part
     * @return \Quanta\Http\UrlVariant
     * @throws \Exception
     */
    private function reduced(UrlVariant $variant, $part): UrlVariant
    {
        if (is_string($part)) {
            return $variant->withConstant($part);
        }

        if (is_array($part) && count($part) == 2) {
            return $variant->withPlaceholder(...$part);
        }

        throw new \Exception;
    }
}
