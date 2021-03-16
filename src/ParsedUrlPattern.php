<?php

declare(strict_types=1);

namespace Quanta\Http;

final class ParsedUrlPattern
{
    /**
     * @var \Quanta\Http\UrlVariant[]
     */
    private array $variants;

    /**
     * @param \Quanta\Http\UrlVariant $variant
     * @param \Quanta\Http\UrlVariant ...$variants
     */
    public function __construct(UrlVariant $variant, UrlVariant ...$variants)
    {
        $variants = [$variant, ...$variants];

        usort($variants, [$this, 'compare']);

        $this->variants = $variants;
    }

    /**
     * @param array<mixed> $placeholders
     * @return \Quanta\Http\UrlPatternMatchingResult
     */
    public function result(array $placeholders): UrlPatternMatchingResult
    {
        foreach ($this->variants as $variant) {
            $result = $variant->result($placeholders);

            if (!is_null($result)) {
                return $result;
            }
        }

        return UrlPatternMatchingResult::failure();
    }

    /**
     * @param \Quanta\Http\UrlVariant $a
     * @param \Quanta\Http\UrlVariant $b
     * @return int
     */
    private function compare(UrlVariant $a, UrlVariant $b): int
    {
        return count($b->expected()) - count($a->expected());
    }
}
