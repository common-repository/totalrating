<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Exceptions;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Contracts\Support\Arrayable;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;

/**
 * Class Exception
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Exceptions
 */
class Exception extends \Exception implements Arrayable
{
    /**
     * Default Code
     */
    const CODE = 500;

    /**
     * @var Collection<string>
     */
    protected $errors = [];

    /**
     * Exception constructor.
     *
     * @param string $message
     * @param int|null $code
     * @param array $errors
     */
    public function __construct(string $message = '', array $errors = [], $code = null)
    {
        parent::__construct(htmlspecialchars($message, ENT_QUOTES), $code ?? static::CODE, null);
        $this->errors = Collection::create($errors);
    }

    /**
     * @param $message
     *
     * @return Exception|false
     */
    public static function createFromContext($message = null): Exception
    {
        $error = error_get_last();

        if ($error !== null) {
            return new static($message ?: $error['message'], $error['type'], debug_backtrace());
        }

        return false;
    }

    /**
     * @return Collection
     */
    public function getErrors(): Collection
    {
        return $this->errors;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'file' => $this->file,
            'line' => $this->line,
            'trace' => $this->getTrace(),
        ];
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param string $message
     * @param int $code
     * @param array $errors
     *
     * @throws static
     */
    public static function throw(string $message = '', array $errors = [], $code = null)
    {
        throw new static($message, $errors, $code);
    }

    /**
     * @param boolean $condition
     * @param string $message
     * @param int $code
     * @param array $errors
     *
     * @throws Exception
     */
    public static function throwIf($condition, string $message = '', array $errors = [], $code = null)
    {
        $condition && static::throw($message, $errors, $code);
    }

    /**
     * @param boolean $condition
     * @param string $message
     * @param int $code
     * @param array $errors
     *
     * @throws Exception
     */
    public static function throwUnless($condition, string $message = '', array $errors = [], $code = null)
    {
        static::throwIf(!$condition, $message, $errors, $code);
    }
}
