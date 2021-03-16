<?php

declare(strict_types=1);

namespace Quanta\Http;

interface UrlPatternParserInterface
{
    /**
     * @param string $pattern
     * @return \Quanta\Http\ParsedUrlPattern
     */
    public function parsed(string $pattern): ParsedUrlPattern;
}
