<?php

declare(strict_types=1);

namespace Quanta\Http;

final class UrlPatternMatchingResult
{
    /**
     * @var bool
     */
    private bool $success;

    /**
     * @var string
     */
    private string $path;

    /**
     * @var string
     */
    private string $error;

    /**
     * @param string $path
     * @return \Quanta\Http\UrlPatternMatchingResult
     */
    public static function success(string $path): self
    {
        return new self(true, $path);
    }

    /**
     * @return \Quanta\Http\UrlPatternMatchingResult
     */
    public static function failure(): self
    {
        $error = 'Route \'%s\' does not match the given placeholders';

        return new self(false, '', $error);
    }

    /**
     * @param string $key
     * @return \Quanta\Http\UrlPatternMatchingResult
     */
    public static function placeholderCastingError(string $key): self
    {
        $error = sprintf('Value given for placeholder \'%s\' of route \'%%s\' can\'t be casted as string', $key);

        return new self(false, '', $error);
    }

    /**
     * @param string $key
     * @param string $regex
     * @param string $placeholder
     * @return \Quanta\Http\UrlPatternMatchingResult
     */
    public static function placeholderFormatError(string $key, string $regex, string $placeholder): self
    {
        $error = vsprintf('Value given for placeholder \'%s\' of route \'%%s\' must match \'%s\', \'%s\' given', [
            $key,
            $regex,
            $placeholder,
        ]);

        return new self(false, '', $error);
    }

    /**
     * @param bool      $success
     * @param string    $path
     * @param string    $error
     */
    private function __construct(bool $success, string $path = '', string $error = '')
    {
        $this->success = $success;
        $this->path = $path;
        $this->error = $error;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return string
     * @throws \LogicException
     */
    public function path(): string
    {
        if ($this->isSuccess()) {
            return $this->path;
        }

        throw new \LogicException('UrlPatternMatchingResul::error() has no path');
    }

    /**
     * @param string $name
     * @return string
     * @throws \LogicException
     */
    public function error(string $name): string
    {
        if (!$this->isSuccess()) {
            return sprintf($this->error, $name);
        }

        throw new \LogicException('UrlPatternMatchingResult::success() has no error');
    }
}
