<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Exceptions;

use Exception;

class TextTranslationException extends Exception
{
    /**
     * @internal
     */
    public function __construct(string $message = 'An error occurred during translation', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
