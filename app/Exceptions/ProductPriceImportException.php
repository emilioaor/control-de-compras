<?php

namespace App\Exceptions;

use Exception;

class ProductPriceImportException extends Exception
{
    protected $errors = [];
    protected $fileLine;

    /**
     * DigitalInventoryException constructor.
     * @param array $errors
     * @param int $line
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(array $errors, $line, $message = "", $code = 0, \Throwable $previous = null)
    {
        $this->errors = $errors;
        $this->fileLine = $line;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return int
     */
    public function getFileLine(): int
    {
        return $this->fileLine;
    }
}
