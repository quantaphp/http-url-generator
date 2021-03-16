<?php

declare(strict_types=1);

namespace Quanta\Http;

final class UrlVariant
{
    const CONSTANT = 0;

    const PLACEHOLDER = 1;

    /**
     * @var array<int, string>
     */
    private array $expected;

    /**
     * @var array<int, array>
     */
    private array $parts;

    /**
     * @return \Quanta\Http\UrlVariant
     */
    public static function start(): self
    {
        return new self;
    }

    /**
     * @param array<int, string>    $expected
     * @param array<int, array>     $parts
     */
    private function __construct(array $expected = [], array $parts = [])
    {
        $this->expected = $expected;
        $this->parts = $parts;
    }

    /**
     * @return array<int, string>
     */
    public function expected(): array
    {
        return $this->expected;
    }

    /**
     * @param string $part
     * @return \Quanta\Http\UrlVariant
     */
    public function withConstant(string $part): self
    {
        return new self($this->expected, [...$this->parts, [
            'type' => self::CONSTANT,
            'value' => $part,
        ]]);
    }

    /**
     * @param string $key
     * @param string $regex
     * @return \Quanta\Http\UrlVariant
     */
    public function withPlaceholder(string $key, string $regex = '[^/]+'): self
    {
        return new self(array_unique([...$this->expected, $key]), [...$this->parts, [
            'type' => self::PLACEHOLDER,
            'key' => $key,
            'regex' => $regex,
        ]]);
    }

    /**
     * @param array<mixed> $placeholders
     * @return \Quanta\Http\UrlPatternMatchingResult|null
     */
    public function result(array $placeholders): ?UrlPatternMatchingResult
    {
        $matching = array_intersect(array_keys($placeholders), $this->expected);

        if (count($matching) == count($this->expected)) {
            return $this->path('', $this->parts, $placeholders);
        }

        return null;
    }

    /**
     * @param string            $path
     * @param array<int, array> $parts
     * @param array<mixed>      $placeholders
     * @return \Quanta\Http\UrlPatternMatchingResult
     */
    private function path(string $path, array $parts, array $placeholders): UrlPatternMatchingResult
    {
        if (count($parts) == 0) {
            return UrlPatternMatchingResult::success($path);
        }

        /** @var array<mixed> */
        $head = array_shift($parts);

        if ($head['type'] == self::CONSTANT) {
            return $this->path($path . $head['value'], $parts, $placeholders);
        }

        ['key' => $key, 'regex' => $regex] = $head;

        try {
            $placeholder = strval($placeholders[$key]);
        }

        catch (\Throwable $e) {
            return UrlPatternMatchingResult::placeholderCastingError($key);
        }

        if (preg_match('~^' . $regex . '$~', $placeholder) !== 0) {
            return $this->path($path . $placeholder, $parts, $placeholders);
        }

        return UrlPatternMatchingResult::placeholderFormatError($key, $regex, $placeholder);
    }
}
