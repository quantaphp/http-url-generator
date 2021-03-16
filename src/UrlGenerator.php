<?php

declare(strict_types=1);

namespace Quanta\Http;

final class UrlGenerator
{
    /**
     * @var \Quanta\Http\UrlPatternParserInterface
     */
    private UrlPatternParserInterface $parser;

    /**
     * @var array<string, null|array{0: string, 1?:\Quanta\Http\ParsedUrlPattern}>
     */
    private array $map;

    /**
     * @param \Quanta\Http\UrlPatternParserInterface $parser
     */
    public function __construct(UrlPatternParserInterface $parser)
    {
        $this->parser = $parser;
        $this->map = [];
    }

    /**
     * @param string $name
     * @param string $pattern
     * @return void
     */
    public function register(string $name, string $pattern): void
    {
        $this->map[$name] = [$pattern];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->map);
    }

    /**
     * @param string        $name
     * @param array<mixed>  $placeholders
     * @param array<mixed>  $query
     * @param string        $fragment
     * @return string
     * @throws \LogicException
     */
    public function generate(string $name, array $placeholders = [], array $query = [], string $fragment = ''): string
    {
        $ref = &$this->map[$name];

        if (is_null($ref)) {
            throw new \LogicException(sprintf('Route \'%s\' not found', $name));
        }

        $pattern = $ref[0];

        try {
            $parsed = $ref[1] ?? $ref[1] = $this->parser->parsed($pattern);
        }

        catch (\Throwable $e) {
            throw new \LogicException(sprintf('Error while parsing url pattern associated with route \'%s\' (%s)', $name, $pattern), 0, $e);
        }

        $result = $parsed->result($placeholders);

        if ($result->isSuccess()) {
            return $result->path() . $this->query($query) . $this->fragment($fragment);
        }

        throw new \LogicException($result->error($name));
    }

    /**
     * @param array<mixed> $query
     * @return string
     */
    private function query(array $query): string
    {
        return count($query) > 0
            ? '?' . http_build_query($query)
            : '';
    }

    /**
     * @param string $fragment
     * @return string
     */
    private function fragment(string $fragment): string
    {
        return $fragment == '' ? '' : '#' . $fragment;
    }
}
